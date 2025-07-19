<?php

namespace Tests\Support\Models\Factories;

use Tests\Support\Models\DummyModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class DummyModelFactory extends Factory
{
    protected $model = DummyModel::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug,
        ];
    }
}