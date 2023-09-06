<?php

namespace Coxlr\RingCentral;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class RingCentralServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ringcentral.php' => config_path('ringcentral.php'),
            ], 'config');
        }
    }

    public function register(): void
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
     * @throws BindingResolutionException
     */
    protected function createRingCentralClient(): RingCentral
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

        if ($this->ringCentralConfigHasNo('operator_token')) {
            $this->raiseRunTimeException('Missing operator token.');
        }

        $ringCentral = (new RingCentral())
            ->setClientId(config('ringcentral.client_id'))
            ->setClientSecret(config('ringcentral.client_secret'))
            ->setServerUrl(config('ringcentral.server_url'))
            ->setUsername(config('ringcentral.username'))
            ->setOperatorToken(config('ringcentral.operator_token'));

        if ($this->ringCentralConfigHas('admin_token')) {
            $ringCentral->setAdminToken(config('ringcentral.admin_token'));
        }

        return $ringCentral;
    }

    /**
     * Checks if has global RingCentral configuration section.
     *
     * @throws BindingResolutionException
     */
    protected function hasRingCentralConfigSection(): bool
    {
        return $this->app->make(Config::class)
            ->has('ringcentral');
    }

    /**
     * Checks if RingCentral config does not
     * have a value for the given key.
     *
     * @throws BindingResolutionException
     */
    protected function ringCentralConfigHasNo(string $key): bool
    {
        return ! $this->ringCentralConfigHas($key);
    }

    /**
     * Checks if RingCentral config has value for the given key.
     *
     * @throws BindingResolutionException
     */
    protected function ringCentralConfigHas(string $key): bool
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
     * @throws RuntimeException
     */
    protected function raiseRunTimeException(string $message): void
    {
        throw new RuntimeException($message);
    }
}
