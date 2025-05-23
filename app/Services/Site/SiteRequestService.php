<?php

namespace App\Services\Site;

use App\Models\Application;
use App\Models\Domain;
use Illuminate\Http\Request;

class SiteRequestService
{
    public string $host = '';

    public string $domain = '';

    public string $subdomain = '';

    public ?string $ipCountry = null;
    public ?string $ipReal = null;
    public ?string $userAgent = null;

    public bool $isPostback = false;

    public bool $isPreview = false;

    public ?string $previewUuid = null;

    public ?Domain $domainModel = null;

    public Request $request;

    public bool $isIframe = false;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->host = $request->host();
        $referer = $request->headers->get('referer');
        $this->isIframe = $referer && $referer !== $request->getSchemeAndHttpHost();
        $this->ipCountry = $request->header('cf-ipcountry');

        if (!$this->ipCountry && app()->environment('local')) {
            $this->ipCountry = env('DEFAULT_IP_COUNTRY');
        }

        $this->ipReal = $request->header('cf-connecting-ip') ?: $request->ip();
        $this->userAgent = $request->userAgent() ?? null;

        $this->searchDomain();
        if (str_starts_with($this->host, 'postback') || $request->routeIs('postback')) {
            $this->isPostback = true;
        }
        if ($request->routeIs('preview') || $request->routeIs('previewPost')) {
            $this->isPreview = true;
            $this->previewUuid = $request->route('app_uuid');
        }
    }

    protected function searchDomain(): void
    {
        $application = Application::query()->where(['full_domain' => $this->host])->first();
        if ($application) {
            $this->domain = $application->domain->domain;
            $this->domainModel = $application->domain;
            $this->subdomain = $application->subdomain;
        } else {
            $this->domain = $this->host;
        }
    }

    protected function getProtocol(): string
    {
        return $this->request->isSecure() ? 'https://' : 'http://';
    }

    public function getHostUrl(string $uri = ''): string
    {
        return $this->getProtocol() . $this->host . $uri;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->request->header('CF-IPCountry') ?? '';
    }
}
