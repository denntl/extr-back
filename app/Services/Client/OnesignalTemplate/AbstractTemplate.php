<?php

namespace App\Services\Client\OnesignalTemplate;

use App\Jobs\OnesignalDeletePush;
use App\Models\OnesignalTemplate;
use App\Models\OnesignalTemplateContents;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\OnesignalTemplate\DTOs\RequestDTO;
use App\Services\Client\OnesignalTemplate\Exceptions\OnesignalTemplateException;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

abstract class AbstractTemplate
{
    public function __construct()
    {
    }

    abstract protected function getSettingsModelByTemplateId(int $templateId): Model;
    abstract protected function getSettingsModel(): Model;
    abstract protected function getSettingsDTO(RequestDTO $requestDTO): SettingsInterface;

    /**
     * @throws \Throwable
     * @throws ValidationException
     */
    public function store(RequestDTO $data): OnesignalTemplate
    {
        DB::beginTransaction();
        try {
            //save to onesignal_templates
            $osTemplate = OnesignalTemplate::query()->create($data->commonData());
            $data->setTemplateId($osTemplate->id);

            /**
             * save template_applications
             * @var ApplicationService $applications
             */
            $applications = app(ApplicationService::class);
            $apps = $applications->getAllByPublicId($data->application_ids, false);
            $osTemplate->applications()->sync($apps);

            //save onesignal_templates_contents
            foreach ($data->contents as $content) {
                OnesignalTemplateContents::query()->create($data->contentData($content, $osTemplate->id));
            }

            //save one of settings
            $dataToCreate = $this->getSettingsDTO($data);
            $this->getSettingsModel()->query()->create($dataToCreate->toArray());

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $osTemplate;
    }

    private function getContentModelByTemplateAndGeoId(int $templateId, int $geoId): OnesignalTemplateContents
    {
        return OnesignalTemplateContents::query()
            ->where('onesignal_template_id', $templateId)
            ->where('geo_id', $geoId)
            ->firstOrFail();
    }

    /**
     * @throws \Throwable
     * @throws ValidationException
     */
    public function update(RequestDTO $data, OnesignalTemplate $template): ?int
    {
        $settings = $this->getSettingsModelByTemplateId($data->onesignal_template_id);

        if ($template->is_active && !empty($settings->getAttribute('handled_at'))) {
            throw new OnesignalTemplateException('You can\'t update template in 5 minutes to be sent');
        }

        DB::beginTransaction();
        try {
            //update onesignal_templates
            $updateData = $data->commonData();
            unset($updateData['created_by']);
            $template->update($updateData);

            //save onesignal_templates_contents
            foreach ($data->contents as $content) {
                $this->getContentModelByTemplateAndGeoId($data->onesignal_template_id, $content->geo)
                    ->update($data->contentData($content, $data->onesignal_template_id));
            }

            //save one of settings
            $dataToUpdate = $this->getSettingsDTO($data);
            $settings->update($dataToUpdate->toArray());

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $data->onesignal_template_id;
    }

    private function cancelPush(int $templateId): void
    {
        /** @var OneSignalNotificationService $notificationService */
        $notificationService = app(OnesignalNotificationService::class);
        $notifications = $notificationService->getByTemplateId($templateId);
        foreach ($notifications as $notification) {
            OnesignalDeletePush::dispatch($notification)
                ->onQueue('onesignal');
        }
    }

    /**
     * @throws \Exception
     */
    public function delete(int $templateId, OnesignalTemplate $template): void
    {
        $settings = $this->getSettingsModelByTemplateId($templateId);

        if ($template->is_active && !empty($settings->getAttribute('handled_at'))) {
            throw new OnesignalTemplateException('You can\'t delete template in 5 minutes to be sent');
        }

        $template->update(['is_active' => false]);
        $template->delete();
    }
}
