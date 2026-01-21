<?php

namespace App\Http\Controllers;

use App\Models\User; // ✅ Use your actual User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AzureController extends Controller
{
    public function handleRedirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    public function handleCallback()
    {
        try {
            $azureUser = Socialite::driver('azure')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $azureUser->getEmail()],
                [
                    'name' => $azureUser->getName(),
                    'email' => $azureUser->getEmail(),
                    'azure_id' => $azureUser->getId(),
                ]
            );

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            dd($e, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function login()
    {
        return redirect('/login'); 
    }
}
