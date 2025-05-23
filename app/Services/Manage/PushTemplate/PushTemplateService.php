<?php

namespace App\Services\Manage\PushTemplate;

use App\Enums\PushTemplate\Event;
use App\Models\PushTemplate;
use Illuminate\Database\Eloquent\Collection;

readonly class PushTemplateService
{
    public static function getEventsList(): array
    {
        return [
            ['value' => Event::INSTALL, 'label' => 'Установка'],
            ['value' => Event::REGISTRATION, 'label' => 'Регистрация'],
            ['value' => Event::DEPOSIT, 'label' => 'Депозит'],
        ];
    }

    public function getById(int $id): PushTemplate
    {
        return PushTemplate::findOrFail($id);
    }

    public function create(array $data, int $creatorId): PushTemplate
    {
        $data['created_by'] = $creatorId;
        return PushTemplate::query()->create($data);
    }

    public function update(int $id, array $data, int $updaterId): PushTemplate
    {
        $template = PushTemplate::query()->findOrFail($id);
        $data['created_by'] = $updaterId;
        $template->update($data);
        return $template;
    }

    public static function getStatusesList(): array
    {
        return [
            ['value' => true, 'label' => 'Активно'],
            ['value' => false, 'label' => 'Неактивно'],
        ];
    }

    public function getListForSelections(): Collection
    {
        return PushTemplate::query()->selectRaw('id as value, name as label')
            ->get();
    }
}
