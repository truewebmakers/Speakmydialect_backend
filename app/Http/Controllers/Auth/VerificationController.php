<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        // Find the user by ID
        $user = User::findOrFail($request->id);

        // Check if the hash matches
        if (! hash_equals((string) $request->hash, (string) sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        // Mark the user as verified
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Verify the user
        $user->markEmailAsVerified();
        event(new Verified($user));

        // Optionally, log in the user after verification
        Auth::login($user);
        $message = "Thank you! You have successfully verified your email. ";
        return view('thankyou',compact('message'));
    }
}
