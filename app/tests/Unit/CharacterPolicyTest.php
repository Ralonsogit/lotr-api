<?php

namespace Tests\Unit;

use App\Models\Character;
use App\Models\Equipment;
use App\Models\Faction;
use App\Models\User;
use App\Policies\CharacterPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CharacterPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected CharacterPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CharacterPolicy();
    }

    public function test_view_any()
    {
        $this->assertTrue($this->policy->viewAny());
    }

    public function test_view()
    {
        $this->assertTrue($this->policy->view());
    }

    public function test_create_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);
        $this->assertTrue($this->policy->create($adminUser));
    }

    public function test_create_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);
        $this->assertTrue($this->policy->update($adminUser));
    }

    public function test_update_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $this->assertFalse($this->policy->update($user));
    }

    public function test_delete_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);
        $this->assertTrue($this->policy->delete($adminUser));
    }

    public function test_delete_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $this->assertFalse($this->policy->delete($user));
    }

    public function test_restore_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);

        // Create equipment and faction
        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();

        // Create character as soft-deleted with required IDs
        $character = Character::factory()->create([
            'deleted_at' => now(),
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $this->assertTrue($this->policy->restore($adminUser, $character));
    }

    public function test_restore_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);

        // Create equipment and faction
        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();

        // Create character as soft-deleted with required IDs
        $character = Character::factory()->create([
            'deleted_at' => now(),
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $this->assertFalse($this->policy->restore($user, $character));
    }

    public function test_force_delete_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);

        // Create equipment and faction
        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();

        // Create character as soft-deleted with required IDs
        $character = Character::factory()->create([
            'deleted_at' => now(),
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $this->assertTrue($this->policy->forceDelete($adminUser, $character));
    }

    public function test_force_delete_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);

        // Create equipment and faction
        $equipment = Equipment::factory()->create();
        $faction = Faction::factory()->create();

        // Create character as soft-deleted with required IDs
        $character = Character::factory()->create([
            'deleted_at' => now(),
            'equipment_id' => $equipment->id,
            'faction_id' => $faction->id,
        ]);

        $this->assertFalse($this->policy->forceDelete($user, $character));
    }
}
