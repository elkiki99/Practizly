<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Event;
use App\Models\Topic;
use App\Models\Subject;
use App\Models\Summary;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable // implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'is_admin',
        'profile_picture',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function events()
    {
        return $this->hasManyThrough(Event::class, Subject::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Subject::class);
    }

    public function assignments()
    {
        return $this->hasManyThrough(Assignment::class, Topic::class);
    }

    public function summaries()
    {   
        return $this->hasManyThrough(Summary::class, Subject::class);
    }
}
