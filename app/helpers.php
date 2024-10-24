<?php

use App\Mail\AdminUserApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\SendContactUs;
use Illuminate\Support\Facades\Mail;
// Your controller or other context where you need to upload a profile picture
if (!function_exists('uploadProfilePicture')) {
    function uploadProfilePicture($file , $path='profile_pictures')
    {
        // Ensure a file was uploaded
        if (!$file) {
            return null;
        }

        // Upload file to S3
        $path = Storage::disk('s3')->put($path, $file);

        return $path;
    }
}

if (!function_exists('SendEmail')) {
    function SendEmail($adminEmail,$email,$post,$type='contact_us')
    {
            try {
                if($type === 'contact_us'){
                    Mail::to($adminEmail)->cc($email)->send(new SendContactUs(data: $post));
                }else if($type === 'verify_email'){
                    Mail::to($adminEmail)->cc($email)->send(new AdminUserApproval($post));

                }

            } catch (\Throwable $th) {
                return $th->getMessage();
            }

     }
}


