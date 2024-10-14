<?php

namespace Tests\Unit;

use App\Models\Faction;
use App\Models\Equipment;
use App\Models\Character;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    private function createNonAdminUser()
    {
        return User::factory()->create(['is_admin' => 0]);
    }

    // public function test_non_admin_user_cannot_create_faction()
    // {
    //     $user = $this->createNonAdminUser();

    //     $response = $this->actingAs($user)->postJson('/api/factions', [
    //         'name' => 'Test Faction',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_update_faction()
    // {
    //     $user = $this->createNonAdminUser();
    //     $faction = Faction::factory()->create();

    //     $response = $this->actingAs($user)->putJson("/api/factions/{$faction->id}", [
    //         'name' => 'Updated Faction',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_delete_faction()
    // {
    //     $user = $this->createNonAdminUser();
    //     $faction = Faction::factory()->create();

    //     $response = $this->actingAs($user)->deleteJson("/api/factions/{$faction->id}");

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_create_equipment()
    // {
    //     $user = $this->createNonAdminUser();

    //     $response = $this->actingAs($user)->postJson('/api/equipments', [
    //         'name' => 'Test Equipment',
    //         'type' => 'Weapon',
    //         'made_by' => 'Test Corp',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_update_equipment()
    // {
    //     $user = $this->createNonAdminUser();
    //     $equipment = Equipment::factory()->create();

    //     $response = $this->actingAs($user)->putJson("/api/equipments/{$equipment->id}", [
    //         'name' => 'Updated Equipment',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_delete_equipment()
    // {
    //     $user = $this->createNonAdminUser();
    //     $equipment = Equipment::factory()->create();

    //     $response = $this->actingAs($user)->deleteJson("/api/equipments/{$equipment->id}");

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_create_character()
    // {
    //     $user = $this->createNonAdminUser();

    //     $response = $this->actingAs($user)->postJson('/api/characters', [
    //         'name' => 'Test Character',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_update_character()
    // {
    //     $user = $this->createNonAdminUser();
    //     $character = Character::factory()->create();

    //     $response = $this->actingAs($user)->putJson("/api/characters/{$character->id}", [
    //         'name' => 'Updated Character',
    //     ]);

    //     $response->assertStatus(403);
    // }

    // public function test_non_admin_user_cannot_delete_character()
    // {
    //     $user = $this->createNonAdminUser();
    //     $character = Character::factory()->create();

    //     $response = $this->actingAs($user)->deleteJson("/api/characters/{$character->id}");

    //     $response->assertStatus(403);
    // }
}
