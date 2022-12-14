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
            'from' => "Yours,\n" . fake()->name(),
            'to' => [fake()->email()],
            'cc' => [fake()->email()],
            'message' => fake()->name() . ",\n\n" . fake()->paragraph(),
            'location' => fake()->city(),
        ];
    }
}
