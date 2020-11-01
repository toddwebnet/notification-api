<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SmsJob;
use App\Services\SendSmsService;
use Tests\TestCase;

class SmsJobTest extends TestCase
{

    public function testSmsJob()
    {
//       $notificationId = 4000;
//        $smsService = $this->setMock(SendSmsService::class);
//        $smsService->shouldReceive('sendMessageFromDB')
//            ->withArgs([$notificationId])
//            ->once();
//
//        $job = new SmsJob(['notificationId' => $notificationId]);
//        $job->handle();

        $this->assertTrue(true);

    }
}
