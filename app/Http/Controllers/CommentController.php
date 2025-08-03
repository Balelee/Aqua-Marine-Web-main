<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{


    public function getCommentsByProduct($productId){
    $comments = Comment::where('product_id', $productId)->get();
    $contents = $comments->pluck('content');
    return response()->json([
        'content' => $contents,
    ]);
}

    // store un commentaire
public function storeComment(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'product_id' => 'required|integer|exists:product,product_id',
        'content' => 'required|string'
    ]);

    $comment = Comment::create([
        'user_id' => $request->user_id,
        'product_id' => $request->product_id,
        'content' => $request->content,
    ]);

    return response()->json([
        'content' => $comment->content
        ]
    );
}

}
