<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\CharacterController;
use App\Models\Character;
use App\Models\Equipment;
use App\Models\Faction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\TestCase;

class CharacterTest extends TestCase
{
    use RefreshDatabase;

    protected CharacterController $characterController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->characterController = new CharacterController();
    }

    public function test_relationships()
    {
        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();
        $character = Character::create([
            'name' => 'Test Character',
            'birth_date' => now(),
            'kingdom' => 'Test Kingdom',
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $this->assertEquals($equipment->id, $character->equipment->id);
        $this->assertEquals($faction->id, $character->faction->id);
    }

    public function test_index()
    {
        $this->actingAs(User::factory()->create(['is_admin' => 0]));

        $equipment1 = Equipment::factory()->create();
        $faction1 = Faction::factory()->create();
        $character1 = Character::create([
            'name' => 'Character One',
            'birth_date' => now(),
            'kingdom' => 'Kingdom One',
            'equipment_id' => $equipment1->id,
            'faction_id' => $faction1->id,
        ]);

        $equipment2 = Equipment::factory()->create();
        $faction2 = Faction::factory()->create();
        $character2 = Character::create([
            'name' => 'Character Two',
            'birth_date' => now(),
            'kingdom' => 'Kingdom Two',
            'equipment_id' => $equipment2->id,
            'faction_id' => $faction2->id,
        ]);

        $response = $this->getJson('/api/v1/characters');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['name' => 'Character One'])
                 ->assertJsonFragment(['name' => 'Character Two']);
    }

    public function test_show()
    {
        $this->actingAs(User::factory()->create(['is_admin' => 0]));

        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();
        $character = Character::create([
            'name' => 'Show Character',
            'birth_date' => now(),
            'kingdom' => 'Show Kingdom',
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $response = $this->getJson("/api/v1/characters/{$character->id}");

        $response->assertStatus(JsonResponse::HTTP_OK)
                 ->assertJsonFragment([
                     'id' => $character->id,
                     'name' => $character->name,
                     'kingdom' => $character->kingdom,
                     'equipment' => $equipment->toArray(),
                     'faction' => $faction->toArray(),
                 ]);
    }

    public function test_destroy()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();
        $character = Character::create([
            'name' => 'Character One',
            'birth_date' => now(),
            'kingdom' => 'Kingdom One',
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $response = $this->characterController->destroy($character->id);

        $this->assertEquals(204, $response->status());
        $this->assertSoftDeleted($character);
    }

    public function test_restore()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();
        $character = Character::factory()->create([
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
            'deleted_at' => now()
        ]);

        $response = $this->characterController->restore($character->id);

        $this->assertEquals(200, $response->status());
        $this->assertNotSoftDeleted($character);
    }

    public function test_force_delete()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();
        $character = Character::factory()->create([
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
            'deleted_at' => now()
        ]);

        $response = $this->characterController->forceDelete($character->id);

        $this->assertEquals(204, $response->status());
        $this->assertDatabaseMissing('characters', $character->toArray());
    }
}
