<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        $serviceGroups = [
            'Home Cleaning Services',
            'Home Repair Services', 
            'Appliance Repair Services',
            'Interior Design Services',
            'Pest Control Services',
            'Furniture Assembly Services',
            'Moving Services',
            
            'Accounting Services',
            'Legal Consultation Services',
            'Marketing Services',
            'Web Development Services',
            'Graphic Design Services',
            'Translation Services',
            'Tutoring Services',
            
            'Personal Training Services',
            'Massage Therapy Services',
            'Hair Styling Services',
            'Makeup Services',
            'Pet Care Services',
            'Photography Services',
            'Event Planning Services',
            
            // Automotive services
            'Car Repair Services',
            'Car Washing Services',
            'Tire Replacement Services',
            'Vehicle Inspection Services',
        ];

        // Cities
        $cities = ['Trnava', 'Surany'];
        
        // Get a random category or use an existing one
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();
        
        return [
            'name' => $this->faker->randomElement($serviceGroups),
            'category_id' => $category->id,
            'price' => $this->faker->numberBetween(10, 500),
            'city' => $this->faker->randomElement($cities),
            'created_at' => now(),
            'updated_at' => now(),
            'duration' => $this->faker->numberBetween(30, 120), // Duration in minutes
        ];
    }
}
