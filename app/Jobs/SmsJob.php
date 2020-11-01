<?php

namespace App\Jobs;

use App\Services\SendSmsService;
use Illuminate\Support\Facades\Log;

class SmsJob extends BaseJob
{
    private $notificationId;

    public function __construct($args)
    {
        $requiredKeys = ['notificationId'];
        $this->checkKeys($args, $requiredKeys);
        $this->notificationId = $args['notificationId'];

    }

    public function handle()
    {
        Log::info('SMSJob: ' . $this->notificationId);
        $smsService = $this->getSmsService();
        $smsService->sendMessageFromDB($this->notificationId);
    }

    private function getSmsService(){
        return  new SendSmsService();
    }
}
