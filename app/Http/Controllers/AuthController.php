<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDocuments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;


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
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'user_type' => 'required',
            'files.*.path' => 'required|string',
            'files.*.type' => 'required|string',
            'files.*.side' => 'required|string',
        ]);



        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'status' => 'in-review'
        ]);

       $DocuemntData = $this->UploadDocuments($request,$user->id);

        return response()->json(['message' => 'User registered successfully','status' => true], 201);
    }


    public function uploadDocuments(Request $request, $userId)
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
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token , 'userInfo' => Auth::user(), 'message' => 'login successful' ], 200);
        }

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

            return response()->json(['message' => 'Password changed successfully.','status' => true]);
        } else {
            // Old password doesn't match
            return response()->json(['message' => 'Your old password does not match with our records. Please check your password and try again.' ,'status' => false], 401);
        }
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out' ,'status' => true
        ]);
    }
}
