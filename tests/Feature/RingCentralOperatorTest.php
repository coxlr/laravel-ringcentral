<?php

namespace Coxlr\Ringcentral\Tests\Feature;

use Coxlr\RingCentral\Exceptions\CouldNotSendMessage;
use Coxlr\RingCentral\RingCentral;
use Coxlr\RingCentral\Tests\TestCase;
use Dotenv\Dotenv;
use RingCentral\SDK\Http\ApiException;

class RingCentralOperatorTest extends TestCase
{
    protected RingCentral|Coxlr\RingCentralLaravel\RingCentral $ringCentral;

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
            ->setOperatorToken(env('RINGCENTRAL_OPERATOR_TOKEN'));

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
    public function it_can_send_an_sms_message(): void
    {
        $result = $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $this->assertNotNull($result->json());


        $this->assertEquals(
            env('RINGCENTRAL_IS_SANDBOX')
                ? 'Test SMS using a RingCentral Developer account - Test Message'
                : 'Test Message',
            $result->json()->subject
        );

        $this->assertEquals(env('RINGCENTRAL_RECEIVER'), $result->json()->to[0]->phoneNumber);
        $this->assertEquals(env('RINGCENTRAL_USERNAME'), $result->json()->from->phoneNumber);
    }

    /** @test */
    public function it_can_retrieve_operator_sent_sms_messages_from_previous_24_hours(): void
    {
        $result = $this->ringCentral->getOperatorMessages();

        $firstMessage = (array) $result[0];

        $this->assertArrayHasKey('id',  $firstMessage);
        $this->assertArrayHasKey('to', $firstMessage);
        $this->assertArrayHasKey('from', $firstMessage);
        $this->assertArrayHasKey('subject', $firstMessage);
        $this->assertArrayHasKey('attachments', $firstMessage);
    }

    /** @test */
    public function it_can_retrieve_operator_sent_sms_messages_from_a_set_date(): void
    {
        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
            'text' => 'Test Message',
        ]);

        $result = $this->ringCentral->getOperatorMessages((new \DateTime())->modify('-2 seconds'));

        //Note: Is 1 when running full test suite up to this point otherwise is 0 when test run individually
        $this->assertCount(1, $result);

        $firstMessage = (array) $result[0];

        $this->assertArrayHasKey('id',  $firstMessage);
        $this->assertArrayHasKey('to', $firstMessage);
        $this->assertArrayHasKey('from', $firstMessage);
        $this->assertArrayHasKey('subject', $firstMessage);
        $this->assertArrayHasKey('attachments', $firstMessage);
    }

    /** @test */
    public function it_can_retrieve_operator_sent_sms_messages_from_a_set_date_to_a_set_date(): void
    {
        $result = $this->ringCentral->getOperatorMessages((new \DateTime())->modify('-1 mins'), (new \DateTime())->modify('+2 mins'));

        $this->assertNotEmpty($result);
        $this->assertTrue(count($result) < 10);

        $firstMessage = (array) $result[0];

        $this->assertArrayHasKey('id',  $firstMessage);
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

        $result = $this->ringCentral->getOperatorMessages(null, null, 1);

        $this->assertSame(count($result), 1);

        $result = $this->ringCentral->getOperatorMessages(null, null, 2);

        $this->assertSame(count($result), 2);
    }

    /** @test */
    public function it_requires_a_to_number_to_send_an_sms_message(): void
    {
        $this->expectException(CouldNotSendMessage::class);

        $this->ringCentral->sendMessage([
            'text' => 'Test Message',
        ]);
    }

    /** @test */
    public function it_requires_a_to_message_to_send_an_sms_message(): void
    {
        $this->expectException(CouldNotSendMessage::class);

        $this->ringCentral->sendMessage([
            'to' => env('RINGCENTRAL_RECEIVER'),
        ]);
    }

    /** @test */
    public function an_exception_is_thrown_if_message_not_sent(): void
    {
        $this->expectException(ApiException::class);

        $this->ringCentral->sendMessage([
            'to' => 123,
            'text' => 'Test Message',
        ]);
    }
}
