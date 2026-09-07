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
    
        // Common service categories
        $categories = [
            'Cleaning',
            'Plumbing',
            'Electrical',
            'Gardening',
            'Home Renovation',
            'Moving',
            'IT Services',
            'Beauty & Wellness',
            'Education & Tutoring',
            'Automotive',
            'Pet Care',
            'Event Planning',
            'Legal Services',
            'Photography',
            'Handyman'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($categories) . ' Services',
            'city' => $this->faker->randomElement(['Trnava', 'Surany']),
            'created_at' => now(),
            'updated_at' => now(),
        ];

    }
}
