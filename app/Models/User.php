<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'login',
        'password',
        'address',
        'isAdmin',
    ];
    public function results()
    {
        return $this->hasMany(Results::class);
    }
    public function topics() 
    {
        return $this->belongsToMany(NewsletterTopic::class, 'newsletter_topic_user');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
    public function commentsToReviews() {
        return $this->hasMany(CommentToReview::class);
    }
    public function messages() {
        return $this->hasMany(Messages::class);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
}
