<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Equipment;
use App\Policies\EquipmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected EquipmentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new EquipmentPolicy();
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
        $equipment = Equipment::factory()->create(['deleted_at' => now()]);

        $this->assertTrue($this->policy->restore($adminUser, $equipment));
    }

    public function test_restore_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $equipment = Equipment::factory()->create(['deleted_at' => now()]);

        $this->assertFalse($this->policy->restore($user, $equipment));
    }

    public function test_force_delete_as_admin()
    {
        $adminUser = User::factory()->create(['is_admin' => 1]);
        $equipment = Equipment::factory()->create(['deleted_at' => now()]);

        $this->assertTrue($this->policy->forceDelete($adminUser, $equipment));
    }

    public function test_force_delete_as_non_admin()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $equipment = Equipment::factory()->create(['deleted_at' => now()]);

        $this->assertFalse($this->policy->forceDelete($user, $equipment));
    }
}
