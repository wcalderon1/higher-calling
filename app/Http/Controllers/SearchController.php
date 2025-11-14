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
        $devotionals = collect();
        $users = collect();
        $tags = collect();
        $plans = collect();

        if ($q !== '') {
            $like = '%'.$q.'%';

            // Devotionals search
            $devotionals = Devotional::query()
                ->with(['user:id,display_name,name', 'tags:id,name,slug'])
                ->select('id', 'title', 'slug', 'excerpt', 'body', 'published_at', 'user_id', 'created_at')
                // non-admins only see published devotionals
                ->when(!(Auth::check() && Auth::user()->isAdmin()), function ($q2) {
                    $q2->whereNotNull('published_at')
                       ->where('published_at', '<=', now());
                })
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('excerpt', 'like', $like)
                      ->orWhere('body', 'like', $like);
                })
                ->latest('published_at')
                ->limit(10)
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

            // Plans search
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
