<?php

namespace App\Http\Controllers;

use App\Services\SendSmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    public function sendMessage(Request $request)
    {
        $cellNumber = $request->post('cell-number');
        $message = $request->post('message');
        if ($cellNumber === null || $message === null) {
            return response('Required Fields: cell-number and message', 500);
        }

        $service = new SendSmsService();
        $notification = $service->queueNotification($cellNumber, $message);
        return response($notification, 200);
    }
}
