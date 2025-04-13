<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name'
    ];

    public function fields()
    {
        return $this->hasMany(Field::class, 'state_id', 'id');
    }
}
