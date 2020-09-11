<?php

namespace Coxlr\RingCentral\Tests;

use Coxlr\RingCentral\RingCentral;

class RingCentralServiceProviderTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_resolves_from_the_service_container()
    {
        $ringCentral = app('ringcentral');

        $this->assertInstanceOf(RingCentral::class, $ringCentral);

        $this->assertEquals('my_client_id', $ringCentral->clientId());
        $this->assertEquals('my_client_secret', $ringCentral->clientSecret());
        $this->assertEquals('my_server_url', $ringCentral->serverUrl());
        $this->assertEquals('my_username', $ringCentral->username());
        $this->assertEquals('my_operator_extension', $ringCentral->operatorExtension());
        $this->assertEquals('my_operator_password', $ringCentral->operatorPassword());
        $this->assertEquals('my_admin_extension', $ringCentral->adminExtension());
        $this->assertEquals('my_admin_password', $ringCentral->adminPassword());
    }
}
