<?php

namespace App\Http\Controllers;

use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        // melakukan var_dum untuk melihat apakah query di jalankan berulang-ulang atau tidak?.
        // DB::listen(function ($query) {
        //     var_dump($query->sql);
        // });

        // mengatasi agar server tidak melakukan query berulang-ulang mengunakan igerload
        $data = Post::with(['user'])->paginate(5);

        // membuat data pagination
        // $data = Post::paginate(5);

        // cara mengcustom response
        return new PostCollection($data);

        // $data = Post::all();
        // return response()->json($data, 200);
    }

    public function show($id)
    {
        $data = Post::find($id);
        if (is_null($data)) {
            return response()->json([
                'Message' => 'Resource not found!'
            ], 404);
        }

        // cara mengcustom response
        return new PostResource($data);

        // return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'title' => ['required', 'min:5']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        // membuat data baru tanpa authentication
        // $response = Post::create($data);

        // membuat data baru dengan authentication
        $response = request()->user()->posts()->create($data);

        return response()->json($response, 201);
    }


    public function update(Request $request, Post $post)
    {
        $post->update($request->all());
        return response()->json($post, 200);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 200);
    }
}
