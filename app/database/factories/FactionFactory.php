<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faction>
 */
class FactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $factions = [
            'elfo',
            'enano',
            'hobbit',
            'orco',
            'troll',
            'ent',
            'hombre',
            'huargo',
            'dragón',
            'balrog',
            'uruk-hai',
            'mediano',
            'enano'
        ];
        return [
            'faction_name' => fake()->randomElement($factions),
            'description' => fake()->text(),
        ];
    }
}
