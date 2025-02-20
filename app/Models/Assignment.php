<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'title', 
        'description', 
        'due_date', 
        'status', 
        'priority', 
        'type', 
        'score'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
