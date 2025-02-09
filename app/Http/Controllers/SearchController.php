<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SearchController extends Controller
{
    private $loggedUser;
    public function __construct()
    {

        // $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function search(Request $r)
    {
        // dd($r);
        $array = ['error' => '', 'users' => []];
        $txt = $r->input('txt');
        if ($txt) {
            $users = User::where('name', 'LIKE', '%' . $txt . '%')->get();
            foreach ($users as  $userItem) {
                $array['users'][] = [
                    'id' => $userItem['id'],
                    'name' => $userItem['name'],
                    'avatar' => url('/media/avatars/' . $userItem['avatar']),
                ];
            }
        } else {
            $array['error'] = 'Digite alguma coisa para buscar.';
            return $array;
        }
        return $array;
    }
}
