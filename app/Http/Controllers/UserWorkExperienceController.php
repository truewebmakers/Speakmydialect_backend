<?php

namespace App\Http\Controllers;

use App\Models\UserWorkExperience;
use Illuminate\Http\Request;

class UserWorkExperienceController extends Controller
{
    //
    public function getWorkExperience($id){
        $workExperience = UserWorkExperience::where('user_id',$id)->get();
        return response()->json(['message' => 'Work Experience feched successfully.','data' => $workExperience ],200);
    }
    public function store(Request $request,$id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'location_type' => 'nullable|string|max:255',
            'start_month' => 'nullable|string|max:255',
            'start_year' => 'nullable|string|max:255',
            'present_working' => 'nullable|in:0,1',
            'end_month' => 'nullable|string|max:255',
            'end_year' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
        ]);

        // $validatedData['user_id'] = auth()->id();
        $validatedData['user_id'] = $id;
        UserWorkExperience::create($validatedData);
        return response()->json(['message' => 'Work Experience add successfully.'],200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'location_type' => 'nullable|string|max:255',
            'start_month' => 'nullable|string|max:255',
            'start_year' => 'nullable|string|max:255',
            'present_working' => 'nullable|in:0,1',
            'end_month' => 'nullable|string|max:255',
            'end_year' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
        ]);
        $workExperience = UserWorkExperience::find($id);

        $workExperience->update($validatedData);
        return response()->json(['message' => 'Work Experience update successfully.'],200);


    }

    public function destroy($id)
    {
        $workExperience = UserWorkExperience::find($id);
        $workExperience->delete();
        return response()->json(['message' => 'Work Experience deleted successfully.'],200);

    }
}
