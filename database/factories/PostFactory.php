<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{

    public function definition()
    {
        return [
            'from' => fake()->name(),
            'body' => fake()->paragraph(),
            'subject' => fake()->sentence(),
            'location' => fake()->city(),
            'created_at' => fake()->datetime(),
        ];
    }
}
