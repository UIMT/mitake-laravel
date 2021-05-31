<?php

/**
 * Copyright (c) 2021 UIMT
 *
 * UIMT licenses this file to you under the MIT License (the "License");
 * you may not use this file except in compliance with the License. You
 * may obtain a copy of the License at:
 *
 *   https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Mitake\Laravel;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Mitake\Client;

class MitakeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application service
     *
     * @return void
     */
    public function boot()
    {
        $dist = __DIR__ . '/../config/mitake.php';

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$dist => config_path('mitake.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('mitake');
        }

        $this->mergeConfigFrom($dist, 'mitake');
    }

    /**
     * Register bindings in the container
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            return $this->createMitakeClient($app['config']);
        });
    }

    /**
     * Get the services provided by the provider
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }

    /**
     * @param Config $config
     *
     * @return Client
     */
    protected function createMitakeClient(Config $config)
    {
        $options = $config->get('mitake');

        return new Client($options['username'], $options['password']);
    }
}
