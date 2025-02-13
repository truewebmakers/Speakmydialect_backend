<?php

namespace App\Http\Controllers;

use App\Helpers\TwilioHelper;
use Illuminate\Http\Request;
use App\Models\{Booking,BookingSlot};
use App\Models\User;
use Carbon\Carbon;

class BookingController extends Controller
{

    public function getPayoutBooking($translatorId,Request $request)
    {
        $query = Booking::where(['translator_id' => $translatorId]);
        $booking = $query->get();
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' =>$booking ,'status' => true],200);
    }


    public function ApprovedBookingPaidStatus(Request $request,$bookindId)
    {
        $request->validate(['payment_status' => 'required']);
        $paymentStatus = $request->input('payment_status');
        $query = Booking::find($bookindId)->update(['payment_status' =>  $paymentStatus , 'payment_by_translator_at' => now()->toDateString()]);
        return response()->json(['message' => 'Booking updated successfully.','data' => $query,'status' => true],200);
    }


    public function getApprovedBookings(Request $request)
    {
        $query = Booking::where(['status' => 'approved']);

        $booking = $query->orderBy('id','desc')->get();
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function getBookingForClient($clientId , $status='',Request $request)
    {
        $query = Booking::with('translator','translatorMeta','slots')->where(['client_id' => $clientId]);

        if($request->input('type') == 'upcoming_booking'){
            $query->where(function($query) use ($status) {
                $query->where('status', $status)
                      ->orWhere('status', 'accept');
            })->whereDate('start_at', '>', now()->toDateString());

        }
        if($request->input('type') == 'current_booking'){

            $query->where(function($query) use ($status) {
                $query->where('status', $status)
                      ->orWhere('status', 'in-process');
            })->whereDate('start_at',  now()->toDateString());
            // $query->where('status', $status)->whereDate('start_at',date('Y-m-d'));
        }

        if($request->input('type') == 'completed_booking'){
            $query->where('status', $status);
        }

        if($request->input('type') == 'approved_booking'){
            $query->where('status', $status);
        }

        if($request->input('type') == 'canceled_booking'){
            $query->where('status', $status);
        }
        if($request->input('type') == 'rejected_booking'){
            $query->where('status', $status);
        }


        $booking = $query->orderBy('created_at','desc')->get();
        return response()->json(['message' => 'Booking fetched successfully.' ,'data' =>$booking ,'status' => true],200);
    }

    public function getBookingForTranslator($translatorId , $status ='' , Request $request)
    {
        $query = Booking::with('client','clientMeta','slots')->where(['translator_id' => $translatorId]);

        if($request->input('type') == 'new_booking'){
            $query->where(['status' => $status])->whereDate('start_at','>=',date('Y-m-d')) ;
        }
        if($request->input('type') == 'today_booking'){
            $query->where(['status' => $status])->whereDate('start_at',date('Y-m-d')) ;
        }
        if($request->input('type') == 'completed_booking'){
            $query->where('status', $status);
        }

        if($request->input('type') == 'approved_booking'){
            $query->where('status', $status);
        }

        if($request->input('type') == 'canceled_booking'){
            $query->where('status', $status);
        }
        if($request->input('type') == 'rejected_booking'){
            $query->where('status', $status);
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


    public function updateClientStatus($id, $status = '')
    {
        // Retrieve the booking details and interpreter information
        $booking = Booking::with('translator','client')->where(['id' => $id])->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.', 'status' => false], 404);
        }

        // Extract interpreter and job details
        $interpreterName = $booking->translator->fname;
        $clientName = $booking->client->fname;
        $jobTitle = $booking->job_title;
        $jobDate = $booking->created_at;
        $phoneN= $booking->translator->phone_number;
        $countryCode = $booking->translator->country_code;
        // return response()->json(['message' => $booking, 'status' => false], 404);
        // Update the booking status
        // return response()->json(['message' => $phoneN, 'cc' => $countryCode, 'status' => false], 400);
        $query = Booking::where(['id' => $id])->update(['status' => $status]);

        // Define the message template based on the status
        $message = '';
        switch ($status) {
            case 'pending':
                $message = "Hi $interpreterName,\nYou have a new job request on the Speak My Dialect app!\nJob Title: $jobTitle\nDate: $jobDate\nClick here to review and respond: [link]";
                break;
            case 'accept':
                $message = "Hi $interpreterName,\nYour job on the Speak My Dialect app has been confirmed.\nJob Title: $jobTitle\nDate: $jobDate\nPlease reach 15 minutes before the scheduled time to prepare.";
                break;
            case 'mark-completed':
                $message = "Hi $interpreterName,\nThank you for completing your job on the Speak My Dialect app!\nJob Title: $jobTitle\nDate: $jobDate\nYour submission has been received and is under review.";
                break;
            case 'approved':
                $message = "Hi $interpreterName,\nGreat news! Your completed job on the Speak My Dialect app has been approved.\nJob Title: $jobTitle\nDate: $jobDate\nYour payment will be processed shortly.";
                break;
            case 'cancel':
                $message = "Hi $interpreterName,\nThe following job on the Speak My Dialect app has been canceled by the client: $clientName \nJob Title: $jobTitle\nDate: $jobDate\nNo further action is required.";
                break;
            default:
                return response()->json(['message' => 'Invalid status provided.', 'status' => false], 400);
        }

        // Send the message via Twilio
        TwilioHelper::StatusMessage($countryCode, $phoneN, $message);

        return response()->json(['message' => 'Status updated and message sent.', 'data' => $query, 'status' => true], 200);
    }


    public function updateTranslatorStatus($id, $status = '')
    {
        $booking = Booking::with('client')->where(['id' => $id])->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found.', 'status' => false], 404);
        }

        // return response()->json(['message' => 'Status updated and message sent.', 'data' => $booking, 'status' => true], 200);
        $clientName = $booking->client->fname;
        $jobTitle = $booking->job_title;
        $jobDate = $booking->created_at;
        $phoneNumber = $booking->client->phone_number;
        $countryCode = $booking->client->country_code;
        $query = Booking::where(['id' => $id])->update(['status' => $status]);

        $message = '';
        switch ($status) {
            case 'Booked':
                $message = "Hi $clientName,\nYour job request on the Speak My Dialect app has been successfully submitted.\nJob Title: $jobTitle\nDate: $jobDate\nWe will notify you once an interpreter accepts the job.";
                break;
            case 'accept':
                $interpreterName = $booking->interpreter->fname ?? ''; // Replace with actual interpreter field if needed
                $message = "Hi $clientName,\nYour job on the Speak My Dialect app has been confirmed and is scheduled as follows:\nJob Title: $jobTitle\nDate: $jobDate\nInterpreter: $interpreterName";
                break;
            case 'mark-completed':
                $message = "Hi $clientName,\nThe job you requested on the Speak My Dialect app has been successfully completed.\nJob Title: $jobTitle\nDate: $jobDate\nThank you for using our services.";
                break;
            case 'approved':
                $message = "Hi $clientName,\nThe job you requested on the Speak My Dialect app has been approved and closed.\nJob Title: $jobTitle\nDate: $jobDate\nWe hope to serve you again soon!";
                break;
            case 'disputed':
                $message = "Hi $clientName,\nYour job on the Speak My Dialect app has been canceled successfully.\nJob Title: $jobTitle\nDate: $jobDate\nIf you need assistance or wish to reschedule, please contact our support team.";
                break;
            default:
                return response()->json(['message' => 'Invalid status provided.', 'status' => false], 400);
        }

        // Send the message via Twilio
        TwilioHelper::StatusMessage($countryCode, $phoneNumber, $message);

        return response()->json(['message' => 'Status updated and message sent.', 'data' => $query, 'status' => true], 200);
    }


    // public function updateTranslatorStatus($id , $status ='')
    // {
    //     $query = Booking::with('client')->where(['id' => $id])->update(['status' => $status]);
    //     TwilioHelper::StatusMessage($country_code,$phoneNumber,$message);
    //     return response()->json(['message' => 'status Updated.' ,'data' => $query ,'status' => true],200);
    // }



    public function BookingCounts(Request $request)
    {
        $id = $request->input('id');
        // $status = $request->input('status');
        $userType = $request->input('userType');
        if($userType == 'client'){
            $query = Booking::where(['client_id' => $id]);

        }else if($userType == 'translator'){
            $query = Booking::where(['translator_id' => $id]);

        }else{
            $query = new Booking;
        }
        $users = $query->get();
        $userCounts = [];
        $statuses = [
            'accept' => 0,
            'reject' => 0,
            'cancel' => 0,
            'in-process' => 0,
            'mark-completed' => 0,
            'approved' => 0,
            'disputed' => 0,
            'pending' => 0
        ];
        foreach($users as $item){
            if(array_key_exists($item->status,$statuses)){
                $statuses[$item->status]++;
            }

        }

        return response()->json(['message' => 'count fecthed.' ,'data' => $statuses ,'status' => true],200);
    }

    public function store(Request $request)
    {
        // duration: { hours: 0, minutes: 0 },
    //    return response()->json(['message' => $request->input('slots')], 200);

        $request->validate([
            'client_id' => 'required|exists:users,id',
            'translator_id' => 'required|exists:users,id',
            'job_title' => 'required|max:155',
            'payment_type' => 'required|in:fix,hourly',
            'present_rate' => 'required|integer',
            'availability' => 'required|in:phone,video-call,in-person',
            'status' => 'required|in:accept,reject,cancel,in-process,approved,reject,disputed,pending,mark-completed',
             // 'work_status' => 'required|in:approved,reject,disputed,pending',
             'payment_status' => 'required|in:paid,escrow,hold,dispute,none',
             'duration' => 'required',
            // 'start_at' => 'required',
            // 'end_at' => 'required',
            'slots' => 'required|array'
        ]);

        $slots = $request->input('slots');
        $start_at = $request->input('start_at');


        $user = User::where(['id' => $request->input('client_id')])->get()->first();
        if(!empty( $user) && $user->user_type == 'client'){

            $dataArr = $request->except('work_status','slots');
            $duration_in_minutes = 0;
            $booking = Booking::create($dataArr);

            $slotArr = [];
            foreach ($slots as $slot) {


                $startDateTime = Carbon::parse($start_at . ' ' . $slot['start_time']); // Concatenate date and time for start
                $endDateTime = Carbon::parse($start_at . ' ' . $slot['end_time']); // Concatenate date and time for end

                $durationInMinutes = $startDateTime->diffInMinutes($endDateTime); // Calculate duration

                $slotArr['booking_id'] = $booking->id;
                $slotArr['start_at'] = $startDateTime; // Use the combined start datetime
                $slotArr['end_at'] = $endDateTime; // Use the combined end datetime
                $duration_in_minutes = $durationInMinutes;


                // $startTime = Carbon::parse($slot['start_time']);
                // $endTime = Carbon::parse($slot['end_time']);
                // $durationInMinutes = $startTime->diffInMinutes($endTime);
                // $slotArr['booking_id'] = $booking->id;
                // $slotArr['start_at'] = $startTime;
                // $slotArr['end_at'] = $endTime;
                // $duration_in_minutes = $durationInMinutes;

                BookingSlot::create($slotArr);
            }
        }else{
            return response()->json(['message' => 'This user is not a client. You must have a client account to be hired.' ,'data' =>[] ,'status' => true ],422);
        }
        return response()->json(['message' => 'Booking added successfully.' ,'data' =>$booking ,'status' => true , 'slots' =>  $slots ,'duration_in_minutes' => $duration_in_minutes ],200);

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
