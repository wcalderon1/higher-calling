<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Devotional;
use App\Models\User;
use App\Models\Tag;
use App\Models\Plan;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Always define these so the view never hits "undefined variable"
        $devotionals = collect();
        $users       = collect();
        $tags        = collect();
        $plans       = collect();

        if ($q !== '') {
            $like = '%' . $q . '%';
            $user = $request->user();

            // Devotionals search
            $devotionalsQuery = Devotional::query()
                ->with(['user', 'tags'])
                ->select('id', 'title', 'slug', 'excerpt', 'body', 'published_at', 'user_id');

            // Only admins can see unpublished devotionals
            if (! $user || ! $user->isAdmin()) {
                $devotionalsQuery->whereNotNull('published_at');
            }

            $devotionals = $devotionalsQuery
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('excerpt', 'like', $like)
                      ->orWhere('body', 'like', $like);
                })
                ->orderByDesc('published_at')
                ->limit(6)
                ->get();

            // Users search
            $users = User::query()
                ->select('id', 'display_name', 'name', 'avatar_path', 'bio')
                ->where(function ($w) use ($like) {
                    $w->where('display_name', 'like', $like)
                      ->orWhere('name', 'like', $like)
                      ->orWhere('bio', 'like', $like);
                })
                ->orderBy('display_name')
                ->limit(8)
                ->get();

            // Tags search
            $tags = Tag::query()
                ->select('id', 'name', 'slug', 'description')
                ->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                      ->orWhere('description', 'like', $like);
                })
                ->orderBy('name')
                ->limit(8)
                ->get();

            // Plans search – your table has slug + description
            $plans = Plan::query()
                ->select('id', 'slug', 'description')
                ->where(function ($w) use ($like) {
                    $w->where('slug', 'like', $like)
                      ->orWhere('description', 'like', $like);
                })
                ->orderBy('slug')
                ->limit(6)
                ->get();
        }

        return view('search.index', [
            'q'           => $q,
            'devotionals' => $devotionals,
            'users'       => $users,
            'tags'        => $tags,
            'plans'       => $plans,
        ]);
    }
}
