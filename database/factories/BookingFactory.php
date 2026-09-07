<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a service (not connected to a user since users don't have accounts)
        $service = Service::inRandomOrder()->first() 
            ?? Service::factory()->create();
            
        // For worker assignment, find a worker who can do this service
        $worker = User::where('role', 'worker')
            ->inRandomOrder()
            ->first() 
            ?? User::factory()->worker()->create();
        
        // Cities - matching those from ServiceFactory and UserFactory
        $cities = ['Bratislava', 'Kosice', 'Zilina', 'Nitra', 'Presov', 'Banska Bystrica', 'Trnava', 'Surany'];
        
        // Generate a random date between today and 60 days in the future
        $date = $this->faker->dateTimeBetween('now', '+60 days')->format('Y-m-d');
        
        // Generate a random start time between 8:00 and 17:00
        $startHour = $this->faker->numberBetween(8, 17);
        $startMinute = $this->faker->randomElement(['00', '15', '30', '45']);
        $startTime = sprintf('%02d:%s:00', $startHour, $startMinute);
        
        // Generate an end time 1-3 hours after start time
        $endHour = $startHour + $this->faker->numberBetween(1, 3);
        $endHour = min($endHour, 20); // Ensure we don't go beyond 8 PM
        $endMinute = $startMinute;
        $endTime = sprintf('%02d:%s:00', $endHour, $endMinute);
        
        // Generate user details (since users don't have accounts)
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $userEmail = $this->faker->safeEmail();
        
        return [
            'user_id' => $worker->id, // Assigning to worker instead of customer
            'service_id' => rand(1, $service->id), // Random service ID
            'customer_name' => $firstName . ' ' . $lastName,
            'customer_email' => $userEmail,
            'customer_phone' => $this->faker->phoneNumber(),
            'city' => $this->faker->randomElement($cities),
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'notes' => $this->faker->optional(0.6)->paragraph(1),
            'status' => 'confirmed',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Configure the booking to use the worker's details.
     */
    public function withWorkerDetails(): static
    {
        return $this->state(function (array $attributes) {
            $worker = User::find($attributes['user_id']);
            if (!$worker) {
                return [];
            }
            
            return [
                'city' => $worker->city,
            ];
        });
    }
    
    /**
     * Configure the booking for a specific service.
     */
    public function forService(Service $service): static
    {
        return $this->state(function (array $attributes) use ($service) {
            return [
                'service_id' => $service->id,
                'city' => $service->city,
            ];
        });
    }
}
