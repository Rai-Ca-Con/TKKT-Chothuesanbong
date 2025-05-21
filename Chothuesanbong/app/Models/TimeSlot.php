<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeSlot extends Model
{
    use HasFactory;

    protected $table = 'time_slots';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'start_time',
        'end_time'
    ];

    public function fieldTimeSlots()
    {
        return $this->hasMany(FieldTimeSlot::class);
    }
}
