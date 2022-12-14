<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draft>
 */
class DraftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'from' => fake()->name(),
            'to' => [fake()->email()],
            'cc' => [fake()->email()],
            'message' => fake()->paragraph(),
            'location' => fake()->city(),
        ];
    }
}
