<?php

namespace App\Models;

use App\Models\Topic;
use App\Models\Assignment;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


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
        'is_favorite'
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class, 'subject_id');
    }
}
