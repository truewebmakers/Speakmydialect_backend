<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payout;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class PayoutController extends Controller
{


    public function generateInvoice(Request $request, $id)
    {
        // $data = [
        //     'created_at' => null,
        //     'id' => null,
        //     'description' => null,
        //     'stripe_id' => null,
        //     'amount' => null,
        //     'paid' => null,
        // ];
        //  return view('invoice.invoice',compact('id','data'));
        // Retrieve the Payout model instance or return a 404 if not found
        $payout = Payout::find($id);

            if (!$payout) {
                return abort(404, 'Payout not found');
            }

            // Convert model instance to an array
            $data = $payout->toArray();

            // Load the view with data and generate PDF
            $pdf = Pdf::loadView('invoice.invoice', ['data' => $data ,'id' => $id]);

            if($request->input('download')=='true'){
                return $pdf->download('invoice.pdf');
            }else{
                return $pdf->stream('invoice.pdf');
            }





    }


    public function createCharge(Request $request)
    {


        try {


            $amount = $request->input('amount');
            $currency = $request->input('currency', 'aud');
            $token = $request->input('token');
            $description = $request->input('description');
            $jobId = $request->input('job_id');
            $name = $request->input('user_name');
            $email = $request->input('email');


            $booking = Booking::find($jobId);

            if( $booking->payment_status == 'escrow' ||  $booking->payment_status == 'paid'){

                return response()->json([
                    'status' => false,
                    'message' => 'You already paid for this Job. Currently Payment is on "'.$booking->payment_status.'" State.'
                ]);
            }






            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                'amount' => $amount * 100, // Amount in cents
                'currency' => $currency,
                'source' => $token,
                'description' => $description,
                'metadata' => [
                    'name' => $name,
                    'email' => $email,
                ],
            ]);

            $chargeData = $charge->jsonSerialize();



            // Create an entry in the payouts table
            // Payout::create([
            //     'stripe_id' => $chargeData['id'],
            //     'balance_transaction' => $chargeData['balance_transaction'],
            //     'amount' => $chargeData['amount'],
            //     'amount_captured' => $chargeData['amount_captured'],
            //     'amount_refunded' => $chargeData['amount_refunded'],
            //     'currency' => $chargeData['currency'],
            //     'description' => $chargeData['description'],
            //     'statement_descriptor' => $chargeData['statement_descriptor'] ?? null,
            //     'receipt_url' => $chargeData['receipt_url'],
            //     'captured' => $chargeData['captured'],
            //     'paid' => $chargeData['paid'],
            //     'disputed' => $chargeData['disputed'],
            //     'payment_method' => $chargeData['payment_method'],
            //     'payment_method_brand' => $chargeData['payment_method_details']['card']['brand'],
            //     'payment_method_last4' => $chargeData['payment_method_details']['card']['last4'],
            //     'payment_method_country' => $chargeData['payment_method_details']['card']['country'],
            //     'payment_method_funding' => $chargeData['payment_method_details']['card']['funding'],
            //     'payment_method_exp_month' => $chargeData['payment_method_details']['card']['exp_month'],
            //     'payment_method_exp_year' => $chargeData['payment_method_details']['card']['exp_year'],
            //     'payment_method_checks' => json_encode($chargeData['payment_method_details']['card']['checks']),
            //     'payment_method_installments' => json_encode($chargeData['payment_method_details']['card']['installments'] ?? null),
            //     'fraud_details' => json_encode($chargeData['fraud_details']),
            //     'metadata' => json_encode($chargeData['metadata']),
            //     'billing_address_city' => $chargeData['billing_details']['address']['city'],
            //     'billing_address_country' => $chargeData['billing_details']['address']['country'],
            //     'billing_address_line1' => $chargeData['billing_details']['address']['line1'],
            //     'billing_address_line2' => $chargeData['billing_details']['address']['line2'],
            //     'billing_address_postal_code' => $chargeData['billing_details']['address']['postal_code'],
            //     'billing_address_state' => $chargeData['billing_details']['address']['state'],
            //     'billing_email' => $chargeData['billing_details']['email'],
            //     'billing_name' => $chargeData['billing_details']['name'],
            //     'billing_phone' => $chargeData['billing_details']['phone'],
            // ]);

            // $invoice = $this->retrieveInvoice($chargeData['id']);

          $payout =  Payout::create([
                'job_id' => $jobId,
                'stripe_id' => $chargeData['id'],
                'amount' => $chargeData['amount'],
                'currency' => $chargeData['currency'],
                'description' => $chargeData['description'],
                'receipt_url' => $chargeData['receipt_url'],
                'captured' => $chargeData['captured'],
                'paid' => $chargeData['paid'],
                'payment_method' => $chargeData['payment_method'],
                'payment_method_brand' => $chargeData['payment_method_details']['card']['brand'],
                'payment_method_last4' => $chargeData['payment_method_details']['card']['last4'],
                'payment_method_exp_month' => $chargeData['payment_method_details']['card']['exp_month'],
                'payment_method_exp_year' => $chargeData['payment_method_details']['card']['exp_year'],
                'billing_address_postal_code' => $chargeData['billing_details']['address']['postal_code'],
                'billing_email' => $chargeData['billing_details']['email'],
                'billing_name' => $chargeData['billing_details']['name'],
                'invoice_url' =>  $chargeData['receipt_url']

            ]);

            $booking->update([
                'payment_status' => 'escrow',
                'payment_by_client_at' => date('Y-m-d H:i:s')
            ]);


            return response()->json([
                'status' => true,
                'message' => 'Message Successful',
                'invoice_url' =>  route('view.invoice',$payout->id)
            ]);
        } catch (\Exception $e) {

            Booking::find($jobId)->update([
                'payment_status' => 'failed',
                'payment_by_client_at' => null

            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function retrieveInvoice($chargeId)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Retrieve the charge
        $charge = Charge::retrieve($chargeId);

        // Retrieve the invoice associated with the charge
        if ($charge->invoice) {
            $invoice = Invoice::retrieve($charge->invoice);
            return isset($invoice->hosted_invoice_url) ? $invoice : null;
        }

        return null; // No invoice associated with this charge
    }

    public function getInvoice($clientId){

      $invoices =   Booking::with('payout')->where(['client_id' => $clientId])->get();
        return response()->json([
            'status' => true,
            'message' => 'getpayout Successfull',
            'data' => $invoices
        ]);

    }
}
