<?php

namespace Tests\Unit\Services;

use App\Jobs\SmsJob;
use App\Models\Notification;
use App\Services\QueueService;
use App\Services\SendSmsService;
use Faker\Factory as Faker;
use Tests\TestCase;
use Twilio\Rest\Client as TwilioClient;

/**
 * these flags make all tests in this class run in a separate process
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SendSmsServiceIsoalationTest extends TestCase
{

    public function testQueueNotification()
    {
        $faker = Faker::create();

        $fakeId = 100;

        $outData = $inData = [
            'type' => 'sms',
            'to' => $faker->phoneNumber(10),
            'body' => $faker->sentence
        ];
        $outData['id'] = $fakeId;
        $outData = (object)$outData;

        $client_mock = \Mockery::mock('overload:' . Notification::class);
        $client_mock->shouldReceive('create')
            ->with($inData)
            ->andReturn($outData);

        $twilloClient = $this->setMock(TwilioClient::class);
        $sendSmsService = new SendSmsService($twilloClient);

        $queueService = $this->setMock(QueueService::class);
        $queueService->shouldReceive('sendToQueue')
            ->withArgs([
                SmsJob::class, [
                    'notificationId' => $fakeId
                ]
            ])
            ->once();

        $sendSmsService->queueNotification($inData['to'], $inData['body']);
    }
}
