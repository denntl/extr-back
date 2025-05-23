<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\ApplicationComment;
use App\Models\Team;
use App\Models\User;
use App\Policies\Admin\Client\User\UserPolicy;
use App\Policies\ApplicationCommentPolicy;
use App\Policies\ApplicationPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ApplicationComment::class => ApplicationCommentPolicy::class,
        Application::class => ApplicationPolicy::class,
        Team::class => TeamPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
