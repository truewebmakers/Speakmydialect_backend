<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class SearchTranslatorsController extends Controller
{
    public function searchTranslators(Request $request)
    {
        $query = User::query();

        // Apply filters from user_metas table
        if ($request->filled('fix_rate')) {
            $query->whereHas('userMeta', function ($subquery) use ($request) {
                $subquery->where('fix_rate', $request->input('fix_rate'));
            });
        }

        if ($request->filled('hourly_rate')) {
            $query->whereHas('userMeta', function ($subquery) use ($request) {
                $subquery->where('hourly_rate', $request->input('hourly_rate'));
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('userMeta', function ($subquery) use ($request) {
                $subquery->where('location', $request->input('location'));
            });
        }

        if ($request->filled('gender')) {
            $query->whereHas('userMeta', function ($subquery) use ($request) {
                $subquery->where('gender', $request->input('gender'));
            });
        }

        // Apply filters from user_skills table
        if ($request->filled('language')) {
            $languageId = $request->input('language');
            $query->whereHas('userSkills', function ($subquery) use ($request,$languageId) {
                $subquery->where('language',$languageId );
            });
            // $languageId = Language::where('name','Like','%'.$request->input('language').'%')->pluck('id');
            // $query->whereHas('userSkills', function ($subquery) use ($request,$languageId) {
            //     $subquery->whereIn('language',$languageId );
            // });
        }

        if ($request->filled('level')) {
            $query->whereHas('userSkills', function ($subquery) use ($request) {
                $subquery->where('level',  $request->input('level'));
            });
        }

        if ($request->filled('dialect')) {
            $query->whereHas('userSkills', function ($subquery) use ($request) {
                $subquery->where('dialect',  $request->input('dialect'));
            });
        }

        $query->with('userMeta', 'userSkills')->where(['user_type' =>'translator','status' => 'active']);
        // $translators = $query->get();
        $pagelimit = $request->input('page_limit',10);
        $currentPage = $request->input('page', 1);
        $translators = $query->paginate($pagelimit , ['*'], 'page', $currentPage);


        return response()->json([
            'message' => 'Translators fetched successfully.',
            'data' => $translators->items(), // Get only the current page items
            'current_page' => $translators->currentPage(),
            'last_page' => $translators->lastPage(),
            'total_count' => $translators->total(), // Total count of records
            'total_pages' => $translators->lastPage(), // Total number of pages
            'status' => true,
        ], 200);
        // return response()->json(['message' => 'Translators fetched successfully.' ,'data' => $translators ,'status' => true],200);


    }
    public function searchTranslatorsSuggestions(Request $request)
    {
        $query = Language::query();
        if ($request->filled('language')) {
            $language = $request->input('language');
            $query->where('name', 'LIKE', "%$language%");
        }
        $languages = $query->get();
        return response()->json(['message' => 'languages suggestion list fetched successfully.' ,'data' => $languages ,'status' => true],200);
    }

    public function getUserProfile($uuid){
        $data = User::with('userMeta', 'userSkills','UserEducation','UserWorkExperince')->where('uuid',$uuid)->first();
        return response()->json(['message' => 'user profile fetched successfully.' ,'data' => $data ,'status' => true],200);
    }

}
