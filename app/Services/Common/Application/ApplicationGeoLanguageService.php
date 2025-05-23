<?php

namespace App\Services\Common\Application;

use App\Models\Application;
use App\Models\ApplicationGeoLanguage;

class ApplicationGeoLanguageService
{
    public function saveGeoLanguages(Application $application, array $geoLanguages = []): void
    {
        $application->applicationGeoLanguages()->whereNotIn('geo', array_column($geoLanguages, 'geo'))->delete();
        foreach ($geoLanguages as $geoLanguage) {
            $application->applicationGeoLanguages()->updateOrCreate(['geo' => $geoLanguage['geo']], [
                'language' => $geoLanguage['language'],
            ]);
        }
    }
}
