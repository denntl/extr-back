<?php

namespace App\Services\Client\Domain;

use App\Enums\Domain\Status;
use App\Models\Application;
use App\Models\Domain;
use Illuminate\Database\Eloquent\Collection;

class DomainService
{
    public static function getDomainList(): array
    {
        return Domain::query()
            ->where('status', Status::Active->value)
            ->get()
            ->map(fn (Domain $domain) => ['value' => $domain->id, 'label' => $domain->domain])
            ->toArray();
    }

    public static function getStatusList(): array
    {
        return [
            ['value' => Status::NotActive->value, 'label' => 'Неактивный'],
            ['value' => Status::Active->value, 'label' => 'Активный'],
        ];
    }

    public function store($data): Domain
    {
        return Domain::create($data);
    }

    public function toggleStatus($id, $status): Domain
    {
        $domain = Domain::find($id);
        $domain->update($status);
        return $domain;
    }

    public function getUsersByDomain(int $domainId): Collection
    {
        return Application::query()
            ->select('owner_id')
            ->where('applications.domain_id', '=', $domainId)
            ->groupBy('owner_id')
            ->get();
    }
}
