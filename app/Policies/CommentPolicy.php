<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    //Anyone can view the list of comments.
    public function viewAny(?User $user): bool
    {
        return true;
    }

    // Anyone can view an individual comment.
    public function view(?User $user, Comment $comment): bool
    {
        return true;
    }

    //Any authenticated user (including admin) can create comments.
    public function create(User $user): bool
    {
        return true;
    }

    //Update: admin can edit any comment; otherwise only the author can.
    public function update(User $user, Comment $comment): bool
    {
        // Admin override
        if ($user->is_admin) {
            return true;
        }

        // Otherwise only the owner of the comment
        return $comment->user_id === $user->id;
    }

    /**
     * Delete: admin can delete any comment; otherwise only the author can.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Admin override
        if ($user->is_admin) {
            return true;
        }

        // Otherwise only the owner of the comment
        return $comment->user_id === $user->id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $comment->user_id === $user->id;
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $comment->user_id === $user->id;
    }
}
