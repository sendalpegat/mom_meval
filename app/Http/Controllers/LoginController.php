<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Controllers\RootController;
use App\Models\User;

class LoginController extends RootController
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('meeting');
        }else{
            return view('login');
        }
    }

    public function actionlogin(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'status' => User::ACTIVE
        ];

        if (Auth::Attempt($data)) {
            return redirect('meeting');
        }else{
            Session::flash('error', 'Email atau Password Salah');
            return redirect('/');
        }
    }

    public function actionlogout()
    {
        Auth::logout();
        return redirect('/');
    }
}
