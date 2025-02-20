<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'name',
        'description', 
        'color', 
        'goal', 
        'completion_percentage', 
        'last_studied_at', 
        'is_favorite'
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
