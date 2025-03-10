<?php

namespace App\Http\Controllers;

use App\Helpers\TwilioHelper;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\{UserDocuments, ContactFormEntry};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Mail\SendContactUs;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;



class AuthController extends Controller
{

    public function uploadDocumentTemp(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,png,pdf,docx', // add your validation rules
            'type' => 'required|string', // e.g., passport
            'side' => 'required|string', // e.g., front or back
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $side = $request->input('side');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store file temporarily
        $path = $file->storeAs('temp', $filename);

        // Return details to the client
        return response()->json([
            'path' => $path,
            'filename' => $filename,
            'type' => $type,
            'side' => $side,
        ]);
    }
    public function register(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'country_code' => 'required',
            'phone_number' => 'required|string|max:12|unique:users',
            'password' => 'required|string|min:8',
            'user_type' => 'required',
            'profile_locked' => 'Yes',
            'files.*.path' => 'required|string',
            'files.*.type' => 'required|string',
            'files.*.side' => 'required|string',
        ]);
        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'username' => ($request->username) ? $request->username : $request->fname,
            'email' => $request->email,
            'country_code' => $request->country_code,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'status' => 'in-review',
            'otp_verified_at' => ($request->input('otp_verified_at')) ?? date('Y-m-d H:i:s'),  // Default to null if not provided

        ]);

        $this->UploadDocuments($request, $user->id);
        $user->sendEmailVerificationNotification();
        return response()->json(['message' => 'User registered successfully and Please verify your email', 'status' => true], 201);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->first();

        // Check if the email is already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email is already verified.', 'status' => true], 422);
        }

        // Send the verification email
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'A new verification link has been sent to your email address.', 'status' => true], 200);
    }

    public function checkEmail(Request $request)
    {
        // Validate the incoming request to ensure 'email' is provided and is a valid email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email exists in the 'users' table
        $emailExists = User::where('email', $request->email)->exists();

        // Return response
        return response()->json([
            'exists' => ($emailExists) ? true : false
        ]);
    }



    public function sendResetLink(Request $request)
    {
        // Validate the email address
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $response = Password::sendResetLink(
            $request->only('email')
        );

        if ($response === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent to your email.'], 200);
        } else {
            return response()->json(['message' => 'Failed to send the password reset link.'], 500);
        }
    }

    public function showResetForm($token)
    {
        // Logic to show the password reset form
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return redirect()->back()->with('status', 'Password has been reset successfully. You can now log in.');
        } else {
            return redirect()->route('password.reset', ['token' => $request->token])
                ->with('error', 'Failed to reset the password. Please try again.');
        }
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'country_code' => 'required',
            'phone_number' => 'required|string|max:20|unique:users'
        ]);
        // Generate OTP (6 digits)
        $otp = rand(100000, 999999);
        // Store OTP in cache with expiration time (5 minutes)
        $cacheKey = 'otp_' . $request->phone_number;  // Store OTP by phone number for simplicity
        Cache::put($cacheKey, $otp, now()->addMinutes(5));
        $message = "Your OTP code is: $otp. Please use this code to verify your phone number.";
        // Send OTP to the user's phone number via Twilio
        TwilioHelper::sendOtp($request->country_code, $request->phone_number, $message);
        return response()->json([
            'message' => 'OTP sent successfully. Please verify your phone number.',
            'status' => true
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
            'phone_number' => 'required|string|max:20'
        ]);

        $otp = Cache::get('otp_' . $request->phone_number);
        if (!$otp) {
            return response()->json([
                'message' => 'OTP expired or not generated. Please request a new OTP.',
                'status' => false,
                'is_verified' => false
            ], 400);
        }
        if ($otp == $request->otp) {
            return response()->json([
                'message' => 'OTP Verifed',
                'status' => true,
                'is_verified' => true,
                'verified_at' => date('Y-m-d H:i:s')
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid OTP.',
            'status' => false,
            'is_verified' => false
        ], 400);
    }


    private function uploadDocuments(Request $request, $userId)
    {
        $files = $request->input('files');

        foreach ($files as $file) {

            $uuid = Str::uuid();
            $tempPath = $file['path'];
            $type = $file['type'];
            $side = $file['side'];
            $filename = basename($tempPath);

            // Move file to S3
            $tempFile = Storage::path($tempPath);
            $s3Path = "userDocuments/{$userId}/{$type}/{$side}/{$filename}";
            Storage::disk('s3')->put($s3Path, file_get_contents($tempFile));

            // Delete the temp file
            Storage::delete($tempPath);

            // Store details in the database
            UserDocuments::create([
                'uuid' => $uuid,
                'user_id' => $userId,
                'filename' => $filename,
                'type' => $type,
                'side' => $side,
                'path' => $s3Path,
            ]);
        }
    }


    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                Auth::logout(); // Log out the user
                return response()->json(['message' => 'Please verify your email before logging in.'], 403);
            }

            // Check if the user's status is active
            if ($user->status === 'active') {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'token' => $token,
                    'userInfo' => $user,
                    'message' => 'Login successful'
                ], 200);
            } else {
                // If the user's status is not active
                Auth::logout(); // Log the user out
                return response()->json(['message' => "Your account is currently awaiting approval. Please contact the admin to complete the approval process. Once approved, you'll have full access to your account."], 403);
            }
        }
        // if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        //     $user = Auth::user();
        //     $token =  $user->createToken('auth_token')->plainTextToken;
        //     return response()->json(['token' => $token , 'userInfo' => Auth::user(), 'message' => 'login successful' ], 200);
        // }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function UpdatePassword(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6',
        ]);

        $user = User::find($id);

        // Check if the old password matches
        if (Hash::check($request->input('old_password'), $user->password)) {
            // Old password matches, update the password
            $user->password = Hash::make($request->input('password'));
            $user->save();

            return response()->json(['message' => 'Password changed successfully.', 'status' => true]);
        } else {
            // Old password doesn't match
            return response()->json(['message' => 'Your old password does not match with our records. Please check your password and try again.', 'status' => false], 401);
        }
    }

    Public function UpdateProfileLock(Request $request, $id){
        $profilelock = $request->input('profile_lock');
        User::find($id)->update(['profile_locked' => $profilelock]);
        return response()->json(['message' => 'Profile Updated  successfully.', 'status' => true]);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
            'status' => true
        ]);
    }

    public function sendEmail(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'subject' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'query' => 'required'

            ]);
            $post = $request->only(['first_name', 'last_name', 'subject', 'email', 'phone', 'query']);

            $email = $request->input('email');
            $adminEmail = env('MAIL_ADMIN_EMAIL');
            Mail::to($adminEmail)->cc($email)->send(new SendContactUs(data: $post));

            ContactFormEntry::create($post);
            return response()->json([
                'message' => 'Email Sent',
                'status' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status' => true
            ]);
        }
    }

    public function getAllUsers(Request $request)
    {
        // Validate user_type if needed
        $request->validate([
            'user_type' => 'required|string'
        ]);

        // Fetch users based on user_type
        $users = User::where('user_type', $request->input('user_type'))->get();

        // Check if any users were found
        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'Not Found',
                'status' => false
            ]);
        }

        // Return the users if found
        return response()->json([
            'message' => 'Users fetched successfully',
            'status' => true,
            'data' => $users
        ]);
    }



    public function FetchContactFormEntires()
    {
        $entries = ContactFormEntry::orderBy('id', 'desc')->get();
        return response()->json([
            'message' => 'Fetched Sent',
            'data' => $entries,
            'status' => true
        ]);
    }
}
