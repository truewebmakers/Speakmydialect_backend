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
            $query->whereHas('userSkills', function ($subquery) use ($request) {
                $subquery->where('language', $request->input('language'));
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('userSkills', function ($subquery) use ($request) {
                $subquery->where('level',  $request->input('level'));
            });
        }

        $query->with('userMeta', 'userSkills');

        $translators = $query->get();
        // $translators = $query->get();

        return response()->json(['message' => 'Translators fetched successfully.' ,'data' => $translators],200);


    }
    public function searchTranslatorsSuggestions(Request $request)
    {
        $query = Language::query();
        if ($request->filled('language')) {
            $language = $request->input('language');
            $query->where('name', 'LIKE', "%$language%");
        }
        $languages = $query->get();
        return response()->json(['message' => 'languages suggestion list fetched successfully.' ,'data' => $languages],200);
    }

}
