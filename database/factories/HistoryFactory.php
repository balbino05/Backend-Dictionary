<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class HistoryFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'word' => $this->faker->word(),
            'searched_at' => now(),
        ];
    }
}
