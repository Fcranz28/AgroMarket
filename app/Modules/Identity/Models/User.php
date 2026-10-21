<?php

namespace App\Modules\Identity\Models;

use App\Modules\Identity\Database\Factories\UserFactory;
use App\Modules\Identity\Enums\UserRole;
use App\Modules\Identity\Enums\VerificationStatus;
use App\Modules\Identity\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property UserRole $role
 * @property VerificationStatus $verification_status
 */
#[UseFactory(UserFactory::class)]
#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable
{
    /** @use HasFactory<\App\Modules\Identity\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
            'role' => UserRole::class,
            'verification_status' => VerificationStatus::class,
        ];
    }

    /**
     * Relations owned by other modules (reportsReceived) are registered by those modules
     * with User::resolveRelationUsing(), so Identity does not depend on them.
     *
     * @return HasMany<Address, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function isFarmer(): bool
    {
        return $this->role === UserRole::Farmer;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::Approved;
    }

    /**
     * @param  Builder<User>  $query
     */
    public function scopeFarmers(Builder $query): void
    {
        $query->where('role', UserRole::Farmer);
    }

    /**
     * @param  Builder<User>  $query
     */
    public function scopePendingVerification(Builder $query): void
    {
        $query->where('verification_status', VerificationStatus::Pending);
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
