<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
