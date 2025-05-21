<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Field extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'fields';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'longitude',
        'latitude',
        'address',
        'category_id',
        'state_id',
        'price',
        'description',
        'deleted_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Nếu chưa có id → tự động tạo UUID
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }


    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'field_id', 'id');
    }

    public function fieldTimeSlots()
    {
        return $this->hasMany(FieldTimeSlot::class);
    }
}
