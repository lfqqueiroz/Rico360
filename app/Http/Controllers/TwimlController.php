<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use App\Models\User;
use App\Models\Call;

class TwimlController extends Controller
{
    // Generate TwiML response for incoming calls
    // Creates a new call record and connects clients
    public function generateTwimlResponse(Request $request)
    {
        // Get recipient's client ID
        $to = $request->input('To', '');
        $response = new VoiceResponse();
        $dial = $response->dial('');
        $dial->client($to);

        // Extract user IDs from the request
        $fromId = str_replace('client:', '', $request->input('From'));
        $toId = str_replace('client:', '', $request->input('To'));

        // Find users involved in the call
        $fromUser = User::where('id', $fromId)->first();
        $toUser = User::where('id', $toId)->first();

        // Create new call record in database
        $call = new \App\Models\Call();
        $call->callSid = $request->input('CallSid');
        $call->duration = $request->input('Duration', 0);
        $call->status = $request->input('CallStatus');
        $call->from_user_id = $fromUser ? $fromUser->id : null;
        $call->to_user_id = $toUser ? $toUser->id : null;

        $call->save();
        
        return response($response, 200)
            ->header('Content-Type', 'application/xml');
    }

    // Update call record with final call data
    // Called when call ends or status changes
    public function saveCallData(Request $request)
    {
        try {
            // Extract user IDs from the request
            $fromId = str_replace('client:', '', $request->input('From'));
            $toId = str_replace('client:', '', $request->input('To'));

            // Find users involved in the call
            $fromUser = User::where('id', $fromId)->first();
            $toUser = User::where('id', $toId)->first();

            // Update existing call record with final data
            Call::where('callSid', $request->input('CallSid'))->update([
                'callSid' => $request->input('CallSid'),
                'duration' => $request->input('CallDuration', 0),
                'status' => $request->input('CallStatus'),
                'from_user_id' => $fromUser ? $fromUser->id : null,
            ]);

            return response()->json(['message' => 'Call data saved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save call data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
