<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a worker user and service
        $worker = User::where('role', 'worker')->inRandomOrder()->first() 
            ?? User::factory()->create(['role' => 'worker']);
        
        $service = Service::inRandomOrder()->first() 
            ?? Service::factory()->create();
        
        // Status options for schedules
        $statuses = ['available', 'booked', 'completed', 'cancelled'];
        
        // Cities - matching those from ServiceFactory and UserFactory
        $cities = ['Bratislava', 'Kosice', 'Zilina', 'Nitra', 'Presov', 'Banska Bystrica', 'Trnava', 'Surany'];
        
        // Generate a random date between today and 30 days in the future
        $date = $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d');
        
        // Generate a random start time between 8:00 and 17:00
        $startHour = $this->faker->numberBetween(8, 17);
        $startMinute = $this->faker->randomElement(['00', '15', '30', '45']);
        $startTime = sprintf('%02d:%s:00', $startHour, $startMinute);
        
        // Generate an end time 1-3 hours after start time
        $endHour = $startHour + $this->faker->numberBetween(1, 3);
        $endHour = min($endHour, 20); // Ensure we don't go beyond 8 PM
        $endMinute = $startMinute;
        $endTime = sprintf('%02d:%s:00', $endHour, $endMinute);
        
        return [
            'user_id' => $worker->id,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->faker->randomElement($statuses),
            'city' => $this->faker->randomElement($cities),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Indicate that the schedule is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }
    
    /**
     * Indicate that the schedule is booked.
     */
    public function booked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'booked',
        ]);
    }
}
