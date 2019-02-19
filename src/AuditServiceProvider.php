<?php

/**
 * This file is part of itas/laravel-audit.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    itas<luoylangpeng@gmail.com>
 * @copyright itas<luoylangpeng@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Itas\LaravelAudit;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AuditServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Itas\LaravelAudit\Events\CreateRecorded' => [
            'Itas\LaravelAudit\Listeners\CreateAuditRecord'
        ]
    ];

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            \dirname(__DIR__).'/config/audit.php' => config_path('audit.php'),
        ], 'config');
        $this->publishes([
            \dirname(__DIR__).'/migrations/' => database_path('migrations'),
        ], 'migrations');
        $this->publishes([
            \dirname(__DIR__).'/resources/views' => resource_path('views/audit'),
        ], 'resources');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(\dirname(__DIR__).'/migrations/');
        }

        if ((double) $this->app->version() >= 5.2) {
            $this->app['router']->post('itas/audit', '\Itas\LaravelAudit\Controllers\AuditController@audit')->middleware('web');
        } else {
            $this->app['router']->post('itas/audit', '\Itas\LaravelAudit\Controllers\AuditController@audit');
        }

        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__).'/config/audit.php', 'audit'
        );
    }

    public function listens()
    {
        return $this->listen;
    }
}