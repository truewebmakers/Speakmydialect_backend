<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserDocuments;

use Illuminate\Support\Facades\Storage;
class UserDocuemntController extends Controller
{
    //

    public function getdocumentsOfUser(Request $request,$userId){
      $documents =   UserDocuments::where(['user_id' => $userId])->get();
      return response()->json(['message' => 'Documents Fetched' ,'data' => $documents,'status' => true]);

    }

    public function UpdateUserStatus(Request $request,$userId){

        $request->validate([
            'status' => 'required',
            'reason' => 'required',
        ]);
        $user =   User::find($userId);
        if($user){
            $user->update([
                'status' => $request->input('status'),
                'reason' => $request->input('reason')
            ]);
        }else{
            return response()->json(['message' => 'User not found' ,'data' => [],'status' => false]);

        }
        return response()->json(['message' => 'User Status has been  updated' ,'data' => $user,'status' => true]);

    }

    public function getNewUserList(Request $request){
        $status = $request->input('status');
        if($status){
            $users = User::where('status', $status)->get();
        }else{
            $users = User::where('status','!=','active')->get();
        }

        if($users){
            return response()->json(['message' => 'User Fetched' ,'data' => $users,'status' => true]);

        }
        return response()->json(['message' => 'User not founf' ,'data' => [],'status' => false]);

    }
}
