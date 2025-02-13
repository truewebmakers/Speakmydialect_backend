<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserEduction;
class UserEductionController extends Controller
{
    public function getWorkExperience($id){
        $workExperience = UserEduction::with('user')->where('user_id',$id)->orderBy('id','desc')->get();
        return response()->json(['message' => 'Education feched successfully.','data' => $workExperience ,'status' => true],200);
    }
    public function store(Request $request,$id)
    {
        $validatedData = $request->validate([
            'degree_name' => 'required|string|max:255',
            'university_name' => 'nullable|string|max:255',
            'year_start' => 'nullable|string|max:255',
            'year_end' => 'nullable|string|max:255',
            'any_info' => 'nullable|string|max:255',
        ]);

        $validatedData['user_id'] = $id;
        UserEduction::create($validatedData);
        return response()->json(['message' => 'Education add successfully.','status' => true],200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'degree_name' => 'required|string|max:255',
            'university_name' => 'nullable|string|max:255',
            'year_start' => 'nullable|string|max:255',
            'year_end' => 'nullable|string|max:255',
            'any_info' => 'nullable|string|max:255',
        ]);
        $workExperience = UserEduction::find($id);

        $workExperience->update($validatedData);
        return response()->json(['message' => 'Education update successfully.','status' => true],200);


    }

    public function destroy($id)
    {
        $workExperience = UserEduction::find($id);
        $workExperience->delete();
        return response()->json(['message' => 'Education deleted successfully.','status' => true],200);

    }
}
