<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            // 1. Get the authenticated user from Google
            $googleUser = Socialite::driver('google')->user();

            // 2. Check if a user with this Google ID already exists
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // 3. If not, find by email
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // 4. If the email exists but no Google ID, link the accounts
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                    $user = $existingUser;
                } else {
                    // 5. Create a new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password' => null, // Social login, so no password
                    ]);
                }
            }

            // 6. Log the user into the application
            Auth::login($user, remember: true);

            // 7. Redirect to intended page or dashboard
            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            // 8. Handle any errors
            logger($e->getMessage());
            return redirect()->route('login')->with('error', 'Something went wrong with Google login.');
        }
    }
}