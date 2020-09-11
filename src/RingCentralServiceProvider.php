<?php

namespace Coxlr\RingCentral;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;

//use Coxlr\LaravelRingcentral\Commands\LaravelRingcentralCommand;

class RingCentralServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ringcentral.php' => config_path('ringcentral.php'),
            ], 'config');

            /*
            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/laravel-ringcentral'),
            ], 'views');

            $migrationFileName = 'create_laravel_ringcentral_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                LaravelRingcentralCommand::class,
            ]);
            */
        }

        //$this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-ringcentral');
    }

    public function register()
    {
        // Bind RingCentral Client in Service Container.
        $this->app->singleton('ringcentral', function () {
            return $this->createRingCentralClient();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/ringcentral.php', 'ringcentral');
    }

    /**
     * Create a new RingCentral Client.
     *
     * @return RingCentral
     *
     */
    protected function createRingCentralClient()
    {
        // Check for RingCentral config file.
        if (! $this->hasRingCentralConfigSection()) {
            $this->raiseRunTimeException('Missing RingCentral configuration.');
        }

        if ($this->ringCentralConfigHasNo('client_id')) {
            $this->raiseRunTimeException('Missing client_id.');
        }

        if ($this->ringCentralConfigHasNo('client_secret')) {
            $this->raiseRunTimeException('Missing client_secret.');
        }

        if ($this->ringCentralConfigHasNo('server_url')) {
            $this->raiseRunTimeException('Missing server_url.');
        }

        if ($this->ringCentralConfigHasNo('username')) {
            $this->raiseRunTimeException('Missing username.');
        }

        if ($this->ringCentralConfigHasNo('operator_extension')) {
            $this->raiseRunTimeException('Missing extension.');
        }

        if ($this->ringCentralConfigHasNo('operator_password')) {
            $this->raiseRunTimeException('Missing password.');
        }

        $ringCentral = (new RingCentral())
            ->setClientId(config('ringcentral.client_id'))
            ->setClientSecret(config('ringcentral.client_secret'))
            ->setServerUrl(config('ringcentral.server_url'))
            ->setUsername(config('ringcentral.username'))
            ->setOperatorExtension(config('ringcentral.operator_extension'))
            ->setOperatorPassword(config('ringcentral.operator_password'));

        if ($this->ringCentralConfigHas('admin_extension')) {
            $ringCentral->setAdminExtension(config('ringcentral.admin_extension'));
        }

        if ($this->ringCentralConfigHas('admin_password')) {
            $ringCentral->setAdminPassword(config('ringcentral.admin_password'));
        }

        return $ringCentral;
    }

    /**
     * Checks if has global RingCentral configuration section.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function hasRingCentralConfigSection()
    {
        return $this->app->make(Config::class)
            ->has('ringcentral');
    }

    /**
     * Checks if RingCentral config does not
     * have a value for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function ringCentralConfigHasNo($key)
    {
        return ! $this->ringCentralConfigHas($key);
    }

    /**
     * Checks if RingCentral config has value for the
     * given key.
     *
     * @param string $key
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function ringCentralConfigHas($key)
    {
        /** @var Config $config */
        $config = $this->app->make(Config::class);

        // Check for RingCentral config file.
        if (! $config->has('ringcentral')) {
            return false;
        }

        return
            $config->has('ringcentral.'.$key) &&
            ! is_null($config->get('ringcentral.'.$key)) &&
            ! empty($config->get('ringcentral.'.$key));
    }


    /**
     * Raises Runtime exception.
     *
     * @param string $message
     *
     * @throws \RuntimeException
     */
    protected function raiseRunTimeException($message)
    {
        throw new \RuntimeException($message);
    }
}
