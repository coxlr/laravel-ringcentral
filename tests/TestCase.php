<?php

namespace Coxlr\RingCentral\Tests;

use Coxlr\RingCentral\RingCentralServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionException;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            RingCentralServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('ringcentral.client_id', 'my_client_id');
        $app['config']->set('ringcentral.client_secret', 'my_client_secret');
        $app['config']->set('ringcentral.server_url', 'my_server_url');
        $app['config']->set('ringcentral.username', 'my_username');
        $app['config']->set('ringcentral.operator_token', 'my_operator_token');
        $app['config']->set('ringcentral.admin_token', 'my_admin_token');
    }

    /**
     * Gets the property of an object of a class.

     * @throws ReflectionException
     */
    public function getClassProperty(string $class, string $property, mixed $object): mixed
    {
        $reflectionClass = new \ReflectionClass($class);
        $refProperty = $reflectionClass->getProperty($property);
        $refProperty->setAccessible(true);

        return $refProperty->getValue($object);
    }

    /**
     * Add delay before running each test to prevent hitting RingCentral rate limits.
     */
    protected function delay(): void
    {
        sleep(env('RINGCENTRAL_DELAY_REQUEST_SECONDS') ?: 0);
    }
}
