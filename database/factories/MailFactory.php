<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mail>
 */
class MailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cc' => [fake()->email()],
            'to' => [fake()->email()],
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'location' => fake()->city(),
        ];
    }
}
