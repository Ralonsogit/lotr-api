<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\FactionController;
use App\Http\Requests\FactionRequest;
use App\Models\Faction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\TestCase;

class FactionTest extends TestCase
{
    use RefreshDatabase;

    protected FactionController $factionController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factionController = new FactionController();
    }

    /** @test */
    public function test_index()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $response = $this->getJson("/api/v1/factions");

        $this->assertEquals(JsonResponse::HTTP_OK, $response->status());
    }

    /** @test */
    public function test_show()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $faction = Faction::factory()->create();

        Cache::shouldReceive('remember')->once()->andReturn($faction);

        $response = $this->factionController->show($faction->id);

        $this->assertEquals(200, $response->status());
        $this->assertEquals($faction->id, $response->getData()->id);
    }

    /** @test */
    public function test_destroy()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $faction = Faction::factory()->create();

        $response = $this->factionController->destroy($faction->id);

        $this->assertEquals(204, $response->status());
        $this->assertSoftDeleted($faction);
    }

    /** @test */
    public function test_restore()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $faction = Faction::factory()->create(['deleted_at' => now()]);

        $response = $this->factionController->restore($faction->id);

        $this->assertEquals(200, $response->status());
        $this->assertNotSoftDeleted($faction);
    }

    /** @test */
    public function test_force_delete()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        Auth::login($user);

        $faction = Faction::factory()->create(['deleted_at' => now()]);

        $response = $this->factionController->forceDelete($faction->id);

        $this->assertEquals(204, $response->status());
        $this->assertDatabaseMissing('factions', $faction->toArray());
    }
}
