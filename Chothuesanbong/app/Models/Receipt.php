<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Receipt extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'booking_id',
        'date',
        'total_price',
        'deposit_price',
        'status',
        'expired_at'
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

    public function booking()
    {
        return $this->belongsTo(BookingSchedule::class, 'booking_id');
    }
}
