<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function create(Request $request){

        $post = new Post;
        $post->user_id = Auth::user()->id;
        $post->desc = $request->desc;

        //check if post has photo
        if($request->photo != ''){
            //choose a unique name for photo
            $photo = time().'.jpg';
//            file_put_contents('storage/posts/'.$photo,base64_decode($request->photo));
//            $post->photo = $photo;
            $post->photo = $request->photo->store('uploads/post/image');
        }
        $post->save();
        $post->user;
        $post['comments_count'] = count($post->comments);
        //likes count
        $post['likes_count'] = count($post->likes);
        //check if users liked his own post
        $post['self_like'] = false;
        foreach($post->likes as $like){
            if($like->user_id == Auth::user()->id){
                $post['self_like'] = true;
            }
        }
        return response()->json(
//            'success' => true,
//            'message' => 'posted',
//            'post' => $post
            $post
        );
    }


    public function update(Request $request){
        $post = Post::find($request->id);
        // check if user is editing his own post
        // we need to check user id with post user id
        if(Auth::user()->id != $post->user_id){
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ]);
        }
        $post->desc = $request->desc;
        $post->update();
        return response()->json([
            'success' => true,
            'message' => 'post edited'
        ]);
    }

    public function delete(Request $request){
        $post = Post::find($request->id);
        // check if user is deleting his own post
        if(Auth::user()->id !=$post->user_id){
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ]);
        }

        //check if post has photo to delete
        if($post->photo != ''){
//            Storage::delete('public/posts/'.$post->photo);
            $post->photo = "";
        }
        $post->delete();
        return response()->json([
            'success' => true,
            'message' => 'post deleted'
        ]);
    }

    public function posts(){
        $posts = Post::orderBy('id','desc')->get();
        foreach($posts as $post){
            //get user of post
            $post->user;
            //comments count
            $post['comments_count'] = count($post->comments);
            //likes count
            $post['likes_count'] = count($post->likes);
            //check if users liked his own post
            $post['self_like'] = false;
            foreach($post->likes as $like){
                if($like->user_id == Auth::user()->id){
                    $post['self_like'] = true;
                }
            }

        }

        return response()->json(
//            'success' => true,
//            'posts' => $posts
                $posts
        );
    }



    public function myPosts(){
        $posts = Post::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
        $user = Auth::user();
        return response()->json([
            "post" =>$posts,
            "user"=> $user
        ]);
    }

}
