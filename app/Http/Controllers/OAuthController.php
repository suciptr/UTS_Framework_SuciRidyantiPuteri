<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google OAuth error: '.$e->getMessage());
        }

        // Check if the user already exists in the database
        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            // If user already exists, you can return response accordingly
            return redirect('/home'); // Redirect ke halaman setelah login
        } else {
            // If user doesn't exist, create a new user
            $user = new User();
            $user->name = $googleUser->name;
            $user->email = $googleUser->email;
            // You may need to adjust this based on your database schema and requirements
            $user->password = bcrypt(str_random(16));
            $user->save();

            // Here you can perform any additional actions you need after registration

            return redirect('/home'); // Redirect ke halaman setelah login
        }
    }
}
