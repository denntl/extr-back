<?php

namespace App\Enums\NotificationTemplate;

use App\Enums\NotificationTemplate\Entity;
use App\Enums\NotificationTemplate\Event;

class EntityHasEvents
{
    public static function get(): array
    {
        return [
            [
                'entity' => Entity::User,
                'events' => [
                    Event::UserLoggedIn,
                    Event::UserStatusChanged,
                ],
            ],
            [
                'entity' => Entity::Domain,
                'events' => [
                    Event::DomainCreated,
                    Event::DomainStatusChanged,
                ]
            ],
            [
                'entity' => Entity::Application,
                'events' => [
                    Event::ApplicationDeactivated,
                    Event::ApplicationCreated,
                ]
            ],
        ];
    }

    public static function mapToMultiselect(): array
    {
        return array_map(function ($item) {
            return [
                'label' => $item['entity']->name,
                'value' => $item['entity']->value,
                'events' => array_map(function ($event) {
                    return [
                        'value' => $event->value,
                        'label' => $event->name
                    ];
                }, $item['events'])
            ];
        }, self::get());
    }

    public static function events(): array
    {
        $result = [];
        foreach (self::get() as $entity) {
            foreach ($entity['events'] as $key => $event) {
                $result[$entity['entity']->value][$key]['label'] = __("notifications.{$event->value}");
                $result[$entity['entity']->value][$key]['value'] = $event->value;
            }
        }
        return $result;
    }
}
