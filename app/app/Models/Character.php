<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Character
 *
 * @property int $id
 * @property string $name
 * @property Carbon $birth_date
 * @property string $kingdom
 * @property int $equipment_id
 * @property int $faction_id
 *
 * @property Equipment $equipment
 * @property Faction $faction
 *
 * @package App\Models
 */
class Character extends Model
{

    use HasFactory;

    protected $table = 'characters';
    public $timestamps = true;

    protected $casts = [
        'birth_date' => 'datetime',
        'equipment_id' => 'int',
        'faction_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'birth_date',
        'kingdom',
        'equipment_id',
        'faction_id'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function faction()
    {
        return $this->belongsTo(Faction::class);
    }
}
