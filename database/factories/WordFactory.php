<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WordFactory extends Factory
{
    public function definition()
    {
        return [
            'word' => $this->faker->unique()->word,
            'language' => 'en',
        ];
    }
}
