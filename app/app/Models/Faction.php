<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Faction
 *
 * @property int $id
 * @property string $faction_name
 * @property string $description
 *
 * @property Collection|Character[] $characters
 *
 * @package App\Models
 */
class Faction extends Model
{

    use HasFactory;

    protected $table = 'factions';
    public $timestamps = true;

    protected $fillable = [
        'faction_name',
        'description'
    ];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }
}
