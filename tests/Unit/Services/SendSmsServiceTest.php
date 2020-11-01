<?php

namespace Tests\Unit\Services;

use App\Models\Notification;
use App\Services\SendSmsService;
use Faker\Factory as Faker;
use Tests\TestCase;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client as TwilioClient;


class SendSmsServiceTest extends TestCase
{

    public function testSendMessagePass()
    {
        $expectedResponse = [
            'one' => 1,
        ];
        $returnObject = $this->setMock(MessageInstance::class);
        $returnObject->errorMessage = null;
        $returnObject->shouldReceive('toArray')
            ->andreturn($expectedResponse)
            ->once();

        $sendObject = $this->setMock(MessageList::class);
        $sendObject->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($returnObject)
            ->once();

        $twilloClient = $this->setMock(TwilioClient::class);
        $twilloClient->messages = $sendObject;
        $sendSmsService = new SendSmsService($twilloClient);

        $response = $this->invokeMethod($sendSmsService, 'sendMessage',
            [
                'cellNumber' => '15005550006',
                'message' => 'this is a test'
            ]
        );
        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testSendMessageFail()
    {
        $errorMessage = "I hate errors";

        $returnObject = $this->setMock(MessageInstance::class);
        $returnObject->errorMessage = null;
        $returnObject->shouldReceive('toArray()')
            ->andreturn([
                'errorMessage' => null
            ])
            ->never();

        $sendObject = $this->setMock(MessageList::class);
        $sendObject->shouldReceive('create')
            ->withAnyArgs()
            ->andThrow(new \Exception($errorMessage))
            ->once();

        $twilloClient = $this->setMock(TwilioClient::class);
        $twilloClient->messages = $sendObject;
        $sendSmsService = new SendSmsService($twilloClient);

        $response = $this->invokeMethod($sendSmsService, 'sendMessage',
            [
                'cellNumber' => '15005550006',
                'message' => 'this is a test'
            ]
        );
        $this->assertNull($response);
        $this->assertEquals($errorMessage, $sendSmsService->getLastError());

    }

    public function testFormatNumber()
    {
        $sendSmsService = new SendSmsService();
        $tests = [
            '7135554444' => '+17135554444',
            '+17135554444' => '+17135554444',
            '713-555-4444' => '+17135554444',
            '713 555. ...4444' => '+17135554444',
            'sadfasdf32@#$#$$^' => '+132',
            '1-343-649-8832 x682' => '+13436498832'
        ];
        foreach ($tests as $input => $expected) {
            $value = $this->invokeMethod($sendSmsService, 'formatNumber', ['number' => $input]);
            $this->assertEquals($expected, $value);
        }

    }

    public function ztestSendMessageFromDBPass()
    {
        $faker = Faker::create();
        $notification = Notification::create([
            'type' => 'sms',
            'to' => $faker->phoneNumber(10),
            'body' => $faker->sentence
        ]);

        $expectedResponse = [
            'one' => 1,
        ];
        $returnObject = $this->setMock(MessageInstance::class);
        $returnObject->errorMessage = null;
        $returnObject->shouldReceive('toArray')
            ->andreturn($expectedResponse)
            ->once();

        $sendObject = $this->setMock(MessageList::class);
        $sendObject->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($returnObject)
            ->once();

        $twilloClient = $this->setMock(TwilioClient::class);
        $twilloClient->messages = $sendObject;
        $sendSmsService = new SendSmsService($twilloClient);

        $sendSmsService->sendMessageFromDB($notification->id);

        $notification = Notification::find($notification->id);
        $this->assertEquals('succeeded', $notification->status);
        $this->assertEquals(json_encode($expectedResponse), $notification->response);

    }

    public function testSendMessageFromDBFail()
    {
        $faker = Faker::create();
        $notification = Notification::create([
            'type' => 'sms',
            'to' => $faker->phoneNumber(10),
            'body' => $faker->sentence
        ]);

        $errorMessage = "I hate errors";

        $returnObject = $this->setMock(MessageInstance::class);
        $returnObject->errorMessage = null;
        $returnObject->shouldReceive('toArray()')
            ->andreturn([
                'errorMessage' => null
            ])
            ->never();

        $sendObject = $this->setMock(MessageList::class);
        $sendObject->shouldReceive('create')
            ->withAnyArgs()
            ->andThrow(new \Exception($errorMessage))
            ->once();

        $twilloClient = $this->setMock(TwilioClient::class);
        $twilloClient->messages = $sendObject;
        $sendSmsService = new SendSmsService($twilloClient);
        $sendSmsService->sendMessageFromDB($notification->id);

        $notification = Notification::find($notification->id);
        $this->assertEquals('failed', $notification->status);
        $this->assertEquals(null, $notification->response);

    }

}
