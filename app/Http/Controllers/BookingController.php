<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{


    public function getPayoutBooking($translatorId)
    {
        $query = Booking::where(['translator_id' => $translatorId]);
        $booking = $query->get();
        return response()->json(['message' => 'Booking added successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function getBookingForClient($clientId , $status='')
    {
        $query = Booking::where(['client_id' => $clientId]);
        if($status){
            $query->where(['status' => $status]);
        }
        $booking = $query->get();
        return response()->json(['message' => 'Booking added successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function getBookingForTranslator($translatorId , $status ='')
    {
        $query = Booking::where(['translator_id' => $translatorId]);
        if($status){
            $query->where(['status' => $status]);
        }
        $booking = $query->get();
        return response()->json(['message' => 'Booking added successfully.' ,'data' =>$booking ,'status' => true],200);
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
            'status' => 'required|in:accept,reject,cancel,in-process',
            'work_status' => 'required|in:approved,reject,disputed,pending',
            'payment_status' => 'required|in:paid,escrow,hold,dispute,none',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);

        $booking = Booking::create($request->all());
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
