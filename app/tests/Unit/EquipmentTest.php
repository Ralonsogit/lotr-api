<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\EquipmentController;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    protected EquipmentController $equipmentController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->equipmentController = new EquipmentController();
    }

    /** @test */
    public function test_index()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->getJson("/api/v1/equipments");

        $this->assertEquals(JsonResponse::HTTP_OK, $response->status());
    }

    /** @test */
    public function test_show()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $equipment = Equipment::factory()->create();

        Cache::shouldReceive('remember')->once()->andReturn($equipment);

        $response = $this->equipmentController->show($equipment->id);

        $this->assertEquals(200, $response->status());
        $this->assertEquals($equipment->id, $response->getData()->id);
    }

    /** @test */
    public function test_destroy()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $equipment = Equipment::factory()->create();

        $response = $this->equipmentController->destroy($equipment->id);

        $this->assertEquals(204, $response->status());
        $this->assertSoftDeleted($equipment);
    }

    /** @test */
    public function test_restore()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $equipment = Equipment::factory()->create(['deleted_at' => now()]);

        $response = $this->equipmentController->restore($equipment->id);

        $this->assertEquals(200, $response->status());
        $this->assertNotSoftDeleted($equipment);
    }

    /** @test */
    public function test_force_delete()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $equipment = Equipment::factory()->create(['deleted_at' => now()]);

        $response = $this->equipmentController->forceDelete($equipment->id);

        $this->assertEquals(204, $response->status());
        $this->assertDatabaseMissing('equipments', $equipment->toArray());
    }
}
