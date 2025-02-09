<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRelation;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = Auth::user();
    }

    public function update(Request $r)
    {
        //adicionar if para verificar se Auth::user() existe
        $array = ['error' => ''];
        $name = $r->input('name');
        $email = $r->input('email');
        $password = $r->input('password');
        $password_confirm = $r->input('password_confirm');
        $birthdate = $r->input('birthdate');
        $city = $r->input('city');
        $work = $r->input('work');

        $user = User::find(Auth::user()->id);

        if ($name) {
            $user->name = $name;
        }
        if ($email) {
            $emailExists = User::where('email', $email)->count();
            if ($emailExists === 0) {
                $user->email  = $email;
            } else {
                $array['error'] = 'E-mail já existe!';
                return $array;
            }
        }
        if ($birthdate) {

            if (strtotime($birthdate) === false) {
                $array['error'] = 'Data de nascimento inválida!';
                return $array;
            }
            $user->birthdate = $birthdate;
        }

        if ($city) {
            $user->city = $city;
        }

        if ($work) {
            $user->work = $work;
        }

        if ($password && $password_confirm) {
            if ($password === $password_confirm) {

                $hash = Hash::make($password);
                $user->password = $hash;
            } else {
                $array['error'] = 'As senhas não batem.';
            }
        }

        $user->save();
        return $array;
    }


    public function updateAvatar(Request $r)
    {
        //adicionar if para verificar se Auth::user() existe
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $image = $r->file('avatar');

        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                $filename = md5(time() . rand(0, 9999)) . '.jpg';
                $destPath = public_path(('/media/avatars'));
                $img = Image::read($image->path())->cover(200, 200)->save($destPath . '/' . $filename);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $filename;
                $user->save();

                $array['url'] = url('/media/avatars/' . $filename);
            } else {
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }
        return $array;
    }

    public function updateCover(Request $r)
    {
        //adicionar if para verificar se Auth::user() existe
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
        $image = $r->file('cover');
        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                $filename = md5(time() . rand(0, 9999)) . '.jpg';
                $destPath = public_path(('/media/covers'));
                $img = Image::read($image->path())->cover(850, 310)->save($destPath . '/' . $filename);

                $user = User::find($this->loggedUser['id']);
                $user->cover = $filename;
                $user->save();

                $array['url'] = url('/media/covers/' . $filename);
            } else {
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }
        return $array;
    }

    public function read($id = false)
    {
        $array = ['error' => ''];

        if ($id) {
            $info = User::find($id);
            if (!$info) {
                $array['error'] = "Usuário inexistente!";
                return $array;
            }
        } else {
            $info = $this->loggedUser;
        }

        $info['avatar'] = url('/media/avatars/' . $info['avatar']);
        $info['cover'] = url('/media/covers/' . $info['cover']);

        $info['me'] = $info['id'] == $this->loggedUser['id'] ? true : false;

        $dateFrom = new \DateTime($info['birthdate']);
        $dateTo = new \DateTime('today');
        $info['age'] = $dateFrom->diff($dateTo)->y;
        $info['followers_count'] = UserRelation::where('user_to', $info['id'])->count();
        $info['following_count'] = UserRelation::where('user_from', $info['id'])->count();
        $info['posts_count'] = Post::where('id_user', $info['id'])
            ->where('type', 'photo')->count();

        //sigo o outro usuario?
        $hasRelation = UserRelation::where('user_from', $this->loggedUser['id'])
            ->where('user_to', $info['id'])->count();
        $info['isFollowing'] = $hasRelation > 0 ? true : false;

        $array['data'] = $info;
        return $array;
    }

    public function follow($id)
    {
        $array = ['error' => ''];

        if ($id == $this->loggedUser['id']) {
            $array['error'] = 'Nao pode seguir voce mesmo';
            return $array;
        }

        $userExistis = User::find($id);

        if (!$userExistis) {
            $array['error'] = 'Usuário inexistente!';
            return $array;
        }

        $relation = UserRelation::where('user_from', $this->loggedUser['id'])
            ->where('user_to', $userExistis['id'])->first();
        if ($relation) {
            $relation->delete();
        } else {
            $newRelation = new UserRelation();
            $newRelation->user_from = $this->loggedUser['id'];
            $newRelation->user_to = $userExistis['id'];
            $newRelation->save();
        }
    }

    public function followers($id)
    {
        $array = ['error' => ''];
        $info = User::find($id);
        if (!$info) {
            $array['error'] = 'Usuário inexistente!';
            return $array;
        }
        $followers = UserRelation::where('user_to', $info['id'])->get();
        $following = UserRelation::where('user_from', $info['id'])->get();
        $array['followers'] = [];
        $array['following'] = [];
        foreach ($followers as $item) {
            $user = User::find($item['user_from']);
            $array['followers'][] = [
                'avatar' => url('/media/avatars/' . $user['avatar']),
                'name' => $user['name'],
                'id' => $user['id'],
            ];
        }
        foreach ($following as $item) {
            $user = User::find($item['user_from']);
            $array['following'][] = [
                'avatar' => url('/media/avatars/' . $user['avatar']),
                'name' => $user['name'],
                'id' => $user['id'],
            ];
        }
        return $array;
    }

}
