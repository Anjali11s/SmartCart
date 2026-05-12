<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            
            // Log for debugging
            Log::info('Google User Data:', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);

            // 2. Check if a user with this Google ID already exists
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // 3. If not, find by email
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // 4. If the email exists but no Google ID, link the accounts
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'email_verified_at' => $existingUser->email_verified_at ?? now(),
                    ]);
                    $user = $existingUser;
                } else {
                    // 5. Create a new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'email_verified_at' => now(),
                        'password' => bcrypt(bin2hex(random_bytes(16))), // Random password for social login
                        'role' => 'user', // Default role
                    ]);
                    
                    // Create cart for new user
                    \App\Models\Cart::create(['user_id' => $user->id]);
                }
            }

            // 6. Log the user into the application
            Auth::login($user, remember: true);

            // 7. Regenerate session to prevent fixation
            $request->session()->regenerate();

            // 8. Redirect to intended page or dashboard
            return redirect()->intended(route('dashboard'));

        } catch (Exception $e) {
            // 9. Handle any errors with proper logging
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Something went wrong with Google login: ' . $e->getMessage());
        }
    }
}