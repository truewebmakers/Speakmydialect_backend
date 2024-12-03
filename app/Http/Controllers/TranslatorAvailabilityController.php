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
            'translator_id' => 'required|exists:users,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $translatorId = $request->input('translator_id');
        $day = $request->input('day');

        // Get the current date (today)
        $currentDate = now();

        // Find the date for the given day of the current week
        $dayOfWeek = collect(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->flip();
        $dayIndex = $dayOfWeek->get($day);

        // Get the difference between today and the requested day
        $dayDate = $currentDate->startOfWeek()->addDays($dayIndex);

        // Fetch the availability slots for the given translator and day
        $availability = TranslatorAvailability::where(['translator_id' => $translatorId, 'day' => $day])->get();

        // Now we check for any bookings that exist for that translator and specific day
        $bookedSlots = Booking::where('translator_id', $translatorId)
            ->whereDate('start_at', $dayDate) // Check the specific date for bookings
            ->get(['start_at', 'end_at']); // Get the start and end times of the booking slots

        // Format booked slots as a collection of [start_at, end_at] ranges
        $bookedRanges = $bookedSlots->map(function ($booking) {
            return [
                'start_at' => $booking->start_at,
                'end_at' => $booking->end_at
            ];
        });

        // Map availability slots to check if the slot is booked
        $availabilityWithBookingStatus = $availability->map(function ($slot) use ($bookedRanges, $dayDate) {
            // Check if the slot's date is the same as the calculated dayDate
            if ($slot->start_at->isSameDay($dayDate)) {
                // Get the start and end times for the slot
                $slotStart = $slot->start_at;
                $slotEnd = $slot->end_at;

                // Check if the slot overlaps with any booked ranges
                $isBooked = $bookedRanges->contains(function ($booking) use ($slotStart, $slotEnd) {
                    // Check if there is an overlap: (start of slot is before end of booking)
                    // and (end of slot is after start of booking)
                    return ($slotStart < $booking['end_at'] && $slotEnd > $booking['start_at']);
                });

                return [
                    'id' => $slot->id,
                    'start_at' => $slot->start_at,
                    'end_at' => $slot->end_at,
                    'is_booked' => $isBooked, // Mark as booked if there's an overlap
                ];
            }

            return null;
        })->filter(); // Remove null values if there are any slots not matching the day

        return response()->json(['data' => $availabilityWithBookingStatus]);
    }



}
