<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
        public function current(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'name'  => Auth::user()->name,
                'email' => Auth::user()->email,
                'role'  => Auth::user()->role, 
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}