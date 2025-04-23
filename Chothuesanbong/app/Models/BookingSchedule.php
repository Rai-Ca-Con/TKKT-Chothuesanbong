<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BookingSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'booking_schedule';

    protected $fillable = [
        'id',
        'user_id',
        'field_id',
        'date_start',
        'date_end',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'booking_id');
    }
}
