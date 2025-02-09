<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends Controller
{

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

                $user = User::find(Auth::user()->id);
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

    public function read($id = false)
    {
        $array = ['error', ''];

        if ($id) {
            $info = User::find($id);
            if (!$info) {
                $array['error'] = "Usuário inexistente!";
                return $array;
            }
        } else {
            $info = Auth::user();
        }
        $array['data'] = $info;
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

                $user = User::find(Auth::user()->id);
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
}
