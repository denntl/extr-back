<?php

namespace App\Services\Site;

use App\Enums\Application\Language;
use App\Enums\Application\Status;
use App\Models\Application;
use App\Models\File;
use App\Models\TopApplication;

class ApplicationService
{
    public function getApplication(string $fullDomain, ?string $ipCountry): ?Application
    {
        if (!$ipCountry) {
            return null;
        }
        return Application::query()->select('applications.*')
            ->where(['full_domain' => $fullDomain])
            ->where('status', Status::Active->value)
            ->leftJoin('application_geo_languages', 'applications.id', '=', 'application_geo_languages.application_id')
            ->where('application_geo_languages.geo', '=', $ipCountry)
            ->first();
    }

    public function getApplicationByUuid(string $uuid): ?Application
    {
        return Application::query()->where('uuid', $uuid)->first();
    }

    public function getWhitePage(string $fullDomain): ?Application
    {
        return Application::query()
            ->where(['full_domain' => $fullDomain])
            ->first();
    }

    public function fillApplicationForPreview(array $data): Application
    {
        $application = !empty($data['uuid']) ? Application::query()->where('uuid', $data['uuid'])->firstOrFail() : new Application(['uuid' => 'uuid']);
        unset($data['uuid']);

        if (empty($data['icon'])) {
            $data['icon'] = '/placeholder.svg';
        }
        if (empty($data['language'])) {
            $data['language'] = 'en';
        }
        if (empty($data['status'])) {
            $data['status'] = Status::Active->value;
        }
        $application->fill($data);
        if (!empty($data['files'])) {
            $files = File::query()->whereIn('id', $data['files'])->get();
            $files = $files->sortBy(function ($item) use ($data) {
                return array_search($item->id, $data['files']);
            });
            $application->files = $files;
        }

        $application->language = $data['language'] ?? Language::En->value;

        $application->topApplications = [];

        if (!empty($data['company_id']) && !empty($data['topApplicationIds'])) {
            $application->topApplications = Application::query()
                ->join('companies', 'applications.company_id', '=', 'companies.id')
                ->whereIn('applications.public_id', $data['topApplicationIds'])
                ->where('companies.public_id', $data['company_id'])
                ->get();
        }

        $application->display_top_bar = (bool) $application->display_top_bar;
        $application->display_app_bar = (bool) $application->display_app_bar;
        $application->landing_type = (int) $application->landing_type;
        $application->platform_type = (int) $application->platform_type;

        return $application;
    }
}
