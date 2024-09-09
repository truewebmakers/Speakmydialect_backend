<?php

namespace App\Http\Controllers;

use App\Models\TranslatorBankDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranslatorBankDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($userId)
    {
        $bankDetails = TranslatorBankDetails::where(['user_id' => $userId])->get()->first();
        return response()->json(['message' => 'bank details store.' ,'data' => $bankDetails ,'status' => true],200);

    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bsb' => 'required|string|max:255',
            'ifsc' => 'nullable|string|max:255',
        ]);
        $validatedData['user_id'] =   Auth::user()->id;
        $bankDetail = TranslatorBankDetails::create($validatedData);
        return response()->json(['message' => 'bank details store.' ,'data' => $bankDetail ,'status' => true],200);

    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bsb' => 'required|string|max:255',
            'ifsc' => 'nullable|string|max:255',
        ]);
        $validatedData['user_id'] =  Auth::user()->id;
        $data = TranslatorBankDetails::find($id)->update($validatedData);
        return response()->json(['message' => 'bank details store.' ,'data' => $data ,'status' => true],200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        TranslatorBankDetails::find($id)->delete();
        // Return a response indicating successful deletion
        return response()->json(['message' => 'bank details deleted.'  , 'status' => true],200);

    }
}
