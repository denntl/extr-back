<?php

namespace App\Console\Commands;

use App\Enums\PushNotification\Type;
use App\Jobs\OnesignalDelivery;
use App\Models\OnesignalTemplateSingleSettings;
use App\Services\Common\OnesignalTemplate\OnesignalDeliveryService;
use Illuminate\Console\Command;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled notifications';

    public function __construct(public OnesignalDeliveryService $deliveryService)
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $deliveryService = $this->deliveryService;
        $deliveryService->getSettingsToPushQuery(Type::Single)
            ->chunk(50, function ($batch) use ($deliveryService) {
                /** @var OnesignalTemplateSingleSettings $item */
                foreach ($batch as $item) {
                    $templates = $deliveryService->getAllTemplatesById($item->getAttribute('onesignal_template_id'), Type::Single);
                    foreach ($templates as $template) {
                        OnesignalDelivery::dispatch($deliveryService->getAllTemplatesByIdDTO($template))
                            ->onQueue('onesignal');
                    }
                }
            });
    }
}
