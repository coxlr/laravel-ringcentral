<?php

namespace Coxlr\Ringcentral\Tests\Feature;

use Coxlr\RingCentral\RingCentral;
use Coxlr\RingCentral\Tests\TestCase;
use Dotenv\Dotenv;

class RingCentralAdminTest extends TestCase
{
    protected RingCentral|Coxlr\RingCentral\RingCentral $ringCentral;

    public function setUp() : void
    {
        parent::setUp();

        $this->loadEnvironmentVariables();

        $this->ringCentral = new RingCentral();

        $this->ringCentral
            ->setClientId(env('RINGCENTRAL_CLIENT_ID'))
            ->setClientSecret(env('RINGCENTRAL_CLIENT_SECRET'))
            ->setServerUrl(env('RINGCENTRAL_SERVER_URL'))
            ->setUsername(env('RINGCENTRAL_USERNAME'))
            ->setOperatorToken(env('RINGCENTRAL_OPERATOR_TOKEN'))
            ->setAdminToken(env('RINGCENTRAL_ADMIN_TOKEN'));

        $this->delay();
    }

    protected function loadEnvironmentVariables(): void
    {
        if (! file_exists(__DIR__.'/../../.env')) {
            return;
        }

        $dotenv = Dotenv::createImmutable(__DIR__.'/../..');

        $dotenv->load();
    }

    /** @test */
    public function it_can_retrieve_extensions(): void
    {
        $result = $this->ringCentral->getExtensions();

        $firstExtension = (array) $result[0];

        $this->assertArrayHasKey('id', $firstExtension);
        $this->assertArrayHasKey('extensionNumber', $firstExtension);
    }

    /** @test */
    public function it_can_retrieve_sent_sms_messages_for_a_given_extension_previous_24_hours(): void
    {
        $this->ringCentral->authenticateAdmin();

        $result = $this->ringCentral->getMessagesForExtensionId($this->ringCentral->loggedInExtensionId());

        $firstMessage = (array) $result[0];

        $uriParts = explode('/', $firstMessage['uri']);
        $this->assertEquals($this->ringCentral->loggedInExtensionId(), $uriParts[8]);

        $this->assertArrayHasKey('id', $firstMessage);
        $this->assertArrayHasKey('to', $firstMessage);
        $this->assertArrayHasKey('from', $firstMessage);
        $this->assertArrayHasKey('subject', $firstMessage);
        $this->assertArrayHasKey('attachments', $firstMessage);
    }

    /** @test */
    public function it_can_retrieve_sent_sms_messages_for_a_given_extension_from_a_set_date(): void
    {
        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $result = $this->ringCentral->getMessagesForExtensionId(
            $this->ringCentral->loggedInExtensionId(),
            (new \DateTime())->modify('-1 mins')
        );

        $this->assertTrue(count($result) < 3);

        $firstMessage = (array) $result[0];

        $uriParts = explode('/', $firstMessage['uri']);
        $this->assertEquals($this->ringCentral->loggedInExtensionId(), $uriParts[8]);

        $this->assertArrayHasKey('id', $firstMessage);
        $this->assertArrayHasKey('to', $firstMessage);
        $this->assertArrayHasKey('from', $firstMessage);
        $this->assertArrayHasKey('subject', $firstMessage);
        $this->assertArrayHasKey('attachments', $firstMessage);
    }

    /** @test */
    public function it_can_retrieve_sent_sms_messages_for_a_given_extension_from_a_set_date_to_a_set_date(): void
    {
        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $result = $this->ringCentral->getMessagesForExtensionId(
            $this->ringCentral->loggedInExtensionId(),
            (new \DateTime())->modify('-1 mins'),
            (new \DateTime())->modify('+2 mins')
        );

        $this->assertNotEmpty($result);
        $this->assertTrue(count($result) < 10);

        $firstMessage = (array) $result[0];

        $uriParts = explode('/', $firstMessage['uri']);
        $this->assertEquals($this->ringCentral->loggedInExtensionId(), $uriParts[8]);

        $this->assertArrayHasKey('id', $firstMessage);
        $this->assertArrayHasKey('to', $firstMessage);
        $this->assertArrayHasKey('from', $firstMessage);
        $this->assertArrayHasKey('subject', $firstMessage);
        $this->assertArrayHasKey('attachments', $firstMessage);
    }

    /** @test */
    public function it_can_retrieve_operator_sent_sms_messages_with_per_page_limit_set(): void
    {
        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $this->delay();

        $result = $this->ringCentral->getMessagesForExtensionId(
            $this->ringCentral->loggedInExtensionId(),
            null,
            null,
            1
        );

        $this->assertSame(count($result), 1);

        $result = $this->ringCentral->getMessagesForExtensionId(
            $this->ringCentral->loggedInExtensionId(),
            null,
            null,
            2
        );

        $this->assertSame(count($result), 2);
    }

    /** @test */
    public function it_can_retrieve_an_sms_messages_attachement(): void
    {
        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $this->delay();

        $result = $this->ringCentral->getMessagesForExtensionId(
            $this->ringCentral->loggedInExtensionId(),
            (new \DateTime())->modify('-1 mins')
        );

        $firstMessage = (array) $result[0];

        $attachment = $this->ringCentral->getMessageAttachmentById(
            $this->ringCentral->loggedInExtensionId(),
            $firstMessage['id'],
            $firstMessage['attachments'][0]->id
        );

        $this->assertNotNull($attachment->raw());
    }
}
