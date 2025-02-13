<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{TranslatorAvailability, Booking,BookingSlot};
use Illuminate\Support\Facades\Validator;

class TranslatorAvailabilityController extends Controller
{

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'translator_id' => 'required|exists:users,id',
            'availability' => 'required|array',
            'availability.*.is_enabled' => 'required|boolean',
            'slot_duration' => 'required|integer|min:1', // Ensure slot_duration is positive
            'availability.*.times' => 'array',
            //  'availability.*.times.*.start_time' => 'required|date_format:H:i',
            //'availability.*.times.*.end_time' => 'required|date_format:H:i|after:availability.*.times.*.start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete any existing availability for this translator
        TranslatorAvailability::where('translator_id', $request->translator_id)->delete();

        // Loop through each day's availability
        foreach ($request->availability as $day => $data) {
            $isEnabled = $data['is_enabled']; // Get the `is_enabled` status for the day

            // Loop through each timeslot for the day
            foreach ($data['times'] as $timeSlot) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $timeSlot['start_time']);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $timeSlot['end_time']);
                $slotDuration = $request->slot_duration;

                // Ensure the start_time is before end_time
                if ($startTime->greaterThanOrEqualTo($endTime)) {
                    return response()->json(['errors' => 'Start time must be earlier than end time.'], 422);
                }

                // Calculate the total number of minutes between start and end time
                $totalMinutes = $startTime->diffInMinutes($endTime);

                // Split the total duration into chunks of slot_duration
                for ($i = 0; $i < floor($totalMinutes / $slotDuration); $i++) {
                    // Calculate start time for the current slot
                    $slotStart = $startTime->copy()->addMinutes($i * $slotDuration);

                    // Calculate end time for the current slot
                    $slotEnd = $slotStart->copy()->addMinutes($slotDuration);

                    // Ensure that we don't exceed the actual end time
                    if ($slotEnd->greaterThan($endTime)) {
                        break;
                    }

                    // Insert or update the slot in the database with slot_duration
                    TranslatorAvailability::updateOrInsert(
                        [
                            'translator_id' => $request->translator_id,
                            'day' => $day,
                            'start_time' => $slotStart->format('H:i'),
                        ],
                        [
                            'end_time' => $slotEnd->format('H:i'),
                            'is_enabled' => $isEnabled,
                            'slot_duration' => $slotDuration, // Store slot_duration in the database
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        return response()->json(['message' => 'Availability added successfully'], 201);
    }




    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'translator_id' => 'required|exists:users,id',
    //         'availability' => 'required|array',
    //         'availability.*.is_enabled' => 'required|boolean',
    //         'slot_duration' => 'required',
    //         // 'availability.*.times' => 'array', // Ensure 'times' is an array
    //         // 'availability.*.times.*.start_time' => 'required|date_format:H:i',
    //         // 'availability.*.times.*.end_time' => 'required|date_format:H:i|after:availability.*.times.*.start_time',
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // Delete any existing availability for this translator
    //     TranslatorAvailability::where('translator_id', $request->translator_id)->delete();

    //     // Save new availability data
    //     // foreach ($request->availability as $day => $data) {
    //     //     $isEnabled = $data['is_enabled']; // Get the `is_enabled` status for the day

    //     //     foreach ($data['times'] as $timeSlot) {
    //     //         TranslatorAvailability::updateOrInsert(
    //     //             [
    //     //                 'translator_id' => $request->translator_id,
    //     //                 'day' => $day,
    //     //                 'start_time' => $timeSlot['start_time']
    //     //             ],
    //     //             [
    //     //                 'end_time' => $timeSlot['end_time'],
    //     //                 'is_enabled' => $isEnabled, // Add `is_enabled` value
    //     //                 'updated_at' => now(),
    //     //             ]
    //     //         );
    //     //     }
    //     // }





    //     return response()->json(['message' => 'Availability added successfully'], 201);
    // }

    public function index($translatorId)
    {
        $availability = TranslatorAvailability::where('translator_id', $translatorId)->get();

        // Check if there is any availability and get the slot_duration from the first item
        $slotDuration = $availability->isNotEmpty() ? $availability->first()->slot_duration : null;

        return response()->json([
            'data' => $availability,
            'slot_duration' => $slotDuration
        ]);
    }
    public function getSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'translator_id' => 'required',
            'day' => 'required',
            'currentDate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $translatorId = $request->input('translator_id');
        $day = $request->input('day');
        $currentDate = $request->input('currentDate');

        // Ensure $currentDate is in the right format, e.g., 'Y-m-d'
        $currentDate = \Carbon\Carbon::parse($currentDate)->format('Y-m-d');

        // Fetch booked slots from BookingSlot table for the given day and translator
        $bookedSlots = BookingSlot::whereDate('start_at', '=', $currentDate)
            ->get();

        // Fetch translator's availability for the given day
        $availability = TranslatorAvailability::where([
            'translator_id' => $translatorId,
            'day' => $day
        ])->get();

        // Loop through the booked slots and filter out availability slots that are already booked
        $filteredAvailability = $availability->filter(function ($availableSlot) use ($bookedSlots, $currentDate) {
            // Combine the current date with availability's start and end time
            $availabilityStart = \Carbon\Carbon::parse($currentDate . ' ' . $availableSlot->start_at);
            $availabilityEnd = \Carbon\Carbon::parse($currentDate . ' ' . $availableSlot->end_at);

            // Check if the slot is booked
            foreach ($bookedSlots as $bookedSlot) {
                $bookedStart = \Carbon\Carbon::parse($bookedSlot->start_at);
                $bookedEnd = \Carbon\Carbon::parse($bookedSlot->end_at);

                // Compare start and end times using Carbon's comparison methods
                // If the availability slot matches a booked slot, it is considered taken and excluded
                if ($availabilityStart->equalTo($bookedStart) && $availabilityEnd->equalTo($bookedEnd)) {
                    return false; // Exclude this slot because it's already booked
                }
            }

            return true; // Include the slot if it's not booked
        });

        // Return filtered availability data
        return response()->json(['data' => $filteredAvailability->values() , 'availability' => $availability, 'bookedSlots' => $bookedSlots ]);
    }






    // public function getSlots(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'translator_id' => 'required',
    //         'day' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $translatorId = $request->input('translator_id');
    //     $day = $request->input('day');
    //     $currentDate = $request->input('currentDate');


    //     // Fetch booked slots for the given day
    //     $bookedSlot = Booking::where(column: [
    //         'day' => $day,
    //         'start_at' => $currentDate
    //     ])->first();

    //     // Initialize booked time range
    //     $bookedStart = $bookedSlot ? $bookedSlot->start_time : null;
    //     $bookedEnd = $bookedSlot ? $bookedSlot->end_time : null;

    //     // Fetch translator's availability
    //     $availability = TranslatorAvailability::where(['translator_id' => $translatorId, 'day' => $day])->get();

    //     // Remove the booked slot from availability
    //     $filteredAvailability = $availability->filter(callback: function (TModel $slot) use ($bookedStart, $bookedEnd) {
    //         // Include only slots outside the booked time range
    //         return is_null($bookedStart) ||
    //             is_null($bookedEnd) ||  ($slot->start_time >= $bookedEnd || $slot->end_time <= $bookedStart);
    //     });

    //     return response()->json(['data' => $filteredAvailability->values()]);
    // }


    // public function getSlots( Request $request )
    // {
    //     $validator = Validator::make($request->all(), [
    //         'translator_id' => 'required',
    //         'day' => 'required'
    //     ]);


    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }
    //     $translatorId =  $request->input('translator_id');
    //     $day =  $request->input('day');

    //     $availability = TranslatorAvailability::where(['translator_id' => $translatorId ,'day' => $day])->get();
    //     return response()->json(['data' => $availability]);
    // }
}
