<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Character;
use App\Models\Equipment;
use App\Models\Faction;
use App\Policies\CharacterPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\FactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
