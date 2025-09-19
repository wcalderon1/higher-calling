<?php

namespace App\Policies;

use App\Models\Devotional;
use App\Models\User;

class DevotionalPolicy
{
    /**
     * Anyone (even guests) can view the list page.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Guests can view only published. Owners can view their own drafts/scheduled.
     */
    public function view(?User $user, Devotional $devotional): bool
    {
        if ($devotional->status === 'published') {
            return true;
        }
        return $user?->id === $devotional->user_id;
    }

    /**
     * Any authenticated user can create.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the owner can update.
     */
    public function update(User $user, Devotional $devotional): bool
    {
        return $user->id === $devotional->user_id;
    }

    /**
     * Only the owner can delete.
     */
    public function delete(User $user, Devotional $devotional): bool
    {
        return $user->id === $devotional->user_id;
    }
}
