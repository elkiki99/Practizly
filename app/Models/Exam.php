<?php

namespace App\Models;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'title', 
        'type',
        'size',
        'difficulty',
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
