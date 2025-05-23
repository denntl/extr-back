<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\Domain;

class ApplicationObserver
{
    public function creating(Application $model): void
    {
        $this->setFullDomain($model);
    }

    public function updating(Application $model): void
    {
        $this->setFullDomain($model);
    }

    private function setFullDomain(Application $model): void
    {
        if ($model->getOriginal('domain_id') !== $model->domain_id || $model->getOriginal('subdomain') !== $model->subdomain) {
            $domain = Domain::query()->find($model->domain_id);
            $model->full_domain = "$model->subdomain.$domain->domain";
        }
    }
}
