<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchTranslatorsController extends Controller
{

    public function searchTranslators(Request $request)
    {

        $query = User::where('user_type', 'translator');

        // Apply additional filters if needed
        if ($request->has('language')) {
            $query->where('language', $request->input('language'));
        }
        if ($request->has('language')) {
            $query->where('language', $request->input('language'));
        }

        // You can add more filters here

        $translators = $query->get();

        return response()->json($translators);
    }

}
