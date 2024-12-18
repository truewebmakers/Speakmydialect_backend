<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{TranslatorAvailability,Booking};
use Illuminate\Support\Facades\Validator;
class TranslatorAvailabilityController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'translator_id' => 'required|exists:users,id',
            'availability' => 'required|array',
            'availability.*.is_enabled' => 'required|boolean',
            // 'availability.*.times' => 'array', // Ensure 'times' is an array
            // 'availability.*.times.*.start_time' => 'required|date_format:H:i',
            // 'availability.*.times.*.end_time' => 'required|date_format:H:i|after:availability.*.times.*.start_time',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete any existing availability for this translator
        TranslatorAvailability::where('translator_id', $request->translator_id)->delete();

        // Save new availability data
        foreach ($request->availability as $day => $data) {
            $isEnabled = $data['is_enabled']; // Get the `is_enabled` status for the day

            foreach ($data['times'] as $timeSlot) {
                TranslatorAvailability::updateOrInsert(
                    [
                        'translator_id' => $request->translator_id,
                        'day' => $day,
                        'start_time' => $timeSlot['start_time']
                    ],
                    [
                        'end_time' => $timeSlot['end_time'],
                        'is_enabled' => $isEnabled, // Add `is_enabled` value
                        'updated_at' => now(),
                    ]
                );
            }
        }

        return response()->json(['message' => 'Availability added successfully'], 201);
    }

    public function index($translatorId)
    {
        $availability = TranslatorAvailability::where('translator_id', $translatorId)->get();
        return response()->json(['data' => $availability]);
    }
    public function getSlots(Request $request)
{
    $validator = Validator::make($request->all(), [
        'translator_id' => 'required',
        'day' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $translatorId = $request->input('translator_id');
    $day = $request->input('day');

    // Fetch booked slots for the given day
    $bookedSlot = Booking::where([
        'day' => $day,
        'start_at' => date('Y-m-d')
    ])->first();

    // Initialize booked time range
    $bookedStart = $bookedSlot ? $bookedSlot->start_time : null;
    $bookedEnd = $bookedSlot ? $bookedSlot->end_time : null;

    // Fetch translator's availability
    $availability = TranslatorAvailability::where(['translator_id' => $translatorId, 'day' => $day])->get();

    // Remove the booked slot from availability
    $filteredAvailability = $availability->filter(function ($slot) use ($bookedStart, $bookedEnd) {
        // Include only slots outside the booked time range
        return is_null($bookedStart) ||
               is_null($bookedEnd) ||
               ($slot->start_time >= $bookedEnd || $slot->end_time <= $bookedStart);
    });

    return response()->json(['data' => $filteredAvailability->values()]);
}


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
