<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserDocuments;

use Illuminate\Support\Facades\Storage;

use App\Mail\AdminUserApproval;
use Illuminate\Support\Facades\Mail;
class UserDocuemntController extends Controller
{
    //

    public function getdocumentsOfUser(Request $request,$userId){
      $documents =   UserDocuments::where(['user_id' => $userId])->orderBy('id','desc')->get();
      if($documents){
        return response()->json(['message' => 'Documents Fetched' ,'data' => $documents,'status' => true]);
      }
      return response()->json(['message' => 'Documents Fetched' ,'data' => [],'status' => false]);

    }

    public function UpdateUserStatus(Request $request,$userId){

        $request->validate([
            'status' => 'required',
            'reason' => 'required',
        ]);
        $post = [];
        $user =   User::find($userId);
        if($user){
            $user->update([
                'status' => $request->input('status'),
                'reason' => $request->input('reason')
            ]);
            $post['status'] = $request->input('status');
             if($request->input('status') == 'active'){
                $post['message'] = "Congratulation, You are now Approved from admin and you can able to login now in the system";
                $post['login'] = true;
            }else if($request->input('status') == 'inactive'){
                $post['message'] = "You status put as inactive for ".$request->input('reason'). " Please check accordingly" ;
                $post['login'] = false;
            }else if($request->input('status') == 'reject'){
                $post['message'] = "You status put as Reject for ".$request->input('reason'). " Please check accordingly" ;
                $post['login'] = false;
            }

        }else{
            return response()->json(['message' => 'User not found' ,'data' => [],'status' => false]);

        }
        $this->ApprovalMail($user->email ,$post );
        return response()->json(['message' => 'User Status has been  updated' ,'data' => $user,'status' => true]);

    }


    public function ApprovalMail($email ,$post){
        try {
            $adminEmail = env('MAIL_ADMIN_EMAIL');
            Mail::to($email) ->send(new AdminUserApproval(data: $post));
            return response()->json([
                'message' => 'Email Sent' ,
                'status' => true
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage() ,
                'status' => true
            ]);
        }
    }

    public function getNewUserList(Request $request){
        $status = $request->input('status');
        if($status){
            $users = User::where('status', $status)->orderBy('id','desc')->get();
        }else{
            $users = User::where('status','!=','active')->orderBy('id','desc')->get();
        }

        if($users){
            return response()->json(['message' => 'User Fetched' ,'data' => $users,'status' => true]);

        }
        return response()->json(['message' => 'User not founf' ,'data' => [],'status' => false]);

    }

    public function destroy($userId){
        $user =   User::find($userId);
        if($user){
            $user->delete();
            return response()->json(['message' => 'User deleted' ,'data' => [],'status' => true]);
        }else{
            return response()->json(['message' => 'User deleted' ,'status' => false]);

        }

    }
}
