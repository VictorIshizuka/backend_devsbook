<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    private $loggedUser;
    public function __construct()
    {

        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }
}
