<?php

namespace App\Services\Client\Notification;

use App\Models\CompanyNotification;
use App\Models\NotificationTemplate;

class NotificationService
{
    public function __construct(protected int $companyId)
    {
    }


    public function activate(int $id, bool $isActive): void
    {
        $notification = CompanyNotification::query()
            ->where('company_id', $this->companyId)
            ->where('notification_template_id', $id)
            ->first();

        if (!$notification) {
            $notification = CompanyNotification::query()
                ->create([
                    'company_id' => $this->companyId,
                    'notification_template_id' => $id,
                    'is_enabled' => false,
                ]);
        }

        $notification->is_enabled = $isActive;
        $notification->save();
    }
}
