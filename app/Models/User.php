<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable // implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        // profile fields
        'display_name',
        'bio',
        'avatar_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    // Accessor: $user->avatar_url
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar_path
            ? asset('storage/' . $this->avatar_path)
            : asset('images/avatar-default.png');
    }

    /** A user has many devotionals */
    public function devotionals(): HasMany
    {
        return $this->hasMany(Devotional::class, 'user_id');
    }

    /** People who follow THIS user */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                    ->withTimestamps();
    }

    /** People THIS user is following */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
                    ->withTimestamps();
    }

    /** Is this user following the given user (by model or id)? */
    public function isFollowing(User|int $user): bool
    {
        $id = $user instanceof User ? $user->getKey() : $user;
        return $this->following()->whereKey($id)->exists();
    }

    /** Is this user followed by the given user (by model or id)? */
    public function isFollowedBy(User|int $user): bool
    {
        $id = $user instanceof User ? $user->getKey() : $user;
        return $this->followers()->whereKey($id)->exists();
    }

    /** Reads relationship */
    public function reads(): HasMany
    {
        return $this->hasMany(UserRead::class);
    }

    /** Has the user marked any devotional as read today? */
    public function hasReadToday(): bool
    {
        return $this->reads()->whereDate('read_on', today())->exists();
    }

    /** Is this user an admin */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
