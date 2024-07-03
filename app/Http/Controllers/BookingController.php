<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;

class BookingController extends Controller
{


    public function getPayoutBooking($translatorId,Request $request)
    {
        $query = Booking::where(['translator_id' => $translatorId]);

        $booking = $query->get();
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function getBookingForClient($clientId , $status='',Request $request)
    {
        $query = Booking::with('translator','translatorMeta')->where(['client_id' => $clientId]);

        if($request->input('type') == 'upcoming_booking'){
            $query->where(function($query) use ($status) {
                $query->where('status', $status)
                      ->orWhere('status', 'accept');
            })->whereDate('start_at', '>', now()->toDateString());

        }
        if($request->input('type') == 'current_booking'){
            $query->where('status', $status)->whereDate('start_at',date('Y-m-d'));
        }

        if($request->input('type') == 'completed_booking'){
            $query->where('status', $status)->whereDate('start_at',date('Y-m-d'));
        }

        if($request->input('type') == 'approved_booking'){
            $query->where('status', $status);
        }

        if($request->input('type') == 'canceled_booking'){
            $query->where('status', $status);
        }


        $booking = $query->orderBy('created_at','desc')->get();
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function getBookingForTranslator($translatorId , $status ='' , Request $request)
    {
        $query = Booking::with('client','clientMeta')->where(['translator_id' => $translatorId]);

        if($request->input('type') == 'new_booking'){
            $query->where(['status' => $status])->whereDate('start_at','>=',date('Y-m-d')) ;
        }
        if($request->input('type') == 'today_booking'){
            $query->where(['status' => $status])->whereDate('start_at',date('Y-m-d')) ;
        }
        if($request->input('type') == 'upcoming_booking'){
            $query->where(function($query) use ($status) {
                $query->where('status', $status)
                      ->orWhere('status', 'accept');
            })->whereDate('start_at', '>', now()->toDateString());
            // $query->whereDate('start_at','>',date('Y-m-d'))->orWhere('status','accept');
        }


        $booking = $query->orderBy('created_at','desc')->get();
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function updateClientStatus($id , $status ='')
    {
        $query = Booking::where(['id' => $id])->update(['status' => $status]);
        return response()->json(['message' => 'status Updated.' ,'data' =>$query ,'status' => true],200);
    }

    public function updateTranslatorStatus($id , $status ='')
    {
        $query = Booking::where(['id' => $id])->update(['status' => $status]);

        return response()->json(['message' => 'status Updated.' ,'data' => $query ,'status' => true],200);
    }

    public function store(Request $request)
    {


        $request->validate([
            'client_id' => 'required|exists:users,id',
            'translator_id' => 'required|exists:users,id',
            'payment_type' => 'required|in:fix,hourly',
            'present_rate' => 'required|integer',
            'availability' => 'required|in:remote,hybrid,onsite',
            'status' => 'required|in:accept,reject,cancel,in-process,approved,reject,disputed,pending,mark-completed',
           // 'work_status' => 'required|in:approved,reject,disputed,pending',
            'payment_status' => 'required|in:paid,escrow,hold,dispute,none',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);


        $user = User::where(['id' => $request->input('client_id')])->get()->first();
        if(!empty( $user) && $user->user_type == 'client'){
            $booking = Booking::create($request->except('work_status'));
        }else{
            return response()->json(['message' => 'This User is not client. You must have client account to get hire' ,'data' =>[] ,'status' => true],422);
        }


        return response()->json(['message' => 'Booking added successfully.' ,'data' =>$booking ,'status' => true],200);

    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return response()->json(['message' => 'Job not found'], 404);
        }
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' => $booking ,'status' => true],200);

    }
    public function destroy($id)
    {
        $job = Booking::find($id);

        if (is_null($job)) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $job->delete();
        return response()->json(['message' => 'Booking updated successfully.' ,'data' => $job ,'status' => true],200);

    }




}
