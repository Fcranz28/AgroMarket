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

    const ROLE_USER = 'user';
    const ROLE_FARMER = 'farmer';
    const ROLE_ADMIN = 'admin';

    const STATUS_NONE = 'none';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_status',
        'verification_document',
        'is_banned',
        'onboarding_completed',
        'dni_front',
        'dni_back',
        'face_photo',
        'phone',
        'dni',
        'avatar',
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
            'is_banned' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function reportsReceived()
    {
        return $this->hasManyThrough(Report::class, Product::class);
    }

    // Helpers
    public function isFarmer(): bool
    {
        return $this->role === self::ROLE_FARMER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isVerified(): bool
    {
        return $this->verification_status === self::STATUS_APPROVED;
    }

    // Scopes
    public function scopeFarmers($query)
    {
        return $query->where('role', self::ROLE_FARMER);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', self::STATUS_PENDING);
    }

    // Accessors
    public function getFirstnameAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return $parts[0];
    }

    public function getLastnameAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }
}
