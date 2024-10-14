<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Faction;
use App\Policies\FactionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactionPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected FactionPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new FactionPolicy();
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
        $faction = Faction::factory()->create(['deleted_at' => now()]);

        $this->assertTrue($this->policy->restore($adminUser, $faction));
    }

    public function test_restore_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $faction = Faction::factory()->create(['deleted_at' => now()]);

        $this->assertFalse($this->policy->restore($user, $faction));
    }

    public function test_force_delete_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);
        $faction = Faction::factory()->create(['deleted_at' => now()]);

        $this->assertTrue($this->policy->forceDelete($adminUser, $faction));
    }

    public function test_force_delete_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $faction = Faction::factory()->create(['deleted_at' => now()]);

        $this->assertFalse($this->policy->forceDelete($user, $faction));
    }
}
