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

    public function getSlots( Request $request )
    {
        $validator = Validator::make($request->all(), [
            'translator_id' => 'required|exists:users,id',
            'day' => 'required',
            'slot_start' => 'required',
            'slot_end' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $translatorId =  $request->input('translator_id');
        $day =  $request->input('day');
        $slot_start =  $request->input('slot_start');
        $slot_end =  $request->input('slot_end');
        $bookingExist = Booking::where('translator_id', $translatorId)
        ->where('day', $day)
        ->where('start_time', $slot_start)
        ->where('end_time', $slot_end)
        ->exists();

        $availability = TranslatorAvailability::where([
            'translator_id' => $translatorId,
            'day' => $day
        ])->get();

        // Add the booking status to each availability slot
        $availabilityWithBookingStatus = $availability->map(function ($slot) use ($bookingExist, $slot_start, $slot_end) {
            // Compare the availability slot with the requested slot times
            $isBooked = $slot->start_time === $slot_start && $slot->end_time === $slot_end;

            return [
                'id' => $slot->id,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'is_booked' => $isBooked || $bookingExist, // Mark the slot as booked if the booking exists
            ];
        });

    return response()->json(['data' => $availabilityWithBookingStatus]);


    }
}
