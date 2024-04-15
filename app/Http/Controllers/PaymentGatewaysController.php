<?php

namespace App\Http\Controllers;

use App\GatewaySetting;
use Illuminate\Http\Request;

class PaymentGatewaysController extends Controller
{
    //
    public function store(Request $request)
    {

        try {
            $user = auth()->user();
            $business_id = $user->business_id;
            if ($request->provider && $request->provider == 'mpesa') {
                $check = GatewaySetting::where('provider', 'mpesa')->where('business_id', $business_id)->first();
                if ($check) {
                    $request->validate([

                        'mpesa_shortcode' => 'required',
                        'mpesa_passkey' => 'required',
                        'mpesa_consumerkey' => 'required',
                        'mpesa_consumersecret' => 'required',
                        'mpesa_shortcode_type' => 'required',
                        //till_number required if shortcode type is till
                        'till_number' => 'required_if:mpesa_shortcode_type,till',
                    ]);
                    $check->update($request->all());
                    return response()->json(['status' => 'success', 'message' => 'Payment gateway updated successfully']);
                } else {

                    $data = $request->all();
                    $data['business_id'] = $business_id;
                    GatewaySetting::create($data);
                    return response()->json(['status' => 'success', 'message' => 'Payment gateway created successfully']);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => 'error:' . $th->getMessage(), 'message' => 'Something went wrong!']);
        }
    }
    public function index()
    {
        $user = auth()->user();
        $data = GatewaySetting::where('provider', 'mpesa')->where('business_id', $user->business_id)->first();

        return response()->json(['status' => 'success', 'data' => $data, 'user' => $user, 'message' => 'Payment gateway created successfully']);
    }
}
