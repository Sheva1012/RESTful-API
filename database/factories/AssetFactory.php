<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'asset_code'    => 'AST-' . fake()->unique()->numerify('####'),
            'name'          => fake()->words(3, true),
            'category'      => fake()->randomElement(['Elektronik', 'Kendaraan', 'Peralatan Kantor', 'Dokumentasi']),
            'status'        => 'available',
            'condition'     => 'Bagus',
            'description'   => fake()->sentence(),
            'purchase_date' => fake()->date(),
        ];
    }
}

