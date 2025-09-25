<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Devotional;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Devotional $devotional)
    {
        // Only users who can view the post may comment (published or owner)
        $this->authorize('view', $devotional);

        $data = $request->validate([
            'body' => ['required','string','max:1000'],
        ]);

        Comment::create([
            'devotional_id' => $devotional->id,
            'user_id'       => $request->user()->id,
            'body'          => $data['body'],
        ]);

        return redirect()
            ->route('devotionals.show', $devotional)
            ->with('ok','Comment posted.');
    }

    public function destroy(Devotional $devotional, Comment $comment)
{
    $this->authorize('delete', $comment);

    $comment->delete();

    return redirect()
        ->route('devotionals.show', $devotional)
        ->with('ok', 'Comment deleted.');
}

}
