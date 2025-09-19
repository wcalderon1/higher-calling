<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): bool
    {
        // Comment author OR devotional author may delete
        return $user->id === $comment->user_id
            || $user->id === $comment->devotional->user_id;
    }
}
