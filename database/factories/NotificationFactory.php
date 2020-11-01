<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => 'sms',
            'to' => $this->faker->phoneNumber,
            'from' => $this->faker->phoneNumber,
            'subject' => $this->faker->sentence,
            'body' => $this->faker->sentence,
        ];
    }
}
