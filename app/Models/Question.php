<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'end_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        self::created(function($model){
            // ... code here
        });
    }

    public function files()
    {
        return $this->hasMany(File::class)->where('is_protocol', false);
    }

    public function protocolFiles()
    {
        return $this->hasMany(File::class)->where('is_protocol', true);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'question_user');
    }
}
