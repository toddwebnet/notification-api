<?php

namespace App\Services;

use App\Jobs\SmsJob;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class SendSmsService
{
    private $twilioClient;
    private $fromNumber;
    private $lastError = null;

    public function __construct(TwilioClient $twilioClient = null)
    {
        $this->fromNumber = env('TWILIO_FROM_NUMBER');
        if ($twilioClient === null) {
            Log::info('creating');
            $this->twilioClient = new TwilioClient(
                env('TWILIO_SID'),
                env('TWILIO_TOKEN')
            );
        } else {
            $this->twilioClient = $twilioClient;
        }
    }

    public function queueNotification($cellNumber, $message)
    {
        $notification = Notification::create([
            'type' => 'sms',
            'to' => $cellNumber,
            'body' => $message
        ]);
        QueueService::instance()->sendToQueue(
            SmsJob::class, [
                'notificationId' => $notification->id
            ]
        );
        return $notification;

    }

    public function sendMessageFromDB($id)
    {
        Log::info("SMS::sendMessageFromDB: {$id}");

        $notification = Notification::find($id);
        $response = $this->sendMessage($notification->to, $notification->body);
        if ($response === null) {
            $notification->status = 'failed';
        } else {
            $notification->status = 'succeeded';
            $notification->response = json_encode($response);
        }
        $notification->save();

    }

    private function sendMessage($cellNumber, $message)
    {
        Log::info("SMS::sendMessage: {$cellNumber}");
        $this->lastError = '';
        try {
            $return = $this->twilioClient->messages->create(
                $this->formatNumber($cellNumber),
                [
                    'from' => $this->formatNumber($this->fromNumber),
                    'body' => $message
                ]);
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            return null;
        }
        return $return->toArray();
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    private function formatNumber($number)
    {
        $keeps = str_split("0123456789");
        $value = '';
        foreach (str_split($number) as $char) {
            if (in_array($char, $keeps)) {
                $value .= $char;
            }
            if ($char == 'x' || $char == 'X') {
                break;
            }

        }
        if (strlen($value) <= 10) {
            $value = "1{$value}";
        }
        $value = "+{$value}";

        return $value;
    }

}
