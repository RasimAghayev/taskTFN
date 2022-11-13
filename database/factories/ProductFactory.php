<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $category=Category::inRandomOrder()->first();
        return [
            'category_id'=>$category->id,
            'name'=>$this->faker->name(),
            'description'=>$this->faker->word(5),
            'price'=>$this->faker->randomFloat(2,1000,9999),
            'image'=>$this->faker->imageUrl(640,480,'product',true),
            'status'=>$this->faker->randomElement(['0','1']),
        ];
    }
}
