<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id', 
        'name', 
        'description', 
        'order', 
        'difficulty', 
        'estimated_study_time', 
        'status'
    ];
}
