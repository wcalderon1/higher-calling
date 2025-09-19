<?php

namespace App\Providers;

use App\Models\Devotional;
use App\Models\Comment;
use App\Policies\DevotionalPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Devotional::class => DevotionalPolicy::class,
        Comment::class    => CommentPolicy::class,
    ];

    public function boot(): void {}
}
