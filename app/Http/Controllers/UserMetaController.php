<?php

namespace App\Http\Controllers;

use App\Models\{Country, Language, Timezone, User, UserMeta, UserSkills};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserMetaController extends Controller
{

    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $request->validate([
            'profile_pic' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'phone' => 'required|string|exists:users_meta,value,'.$id.',user_id',
            'fix_rate' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'email' => 'required|email|unique:users,email,'.$id,
            'gender' => 'nullable|string',
            'location' => 'nullable|string',
            'intro' => 'nullable|string',

        ]);
        $user = User::findOrFail($id);

        $userMeta = UserMeta::where(['user_id' => $id]);

        if (!empty($userMeta)) {
            $userMeta->update([
                'phone' => $request->input('phone'),
                'gender' => $request->input('gender'),
                'location' => $request->input('location'),
                'fix_rate' => $request->input('fix_rate'),
                'hourly_rate' => $request->input('hourly_rate'),
                'intro' => $request->input('intro'),
            ]);
        }else{
            $userMeta->create([
                'phone' => $request->input('phone'),
                'gender' => $request->input('gender'),
                'location' => $request->input('location'),
                'fix_rate' => $request->input('fix_rate'),
                'hourly_rate' => $request->input('hourly_rate'),
                'intro' => $request->input('intro'),
            ]);
        }


        if ($request->hasFile('profile_pic')) {
            $profilePic = $request->file('profile_pic');
            $path = Storage::disk('s3')->put('profile_pictures', $profilePic, 'public'); // Assuming AWS S3 is configured
            $user->profile_pic = $path;
            $user->save();
        }

        return response()->json(['message' => 'User meta information updated successfully'], 200);
    }



    public function updateOrCreateSkills(Request $request, $userId)
    {
        $skillsData = $request->input('skills');

        // Loop through each skill and update or create accordingly
        foreach ($skillsData as $skill) {
            $existingSkill = UserSkills::where('user_id', $userId)
                ->where('language', $skill['language'])
                ->first();

            if ($existingSkill) {
                // If skill exists, update it
                $existingSkill->update([
                    'level' => $skill['level'],
                    'status' => $skill['status']
                ]);
            } else {
                // If skill doesn't exist, create it
                UserSkills::create([
                    'user_id' => $userId,
                    'language' => $skill['language'],
                    'level' => $skill['level'],
                    'status' => $skill['status']
                ]);
            }
        }

        return response()->json(['message' => 'Skills updated successfully']);
    }

    public function getUserDetail($id){
        $user = User::with('UserMeta')->findOrFail($id);
        return response()->json(['message' => 'User fetched successfully' , 'user' =>$user]);

    }


    public function getLangauges(){
       $lang =  Language::all();
        return response()->json(['message' => 'Languages Fetched' ,'data' => $lang]);
    }
    public function getCountries(){
        $countries =  Country::all();
         return response()->json(['message' => 'Country Fetched' ,'data' => $countries]);
     }
    public function getTimezone(){
        $timezone =  Timezone::all();
         return response()->json(['message' => 'Timezone Fetched' ,'data' => $timezone]);
     }
}
