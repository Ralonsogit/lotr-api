<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $equipmentName = [
            'mandoble',
            'daga',
            'hacha de batalla',
            'lanza',
            'maza',
            'martillo de guerra',
            'arco corto',
            'ballesta',
            'estoque',
            'alabarda',
            'cota de malla',
            'armadura de cuero',
            'armadura de placas',
            'armadura de escamas',
            'armadura acolchada',
            'armadura de anillas',
            'brigantina',
            'cuero tachonado',
            'media armadura',
            'coraza',
            'escudo redondo',
            'escudo torre',
            'broquel',
            'escudo cometa',
            'escudo de heraldo'
        ];
        $equipmentType = ['arma',
            'armadura',
            'escudo'
        ];
        return [
            'name' => fake()->randomElement($equipmentName),
            'type' => fake()->randomElement($equipmentType),
            'made_by' => fake()->firstName(),
        ];
    }
}
