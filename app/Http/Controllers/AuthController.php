<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{


    public function unauthorized()
    {
        return response()->json(['error' => 'Não autorizado'], 401);
    }

    public function login(Request $r)
    {
        $array = ['error' => ''];
        $email = $r->input('email');
        $password = $r->input('password');
        if ($email && $password) {

            $token = Auth::attempt(['email' => $email, 'password' => $password]);
            if (!$token) {
                $array['error'] = 'E-mail e/ou senha errados!';
                return $array;
            }
            $array['token'] = $token;
            return $array;
        }
        $array['error'] = "Preencha os campos em vazio";
        return $array;
    }
    public function logout()
    {
        Auth::logout();
        return ['error' => ''];
    }
    public function refresh()
    {
        $token = Auth::refresh();
        return [
            'error' => '',
            'token' => $token
        ];
    }

    public function create(Request $r)
    {
        $array = ['error' => ''];
        $name = $r->input('name');
        $email = $r->input('email');
        $password = $r->input('password');
        $birthdate = $r->input('birthdate');

        if ($name && $email && $password && $birthdate) {
            if (strtotime($birthdate) === false) {
                $array['error'] = "Data de nascimento inválida!";
                return $array;
            }

            $emailExists = User::where('email', $email)->count();
            if ($emailExists === 0) {
                $hash = Hash::make($password);

                $newUser = new User();
                $newUser->name = $name;
                $newUser->email = $email;
                $newUser->password = $hash;
                $newUser->birthdate = $birthdate;
                $newUser->save();

                $token = Auth::attempt(['email' => $email, 'password' => $password]);
                if (!$token) {
                    $array['error'] = "Ocorreu um erro!";
                    return $array;
                }
                $array['token'] = $token;
            } else {
                $array['error'] = "E-mail já cadastrado!";
                return $array;
            }
        } else {
            $array['error'] = "Não enviou todos os campos.";
            return $array;
        }
        return $array;
    }
}
