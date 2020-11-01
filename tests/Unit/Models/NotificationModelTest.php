<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use Faker\Factory as Faker;
use Tests\TestCase;

class NotificationModelTest extends TestCase
{

    public function testCreatePass()
    {
        $faker = Faker::create();
        $datas = [

            [
                'type' => 'sms',
                'to' => $faker->phoneNumber(10),
                'body' => $faker->sentence
            ],
            [
                'type' => 'email',
                'to' => $faker->email,
                'from' => $faker->email,
                'subject' => $faker->sentence,
                'body' => implode('  ', $faker->sentences(3)),
            ],
        ];
        foreach ($datas as $data) {
            $model = Notification::create($data);
            foreach ($model->toArray() as $key => $value) {
                if ($key == 'id') {
                    $this->assertIsNumeric($value);
                } else {
                    if (array_key_exists($key, $data)) {
                        $this->assertEquals($data[$key], $value);
                    } else {
                        if (!in_array($key, ['updated_at', 'created_at'])) {
                            $this->assertNull($value);
                        }
                    }
                }
            }
            $model->delete();
        }

    }

    public function testCreateFail()
    {
        $faker = Faker::create();
        $datas = [

            [
                'type' => 'junk',
                'to' => $faker->phoneNumber,
                'body' => $faker->sentence
            ],
            [
                'type' => 'email',
            ],
        ];
        foreach ($datas as $data) {
            try {
                $model = Notification::create($data);
                $this->assertEquals('Failed to catch error', null);

            } catch (\Exception $e) {
                $this->assertEquals('Successful Error Catch', 'Successful Error Catch');
            }
        }

    }
}
