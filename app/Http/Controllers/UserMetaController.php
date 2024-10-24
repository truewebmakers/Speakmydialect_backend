<?php

namespace App\Http\Controllers;

use App\Models\{Country, Language, Timezone, User, UserDocuments, UserMeta, UserSkills};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserMetaController extends Controller
{



    public function UserDocumentUpload(Request $request,$id)
    {

        $file = $request->file('file');
        $validatedData = $request->validate([
            'file' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'type' => 'nullable|string|max:255',
        ]);


       $path = uploadProfilePicture($file , 'user1');

        $validatedData['user_id'] = $id;
        UserDocuments::create($validatedData);
        return response()->json(['message' => 'Education add successfully.','status' => true],200);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $request->validate([
            'profile_pic' => 'image|mimes:jpeg,png,jpg,gif|max:10000',
            'phone' => 'required',
            'fix_rate' => 'required',
            'hourly_rate' => 'required',
            'gender' => 'nullable|string',
            'location' => 'nullable|string',
            'intro' => 'nullable|string',
            'address' => 'nullable|string'

        ]);
        if($request->input('fname') || $request->input('lname')){
            $request->validate([
                'fname' => 'required',
                'lname' => 'required',
            ]);
            User::find($id)->update([
                'fname' => $request->input('fname'),
                'lname' => $request->input('lname'),
            ]);
        }
        $userMeta = UserMeta::where(['user_id' => $id]);
        $insert = [
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'location' => $request->input('location'),
            'fix_rate' => $request->input('fix_rate'),
            'hourly_rate' => $request->input('hourly_rate'),
            'intro' => $request->input('intro'),
            'address' => $request->input('address'),
            'user_id' => $id
        ];
        if ($userMeta->count() > 0) {
            if ($request->hasFile('profile_pic')) {
                $profilePic = $request->file(key: 'profile_pic');
                $path = Storage::disk('s3')->put('profile_pictures', $profilePic);
                $insert['profile_pic'] = $path;
            }
            $userMeta->update($insert);
        }else{
            if ($request->hasFile('profile_pic')) {
                $profilePic = $request->file('profile_pic');
                $path = Storage::disk('s3')->put('profile_pictures', $profilePic);
                $insert['profile_pic'] = $path;
            }
            UserMeta::create($insert);
        }
        return response()->json(['message' => 'User meta information updated successfully' ,'status' => true], 200);
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
                    'dialect' => $skill['dialect'],
                    'status' => $skill['status']
                ]);
            } else {
                // If skill doesn't exist, create it
                UserSkills::create([
                    'user_id' => $userId,
                    'language' => $skill['language'],
                    'dialect' => $skill['dialect'],
                    'level' => $skill['level'],
                    'status' => $skill['status']
                ]);
            }
        }

        return response()->json(['message' => 'Skills updated successfully','status' => true]);
    }

    public function getSkills($id){
         $skills =  UserSkills::where(['user_id' => $id])->orderBy('id','desc')->get();
         return response()->json(['message' => 'User skills fetched successfully' , 'data' =>$skills ,'status' => true]);

    }

    public function getSkillsAll($column){
        $skills =  UserSkills::get($column);
        return response()->json(['message' => 'User skills fetched successfully' , 'data' =>$skills ,'status' => true]);

   }

    public function DeleteSkill($id){
        $skills =  UserSkills::where(['id' => $id])->delete();
        return response()->json(['message' => 'User skills delete successfully','status' => true]);

   }



    public function getUserDetail($id){
        $user = User::with('UserMeta')->findOrFail($id);
        return response()->json(['message' => 'User fetched successfully' , 'user' =>$user,'status' => true]);

    }


    public function getLangauges(){
       $lang =  Language::all();
        return response()->json(['message' => 'Languages Fetched' ,'data' => $lang,'status' => true]);
    }
    public function getCountries(){
        $countries =  Country::all();
         return response()->json(['message' => 'Country Fetched' ,'data' => $countries,'status' => true]);
     }
    public function getTimezone(){
        $timezone =  Timezone::all();
         return response()->json(['message' => 'Timezone Fetched' ,'data' => $timezone,'status' => true]);
     }
}
