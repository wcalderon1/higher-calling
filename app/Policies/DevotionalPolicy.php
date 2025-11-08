<?php

namespace App\Policies;

use App\Models\Devotional;
use App\Models\User;
use Illuminate\Support\Carbon;

class DevotionalPolicy
{
    // Anyone can view the list
    public function viewAny(?User $user): bool
    {
        return true;
    }

    // Guests can view published; owners can view their own drafts; admins can view anything
    public function view(?User $user, Devotional $devotional): bool
    {
        // Admin can view any devotional
        if ($user && $user->is_admin) {
            return true;
        }

        // 1) Preferred: published_at timestamp (published if not null and <= now)
        if (!is_null($devotional->published_at)) {
            if (Carbon::parse($devotional->published_at)->lessThanOrEqualTo(now())) {
                return true;
            }
        }

        // 2) Backward-compat: status string, if your table has it
        if (isset($devotional->status) && $devotional->status === 'published') {
            return true;
        }

        // Owner can view drafts/unpublished
        return $user?->id === $devotional->user_id;
    }

    public function create(User $user): bool
    {
        // Any authenticated user (including admin) can create
        return true;
    }

    public function update(User $user, Devotional $devotional): bool
    {
        // Admin can update any devotional
        if ($user->is_admin) {
            return true;
        }

        // Otherwise only the owner can update
        return $user->id === $devotional->user_id;
    }

    public function delete(User $user, Devotional $devotional): bool
    {
        // Admin can delete any devotional
        if ($user->is_admin) {
            return true;
        }

        // Otherwise only the owner can delete
        return $user->id === $devotional->user_id;
    }
}
