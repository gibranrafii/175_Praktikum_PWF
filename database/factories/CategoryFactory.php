<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Elektronik', 'Fashion', 'Makanan dan minuman', 'Kesehatan', 'Otomotif', 'Perabotan', 'Olahraga']),
            'product_id' => \App\Models\Product::factory(),
        ];
    }
}
