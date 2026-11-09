<?php

namespace Database\Factories;

use App\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowerFactory extends Factory
{
    protected $model = Borrower::class;

    public function definition(): array
    {
        return [
            'borrower_code' => 'EMP-' . fake()->unique()->numerify('####'),
            'name'          => fake()->name(),
            'department'    => fake()->randomElement(['IT Support', 'Quality Control', 'Operasional', 'Marketing']),
            'phone'         => fake()->phoneNumber(),
            'email'         => fake()->unique()->safeEmail(),
        ];
    }
}

