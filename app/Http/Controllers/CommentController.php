<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Quotes\QuoteVersion;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $quote = QuoteVersion::findOrFail($request->id);

        $comments = $quote->comments->whereNull('response_id')->map(
            function ($comment) {
                return [
                    'user_photo' => $comment->user->photo,
                    'user_name' => $comment->user->short_name,
                    'user_id' => $comment->user->id,
                    'message' => $comment['message'],
                    'responses' => $comment->comments->map(
                        function ($comment) {
                            return [ 
                                'user_photo' => $comment->user->photo,
                                'user_name' => $comment->user->short_name,
                                'user_id' => $comment->user->id,
                                'message' => $comment['message'],
                                'id' => $comment['id'],
                                'date' => $comment['created_at']
                            ];
                        }
                    ),
                    'id' => $comment['id'],
                    'date' => $comment['created_at']
                ];
            }
        )->toArray();

        return response()->json(['comments' => $comments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in stor'age.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'commentable_id' => 'required|numeric',
            'message' => 'required',
            'response_id' => 'nullable'
        ]);
        $validateData['commentable_type'] = !isset($request->response_id) ? 'App\Models\Quotes\QuoteVersion' : 'App\Models\Comment';
        $validateData['user_id'] = auth()->user()->id;
        try {
            Comment::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request) {

    // $validateData = $request->validate([
    //     'commentable_id' => 'required|numeric',
    //     'message' => 'required',
    //     'response_id' => 'nullable'
    // ]);

    // if ($request->has('comment_id')) {

    //     $comment = Comment::findOrFail($request->comment_id);

    //     if ($comment->user_id != auth()->user()->id) {
    //         return back()->withErrors('message', 'No tienes permiso para editar este comentario.');
    //     }

    //     $comment->update($validateData);

    //     } else {
    //         $validateData['commentable_type'] = !isset($request->response_id) ? 'App\Models\Quotes\QuoteVersion' : 'App\Models\Comment';
    //         $validateData['user_id'] = auth()->user()->id;

    //         try {
    //             Comment::create($validateData);
    //         } catch (Exception $e) {
    //             return back()->withErrors('message', 'Ocurrió un error al crear: ' . $e->getMessage());
    //         }
    //     }

    //     return response()->json(['comment' => $comment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        $validateData = $request->validate([
            'message' => 'required',
        ]);
        try {
            $comment->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        try {
            $comment->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
