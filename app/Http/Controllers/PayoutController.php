<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PayoutController extends Controller
{
    public function createCharge(Request $request)
    {
        $amount = $request->input('amount');
        $currency = $request->input('currency','aud');
        $source = $request->input('token');
        $description = $request->input('description');

        Stripe::setApiKey(config('services.stripe.secret'));

        $charge = Charge::create([
            'amount' => $amount * 100, // Amount in cents
            'currency' => $currency,
            'source' => $source,
            'description' => $description,
        ]);

        return response()->json($charge);
    }
}
