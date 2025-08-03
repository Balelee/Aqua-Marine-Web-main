<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReactionController extends Controller
{


    public function getReactionStatus(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'product_id' => 'required|integer|exists:product,product_id',
    ]);

    $userReaction = Reaction::where('user_id', $request->user_id)
        ->where('product_id', $request->product_id)
        ->first();

    $likeCount = Reaction::where('product_id', $request->product_id)
        ->where('type', 'like')
        ->count();

    return response()->json([
        'reaction' => $userReaction ? $userReaction->type : null,
        'like_count' => $likeCount,
    ]);
}

    public function react(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'product_id' => 'required|integer|exists:product,product_id',
            'type' => 'required|in:like,dislike',
        ]);
        $userId = $request->user_id;
        $reaction = Reaction::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $request->product_id,
            ],
            [
                'type' => $request->type,
            ]
        );
        $likeCount = Reaction::where('product_id', $request->product_id)
            ->where('type', 'like')
            ->count();

        return response()->json([
            'reaction' => $reaction->type,
            'like_count' => $likeCount,
        ]);
    }

}
