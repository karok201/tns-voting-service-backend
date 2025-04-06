<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'path', 'question_id', 'is_protocol'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
