<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'file_path', 
        'file_name', 
        'file_size', 
        'file_type', 
        'is_protected', 
        'uploaded_by'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
