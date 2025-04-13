<?php

namespace Database\Factories;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'id' => fake()->numberBetween(1, 10000), 
            'name' => fake()->company(), // hoặc ->name() nếu là tên người
            'address' => fake()->address(),
            'category_id' => fake()->numberBetween(1, 5), // giả định có 5 category
            'price' => fake()->randomFloat(2, 100000, 1000000), // giá từ 100 đến 10000
            'description' => fake()->paragraph(),
            'state_id' => fake()->numberBetween(1, 3), // giả định có 10 state
        ];
    }
}
