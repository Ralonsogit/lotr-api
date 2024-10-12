<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Equipment
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $made_by
 *
 * @property Collection|Character[] $characters
 *
 * @package App\Models
 */
class Equipment extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'equipments';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'type',
        'made_by'
    ];

    public function characters()
    {
        return $this->hasMany(Character::class);
    }
}
