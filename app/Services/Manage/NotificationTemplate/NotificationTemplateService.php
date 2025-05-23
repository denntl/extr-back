<?php

namespace App\Services\Manage\NotificationTemplate;

use App\Enums\NotificationTemplate\Entity;
use App\Enums\NotificationTemplate\EntityHasEvents;
use App\Enums\NotificationTemplate\Event;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\DB;

class NotificationTemplateService
{
    public function getEntitiesList(): array
    {
        return array_map(
            fn($entity) => [
            'value' => $entity->value,
            'label' => __("notifications." . Entity::tryFrom($entity->value)?->name)
            ],
            Entity::cases()
        );
    }

    public function mapEventsList(): array
    {
        return EntityHasEvents::events();
    }

    public function getEventsList(): array
    {
        return array_map(fn($event) => ['value' => $event->value, 'label' => $event->name], Event::cases());
    }

    public function activeOptions(): array
    {
        return [
            ['value' => true, 'label' => 'Активно'],
            ['value' => false, 'label' => 'Не активно'],
        ];
    }

    public function enableForClientOptions(): array
    {
        return [
            ['value' => true, 'label' => 'Да'],
            ['value' => false, 'label' => 'Нет'],
        ];
    }

    public function allRolesOptions(): array
    {
        return [
            ['value' => true, 'label' => 'Да'],
            ['value' => false, 'label' => 'Нет'],
        ];
    }

    public function getById(int $id): NotificationTemplate
    {
        return NotificationTemplate::query()->findOrFail($id);
    }

    public function create(array $params): NotificationTemplate
    {
        DB::beginTransaction();
        try {
            /** @var NotificationTemplate $notificationTemplate */
            $notificationTemplate = NotificationTemplate::query()->create([
                'name' => $params['name'],
                'is_auto' => true,
                'entity' => $params['entity'],
                'event' => $params['event'],
                'all_roles' => $params['isAllUsers'],
                'is_active' => $params['isActive'],
                'enable_for_client' => $params['isClientShow'],
            ]);

            if (!$params['isAllUsers']) {
                $notificationTemplate->roles()->sync($params['roles']);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $notificationTemplate;
    }

    public function update(int $id, array $params): NotificationTemplate
    {
        DB::beginTransaction();
        try {
            $notificationTemplate = $this->getById($id);
            $notificationTemplate->update([
                'name' => $params['name'],
                'entity' => $params['entity'],
                'event' => $params['event'],
                'all_roles' => $params['isAllUsers'],
                'is_active' => $params['isActive'],
                'enable_for_client' => $params['isClientShow'],
            ]);
            $notificationTemplate->roles()->sync(!$params['isAllUsers'] ? $params['roles'] : []);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $notificationTemplate;
    }

    public function delete(int $id): bool|null
    {
        $notificationTemplate = $this->getById($id);
        return $notificationTemplate->delete();
    }
}
