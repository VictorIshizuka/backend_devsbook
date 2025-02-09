<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;

class PostController extends Controller
{
    private $loggedUser;
    public function __construct()
    {

        // $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function like($id)
    {

        $array = ['error' => ''];
        $post = Post::find($id);
        if ($post) {
            $isLiked = PostLike::where('id_post', $id)->where('id_user', $this->loggedUser['id'])->count();

            if ($isLiked > 0) {
                PostLike::where('id_post', $id)->where('id_user', $this->loggedUser['id'])->delete();
                $array['isLiked'] = false;
                // dd($isLiked );
            } else {
                $like = new PostLike();
                $like->id_post = $id;
                $like->id_user = $this->loggedUser['id'];
                $like->created_at = date('Y-m-d H:i:s');
                $like->save();
                $array['isLiked'] = true;
            }
            $likeCount = PostLike::where('id_post', $id)->count();
            $array['likeCount'] = $likeCount;
        } else {
            $array['error'] = "Post inexistente!";
            return $array;
        }
        return      $array;
    }

    public function comment($id, Request $r)
    {
        $array = ['error' => ''];
        $txt = $r->input('txt');
        $post = Post::find($id);
        // dd($txt, $this->loggedUser);
        if ($post) {
            if ($txt) {
                $comment = new PostComment();
                $comment->id_post = $id;
                $comment->id_user = $this->loggedUser['id'];
                $comment->body = $txt;
                $comment->created_at = date('Y-m-d H:i:s');
                $comment->save();
            } else {
                $array['error'] = "Não enviou mensagem!";
                return $array;
            }
        } else {
            $array['error'] = "Post inexistente!";
            return $array;
        }
        return $array;
    }
}
