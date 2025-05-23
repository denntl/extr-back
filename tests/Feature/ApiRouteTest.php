<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiRouteTest extends TestCase
{
    private array $validPrefixes = [
        'manage' => 'manage',
        'client' => 'client',
        'common' => 'common',
    ];

    private array $rules = [
        'edit' => 'Update',
        'update' => 'Update',
        'store' => 'Create',
        'create' => 'Create',
        'delete' => 'Delete',
        'destroy' => 'Delete',
    ];

    private array $exceptionsForRoute = [
        'client.application.create' => 'Save',
        'client.application.store' => 'Save',
        'client.application.update' => 'Save',
        'client.application.edit' => 'Save',
        'client.application.comment.destroy' => 'Save',
        'client.application.comment.edit' => 'Save',
        'client.application.comment.update' => 'Save',
        'client.application.comment.store' => 'Save',
        'manage.application.create' => 'Save',
        'manage.application.store' => 'Save',
        'manage.application.update' => 'Save',
        'manage.application.edit' => 'Save',
        'manage.application.comment.destroy' => 'Save',
        'manage.application.comment.edit' => 'Save',
        'manage.application.comment.update' => 'Save',
        'manage.application.comment.store' => 'Save',
    ];

    public function testRouteSuffixMatchesWithMiddleware()
    {
        $apiRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/');
        });

        foreach ($apiRoutes as $route) {
            $routeName = $route->getName();
            $middlewares = $route->middleware();

            if (!$routeName) {
                continue;
            }

            $routeParts = explode('.', $routeName);
            $lastPart = last($routeParts);

            if (
                !isset($this->rules[$lastPart])
                && !$this->routeHasPermission($middlewares)
            ) {
                continue;
            }

            if (isset($this->exceptionsForRoute[$routeName])) {
                $this->assertTrue(
                    $this->anyMiddlewareEndsWithExceptionalSuffix($middlewares, $this->exceptionsForRoute[$routeName]),
                    'Route <fg=green;options=bold>' . $routeName . '</> should have a middleware ending with <fg=green;options=bold>'
                        . $this->exceptionsForRoute[$routeName] . '</>. Permission is <fg=red;options=bold>'
                        . str_replace('permission:', '', implode('|', preg_grep('/^permission:.*/', $middlewares))) . '</>'
                );
                continue;
            }

            if (isset($this->rules[$lastPart])) {
                $this->assertTrue(
                    $this->anyMiddlewareEndsWithOrContain($middlewares, $this->rules[$lastPart]),
                    'Route <fg=green;options=bold>' . $routeName . '</> should have a middleware ending with <fg=green;options=bold>'
                        . $this->rules[$lastPart] . '</>. Permission is <fg=red;options=bold>'
                        . str_replace('permission:', '', implode('|', preg_grep('/^permission:.*/', $middlewares))) . '</>'
                );
            }
        }
    }

    public function testRoutePrefixMatchesWithMiddleware()
    {
        $apiRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri, 'api/');
        });

        foreach ($apiRoutes as $route) {
            $routeName = $route->getName();
            $middlewares = $route->middleware();

            if (!$routeName || !$this->routeHasPermission($middlewares)) {
                continue;
            }

            $routeParts = explode('.', $routeName);
            $firstPart = $routeParts[0];

            if (isset($this->validPrefixes[$firstPart])) {
                $permissionBlock = str_replace('permission:', '', implode('|', preg_grep('/^permission:.*/', $middlewares)));
                $message = "Route <fg=red;options=bold>$routeName</> should have a middleware starting with "
                    . '<fg=green;options=bold>' . $this->validPrefixes[$firstPart] . '</>, permission is <fg=red;options=bold>'
                    . $permissionBlock . '</>';
                $this->assertTrue($this->anyMiddlewareStartsWithOrContain($middlewares, $this->validPrefixes[$firstPart]), $message);
            }
        }
    }

    private function anyMiddlewareEndsWithOrContain(array $middlewares, string $suffix): bool
    {
        foreach ($middlewares as $middleware) {
            if (str_ends_with($middleware, $suffix) || str_contains($middleware, $suffix . '|')) {
                return true;
            }
        }
        return false;
    }
    private function anyMiddlewareStartsWithOrContain(array $middlewares, string $prefix): bool
    {
        foreach ($middlewares as $middleware) {
            $permission = str_replace('permission:', '', $middleware);
            if (str_contains($permission, '|')) {
                $permissions = explode('|', $permission);
                foreach ($permissions as $p) {
                    if (!str_starts_with($p, $prefix)) {
                        return false;
                    }
                }
            }
            if (str_starts_with($permission, $prefix)) {
                return true;
            }
        }
        return false;
    }

    private function anyMiddlewareEndsWithExceptionalSuffix(array $middlewares, string $suffix): bool
    {
        foreach ($middlewares as $middleware) {
            if (str_ends_with($middleware, $suffix)) {
                return true;
            }
        }
        return false;
    }

    private function routeHasPermission($middlewares): bool
    {
        return !empty(preg_grep('/^permission:.*/', $middlewares));
    }
}
