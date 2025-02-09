<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\User;
use App\Models\UserRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;

class FeedController extends Controller
{
    public function create(Request $r)
    {
        $array = ['error' => ''];

        $type = $r->input('type');
        $body = $r->input('body');
        $photo = $r->file('photo');
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        if ($type) {
            switch ($type) {
                case 'text':
                    if (!$body) {
                        $array['error'] = "Texto não enviado!";
                        return $array;
                    }
                    break;

                case 'photo':
                    if ($photo) {
                        if (in_array($photo->getClientMimeType(), $allowedTypes)) {
                            $filename = md5(time() . rand(0, 9999)) . '.jpg';
                            $destPath = public_path('/media/uploads');
                            $img = Image::read($photo->path())
                                ->scaleDown(800)
                                ->save($destPath . '/' . $filename);

                            $body = $filename;
                        } else {
                            $array['error'] = 'Arquivo não suportado!';
                            return $array;
                        }
                    } else {
                        $array['error'] = 'Arquivo não enviado!';
                        return $array;
                    }
                    break;

                default:
                    $array['error'] = 'Tipo de postagem inexistente.';
                    return $array;
            }

            $newPost = new Post();
            $newPost->id_user = Auth::user()->id;
            $newPost->type = $type;
            $newPost->created_at =  date('Y-m-d H-i-s');
            $newPost->body = $body;
            $newPost->save();
        } else {
            $array['error'] = "Dados não enviados.";
        }

        return $array;
    }

    public function read(Request $r)
    {
        $array = ['error' => ''];
        $page = intval($r->input('page'));
        $perPage = 2;

        $users = [];
        $userList = UserRelation::where('user_from', Auth::user()->id);
        foreach ($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = Auth::user()->id;
        $postList = Post::whereIn('id_user', $users)
            ->orderBy('created_at', 'desc')
            ->offset($page * $perPage)
            ->limit($perPage)->get();

        $array['posts'] = [];
        $total = Post::whereIn('id_user', $users)->count();
        $pageCount = ceil($total / $perPage);

        $posts = $this->_postListToObject($postList, Auth::user()->id);

        $array['posts'] = $posts;
        $array['pageCount'] = $pageCount;
        $array['currentPage'] = $page;


        return $array;
    }

    public function userFeed(Request $r, $id = false)
    {
        $array = ['error' => ''];
        if ($id == false) {
            $id = Auth::user()->id;
        }
        $page = intval($r->input('page'));
        $perPage = 2;

        $postList = Post::where('id_user', $id)
            ->orderBy('created_at', 'desc')
            ->offset($page * $perPage)
            ->limit($perPage)->get();


        $total = Post::where('id_user', $id)->count();
        $pageCount = ceil($total / $perPage);

        $posts = $this->_postListToObject($postList, Auth::user()->id);

        $array['posts'] = $posts;
        $array['pageCount'] = $pageCount;
        $array['currentPage'] = $page;

        return $array;
    }
    private function _postListToObject($postList, $loggedUserId)
    {

        foreach ($postList as $postKey => $postItem) {
            if ($postItem['id_user'] == $loggedUserId) {
                $postList[$postKey]['mine'] = true;
            } else {
                $postList[$postKey]['mine'] = false;
            }

            $userInfo  = User::find($postItem['id_user']);
            $userInfo['avatar'] = url('/media/avatars/' . $userInfo['avatar']);
            $userInfo['cover'] = url('/media/covers/' . $userInfo['cover']);
            $postList[$postKey]['user'] = $userInfo;

            $likes = PostLike::where('id_post', $postItem['id'])->count();
            $postList[$postKey]['likeCount'] = $likes;

            $isLiked = PostLike::where(column: 'id_user', operator: $postItem['id'])
                ->where('id_user',  $loggedUserId)->count();
            $postList[$postKey]['liked'] = $isLiked > 0;

            $comments = PostComment::where('id_post', $postItem['id'])->get();
            foreach ($comments as $commentKey => $comment) {
                $user = User::find($comment['id_user']);
                $user['avatar'] = url('media/avatars/' . $user['avatar']);
                $user['cover'] = url('media/covers/' . $user['cover']);
                $comments[$commentKey]['user'] = $user;
            }
            $postList[$postKey]['comments'] = $comments;
        }
        //erro ao listar verificar
        return $postList;
    }
}
