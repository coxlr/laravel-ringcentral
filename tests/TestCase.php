<?php

namespace Coxlr\RingCentral\Tests;

use Coxlr\RingCentral\RingCentralServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        //$this->withFactories(__DIR__.'/database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            RingCentralServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('ringcentral.client_id', 'my_client_id');
        $app['config']->set('ringcentral.client_secret', 'my_client_secret');
        $app['config']->set('ringcentral.server_url', 'my_server_url');
        $app['config']->set('ringcentral.username', 'my_username');
        $app['config']->set('ringcentral.operator_extension', 'my_operator_extension');
        $app['config']->set('ringcentral.operator_password', 'my_operator_password');
        $app['config']->set('ringcentral.admin_extension', 'my_admin_extension');
        $app['config']->set('ringcentral.admin_password', 'my_admin_password');

        /*
        include_once __DIR__.'/../database/migrations/create_laravel_ringcentral_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }

    /**
     * Gets the property of an object of a class.
     *
     * @param string $class
     * @param string $property
     * @param mixed $object
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function getClassProperty($class, $property, $object)
    {
        $reflectionClass = new \ReflectionClass($class);
        $refProperty = $reflectionClass->getProperty($property);
        $refProperty->setAccessible(true);

        return $refProperty->getValue($object);
    }
}
