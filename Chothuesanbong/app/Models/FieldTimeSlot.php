<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldTimeSlot extends Model
{
    use HasFactory;

    protected $table = 'field_time_slot';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'field_id',
        'time_slot_id',
        'status',
        'custom_price',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
