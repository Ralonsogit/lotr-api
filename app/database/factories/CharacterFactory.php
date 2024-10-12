<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\Equipment;
use App\Models\Faction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kingdoms = [
            'Mordor',
            'Hobbiton',
            'Rohan',
            'Gondor',
            'Rivendel',
            'Isengard',
            'El Bosque Negro',
            'Erebor',
            'La Comarca',
            'Minas Tirith',
            'Minas Morgul',
        ];
        $equipments = Equipment::pluck('id');
        $factions = Faction::pluck('id');
        return [
            'name' => fake()->firstName(),
            'birth_date' => fake()->date(),
            'kingdom' => fake()->randomElement($kingdoms),
            'equipment_id' => fake()->randomElement($equipments),
            'faction_id' => fake()->randomElement($factions),
        ];
    }
}