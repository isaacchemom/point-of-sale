<?php

namespace App\Http\Controllers;

use App\CurrentStkPushRequest;
use App\Events\MpesaC2BDataReceived;
use App\Events\MpesaPaymentDataReceived;
use App\Events\MpesaSTKPushDataProcessed;
use App\Events\StkPushSent;
use App\GatewaySetting;
use App\Http\MpesaSetting;
use App\MpesaC2BPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use App\MpesaPayment;
use App\MpesaTransaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function index()
    {

        return view('payment_gateways.create');
    }



    public function getPassword($user_id)
    {
        $user = User::find($user_id);
        $business_id = $user->business_id;
        $settings = GatewaySetting::where('provider', 'mpesa')->where('business_id', $business_id)->first();

        $mpesa_passkey = $settings->mpesa_passkey;
        $mpesa_shortcode = $settings->mpesa_shortcode;

        $timestamp = Carbon::rawParse('now')->format('YmdHms'); //Helps us get current date and time
        $password = base64_encode($mpesa_shortcode . $mpesa_passkey . $timestamp);
        return $password;
    }
    public function completeTransaction(Request $request)
    {
        $transaction = MpesaTransaction::find($request->transaction_id);
        if ($transaction) {
            $transaction->status = 1;
            $transaction->save();
            return response()->json(['status' => 'success', 'message' => 'Transaction Completed Successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Transaction Not Found']);
        }
    }
    public function getMpesaTransactions(Request $request)
    {
        // Log::info("GET TRANSACTIONS Request Data:" . json_encode($request->all()));

        try {
            // $latest_transaction = MpesaTransaction::where('cashier_id', $request->user_id)->first();
            // $MerchantRequestID = $latest_transaction->MerchantRequestID;
            // $CheckoutRequestID = $latest_transaction->CheckoutRequestID;

            $mpesa_transactions = MpesaTransaction::where('MerchantRequestID', $request->MerchantRequestID)->where('CheckoutRequestID', $request->CheckoutRequestID)->first();
            if ($mpesa_transactions && $mpesa_transactions->status == 3) {
                return response()->json(['status' => 'success', 'message' => 'Payment received. Finalizing processing...', 'data' => $mpesa_transactions]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Transaction not found']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function cancelTransaction(Request $request)
    {
        $phone = $request->phone;  //We use request to get the phone number that the user inputs for the form.
        $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
        $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
        $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;
        $cash = $request->cash;
        $u_amount = str_replace(',', '', $request->amount);
        $amount = explode('.', $u_amount);
        $formarted_amount = $amount[0];

        if ($cash >= $formarted_amount) {
            return response()->json(['status' => 'error', 'message' => 'Cash amount cannot be greater than the total amount']);
        }
        $total_amount = (int)$formarted_amount - (int)$cash;

        try {
            //  $latest_transaction = MpesaTransaction::where('cashier_id', $request->user_id)->first();
            // $MerchantRequestID = $latest_transaction->MerchantRequestID;
            // $CheckoutRequestID = $latest_transaction->CheckoutRequestID;

            $mpesa_transaction = MpesaTransaction::where('cashier_id', $request->user_id)->where('phone', $phone)->where('total_amount', $total_amount)->latest()->first();
            if ($mpesa_transaction && $mpesa_transaction->status == 0) {
                $mpesa_transaction->status = 2;
                $mpesa_transaction->save();
                return response()->json(['status' => 'success', 'message' => 'Transaction has been cancelled...', 'data' => $mpesa_transaction]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function generateAccessToken($user_id = 1)
    {
        $user = User::find($user_id);
        $business_id = $user->business_id;
        $settings = GatewaySetting::where('provider', 'mpesa')->where('business_id', $business_id)->first();

        $mpesa_consumerkey = null;
        $mpesa_consumersecret = null;
        $url = null;

        $mpesa_consumerkey = $settings->mpesa_consumerkey;

        $mpesa_consumersecret = $settings->mpesa_consumersecret;

        $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";


        $credentials = base64_encode($mpesa_consumerkey . ":" . $mpesa_consumersecret);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);
        // Log::info("access token: " . $curl_response);
        $data = [
            'env' => 'live',
            'consumer key' => $mpesa_consumerkey,
            'consumer secret' => $mpesa_consumersecret,
            'access token' => $access_token
        ];
        if ($access_token == null) {
            $message = 'Something went wrong, please check your credentials';
            return response()->json(['status' => 'error', 'message' => $message]);
        }
        // dd($data);
        return $access_token->access_token;
    }


    /** Lipa na M-PESA STK Push method **/
    public function stkPushRequest(Request $request)
    {
        $user = User::find($request->user_id);
        $business_id = $user->business_id;
        $settings = GatewaySetting::where('provider', 'mpesa')->where('business_id', $business_id)->first();
        if (!$settings) {
            return response()->json(['status' => 'error', 'message' => 'Mpesa settings not found']);
        }

        if (!$request->amount) {
            return response()->json(['status' => 'error', 'message' => 'Amount is required']);
        } elseif (!$request->phone) {
            return response()->json(['status' => 'error', 'message' => 'Phone number is required']);
        }
        $cash = $request->cash;
        // Log::info(json_encode($request->all()));
        $u_amount = str_replace(',', '', $request->amount);
        $amount = explode('.', $u_amount);
        $formarted_amount = $amount[0];

        if ($cash >= $formarted_amount) {
            return response()->json(['status' => 'error', 'message' => 'Cash amount cannot be greater than the total amount']);
        }

        $total_amount = (int)$formarted_amount - (int)$cash;

        // $formarted_amount =1;
        $phone = $request->phone;  //We use request to get the phone number that the user inputs for the form.
        $phone = (substr($phone, 0, 1) == "+") ? str_replace("+", "", $phone) : $phone;
        $phone = (substr($phone, 0, 1) == "0") ? preg_replace("/^0/", "254", $phone) : $phone;
        $phone = (substr($phone, 0, 1) == "7") ? "254{$phone}" : $phone;

        $current_transaction = MpesaTransaction::where('cashier_id', $request->user_id)->where('phone', $phone)->where('total_amount', $total_amount)->latest()->first();

        // $transac_status = $current_transaction->status;
        // if ($transac_status == 0) {
        //     // $transac->status = 2; //cancel transaction
        //     // $transac->save();
        //     Log::info('Another payment is in progress, please wait...');
        //     return false;
        // } else {
        $data = [
            'cashier_id' => $request->user_id,
            'phone' => $phone,
            'total_amount' => $total_amount
        ];


        $mpesa_transaction = MpesaTransaction::create($data);


        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $mpesa_shortcode = $settings->mpesa_shortcode;
        $transaction_type = $settings->mpesa_shortcode_type == 'till' ? 'CustomerBuyGoodsOnline' : 'CustomerPayBillOnline';


        try {
            //code...
            $curl = curl_init();
            if ($curl === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->generateAccessToken($user->id))); //setting custom header
            $domain = $_SERVER['HTTP_HOST'];
            $callback_url = "https://{$domain}/api/v1/mpesa/response";
            // Log::info('CallbackUrl:' . $callback_url);
            $curl_post_data = [
                //Use valid values for the parameters below
                'BusinessShortCode' => $mpesa_shortcode,
                'Password' => $this->getPassword($user->id),
                'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
                'TransactionType' => $transaction_type,
                'Amount' => $total_amount,
                'PartyA' => $phone,
                'PartyB' => $settings->mpesa_shortcode_type == 'paybill' ? $mpesa_shortcode : $settings->till_number,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callback_url,
                'AccountReference' => "Transaction",
                'TransactionDesc' => "Registration Fees Payment"
            ];
            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $curl_response = curl_exec($curl);
            // Log::info(curl_getinfo($curl, CURLINFO_HEADER_OUT));


            if ($curl_response === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
            // Log::info("Logged response RESPONSE:" . $curl_response);


            $decoded_res = json_decode($curl_response);
            if (isset($decoded_res->errorCode)) {
                // An error occurred
                $message = $decoded_res->errorMessage ?? 'An error occurred';
                $data = ['status' => 'error', 'message' => $message, 'error_code' => $decoded_res->errorCode];
                return response()->json($data);
            }
            $mpesa_transaction->MerchantRequestID = $decoded_res->MerchantRequestID;
            $mpesa_transaction->CheckoutRequestID = $decoded_res->CheckoutRequestID;
            $mpesa_transaction->save();

            $c2b = new MpesaC2BPayment();
            $c2b->MerchantRequestID = $decoded_res->MerchantRequestID;
            $c2b->CheckoutRequestID = $decoded_res->CheckoutRequestID;
            $c2b->save();

            $current_stk_push_request = new CurrentStkPushRequest();
            $current_stk_push_request->user_id = $user->id;
            $current_stk_push_request->MerchantRequestID = $decoded_res->MerchantRequestID;
            $current_stk_push_request->CheckoutRequestID = $decoded_res->CheckoutRequestID;
            $current_stk_push_request->c2b_id = $c2b->id;
            $current_stk_push_request->save();






            if ($decoded_res->ResponseDescription && $decoded_res->ResponseDescription == 'Success. Request accepted for processing') {
                $message = 'Thank you! A popup has been sent to the customer\'s phone, ask the customer to enter M-PESA pin to authorize the transaction.';
                $data = ['status' => 'success', 'message' => $message, 'data' => ['MerchantRequestID' => $decoded_res->MerchantRequestID, 'CheckoutRequestID' => $decoded_res->CheckoutRequestID]];
                event(new StkPushSent($user->id, $mpesa_transaction, $data));
            } else {
                $message = 'Something went wrong! please try again';
                $data = ['status' => 'error', 'message' => $message, $data => ['merchant_request_id' => $decoded_res->MerchantRequestID, 'checkout_request_id' => $decoded_res->CheckoutRequestID]];

                event(new StkPushSent($user->id, $mpesa_transaction, $data));
            }
        } catch (\Exception $e) {
            Log::info("There was an error. " . $e);
            $response = ['status' => 'error', 'message' => $e->getMessage(), 'error_code' => $e->getCode()];
            return response()->json($response);
        } finally {
            // Close curl handle unless it failed to initialize
            if (is_resource($curl)) {
                curl_close($curl);
            }
        }
        // }
    }

    public function processPayment(Request $request)
    {

        try {
            //code...
            $response = json_decode($request->getContent());
            // Log::info(json_encode($response));
            $mpesa_response = json_encode($response);
            $resCode = $response->Body->stkCallback->ResultCode;
            $message = $response->Body->stkCallback->ResultDesc;
            $MerchantRequestID = $response->Body->stkCallback->MerchantRequestID;
            $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;

            $transaction = MpesaTransaction::where('CheckoutRequestID', $CheckoutRequestID)
                ->where('MerchantRequestID', $MerchantRequestID)
                ->first();
            $resData = [];
            $data = [];
            if ($resCode == 0) {
                $resData = $response->Body->stkCallback->CallbackMetadata;
                $resData =  $response->Body->stkCallback->CallbackMetadata;
                $amountPaid = $resData->Item[0]->Value;
                $mpesaTransactionId = $resData->Item[1]->Value;
                $paymentPhoneNumber = isset($resData->Item[4]->Value) ? $resData->Item[4]->Value : $resData->Item[3]->Value;
                $data = [
                    'phone' => $paymentPhoneNumber,
                    'receipt_no' => $mpesaTransactionId,
                    'amount' => $amountPaid,
                    'mpesa_response' => $mpesa_response

                ];

                if (!empty($data)) {

                    $payment = MpesaPayment::create($data);
                    //change status, send message

                    if ($transaction) {
                        $transaction->status = 3;
                        $transaction->payment_id = $payment->id;
                        $transaction->save();
                        //send a message

                        $data['status'] = 'success';
                        $data['message'] = $message;

                        event(new  MpesaSTKPushDataProcessed($transaction->cashier_id, $data));
                    } else {
                        Log::info('DATA IS EMPTY... TRANSACTION NOT FOUND SAVED IN MPESA PAYMENTS TABLE');
                    }
                }
            } else {
                // Log::info("There was an error:  ".json_encode( $response));
                $errorData = ['status' => 'error', 'message' => $message];
                $pending_stkpush_request = CurrentStkPushRequest::where('MerchantRequestID',  $MerchantRequestID)->where('CheckoutRequestID', $CheckoutRequestID)->first();

                if ($pending_stkpush_request) {
                    $c2b_trans = MpesaC2BPayment::find($pending_stkpush_request->c2b_id);
                    if ($c2b_trans) {
                        $c2b_trans->delete();
                    }
                    $pending_stkpush_request->delete();
                }
                event(new  MpesaPaymentDataReceived($transaction->cashier_id, $errorData));
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::info('ERROR PROCESSING MPESA PAYMENT:' . $th->getMessage());

            // $errorData = ['status' => 'error', 'message' => $th->getMessage()];
            // event(new  MpesaPaymentDataReceived($transaction->cashier_id, $errorData));

        }
    }

    public function handleMpesaConfirmation(Request $request)
    {
        $response = json_decode($request->getContent());
        // Log::info(json_encode($response));
        $mpesa_response = json_encode($response);

        // Accessing individual elements
        $transactionType = $response->TransactionType;
        $transID = $response->TransID;
        $transTime = $response->TransTime;
        $transAmount = $response->TransAmount;
        $businessShortCode = $response->BusinessShortCode;
        $billRefNumber = $response->BillRefNumber;
        $invoiceNumber = $response->InvoiceNumber;
        $orgAccountBalance = $response->OrgAccountBalance;
        $thirdPartyTransID = $response->ThirdPartyTransID;
        $msisdn = $response->MSISDN;
        $firstName = $response->FirstName;
        $middleName = $response->MiddleName;
        $lastName = $response->LastName;

        $c2bData = [
            'TransactionType' => $transactionType,
            'TransID' => $transID,
            'TransTime' => $transTime,
            'TransAmount' => $transAmount,
            'BusinessShortCode' => $businessShortCode,
            'BillRefNumber' => $billRefNumber,
            'InvoiceNumber' => $invoiceNumber,
            'OrgAccountBalance' => $orgAccountBalance,
            'ThirdPartyTransID' => $thirdPartyTransID,
            'MSISDN' => $msisdn,
            'FirstName' => $firstName,
            'MiddleName' => $middleName,
            'LastName' => $lastName,
            'ResponseData' => $mpesa_response
        ];
        $MerchantRequestID = null;
        $CheckoutRequestID = null;
        $pending_stkpush_request = CurrentStkPushRequest::first();
        if ($pending_stkpush_request) {
            $MerchantRequestID = $pending_stkpush_request->MerchantRequestID;
            $CheckoutRequestID = $pending_stkpush_request->CheckoutRequestID;
        }

        if ($MerchantRequestID === null && $CheckoutRequestID === null) {
            $c2bData['type'] = 'C2B';
            $c2bData['used'] = 0;
            MpesaC2BPayment::create($c2bData);
        } else {
            $mpesa_c2b_payment = MpesaC2BPayment::find($pending_stkpush_request->c2b_id);

            if ($mpesa_c2b_payment) {
                $c2bData['used'] = 1;
                $c2bData['type'] = 'STK PUSH';
                $mpesa_c2b_payment->update($c2bData);

                $pending_stkpush_request->delete();
            }
        }

        event(new MpesaC2BDataReceived($c2bData));
    }
}
