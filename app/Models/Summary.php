<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    /** @use HasFactory<\Database\Factories\SummaryFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'content', 
        'word_count', 
        'is_shared', 
        'format', 
        'key_points', 
        'study_recommendations'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
