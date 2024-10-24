<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function verify($id,$hash)
    {
        // Validate the request
        // $request->validate([
        //     'id' => 'required|integer',
        //     'hash' => 'required|string',
        // ]);

        echo "<pre>"; print_r($id); die;

        // Find the user by ID
        $user = User::findOrFail($id);

        // Check if the hash matches
        if (! hash_equals((string) $hash, (string) sha1($user->getEmailForVerification()))) {
            return 'Invalid verification link.';
        }

        // Mark the user as verified
        if ($user->hasVerifiedEmail()) {
            return 'Email already verified.';
        }

        // Verify the user
        $user->markEmailAsVerified();
        // event(new Verified($user));
        $message = "Thank you! You have successfully verified your email. ";
        return view('thankyou',compact('message'));
    }
}
