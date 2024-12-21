<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use App\Models\Call;

class CallController extends Controller
{

    public function grantToken(Request $request) {
        $accountSid = config('services.twilio.sid');
        $apiKey = config('services.twilio.api_key');
        $apiSecret = config('services.twilio.api_secret');
        $twimlAppSid = config('services.twilio.twiml_app_sid');
        

        if (!$accountSid || !$apiKey || !$apiSecret || !$twimlAppSid) {
            return response()->json([
                'error' => 'Credenciais do Twilio não estão configuradas corretamente.'
            ], 500);
        }

        // Client Identity (unique user identity for the client)
        $identity = $request->input('identity', auth()->user()->id ?? 'default_user');

        try {
            // Creates an access token with 1 hour duration
            $token = new AccessToken(
                $accountSid,
                $apiKey,
                $apiSecret,
                3600, // Token expiration time in seconds
                $identity
            );

            // Configure Voice grant for the TwiML application
            $voiceGrant = new VoiceGrant();
            $voiceGrant->setOutgoingApplicationSid($twimlAppSid);
            $voiceGrant->setIncomingAllow(true);
            // Add VoiceGrant to token
            $token->addGrant($voiceGrant);

            // Return JWT token in JSON format
            return response()->json([
                'token' => $token->toJWT(),
                'identity' => $identity
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Falha ao gerar o token.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_user_id' => 'nullable|exists:users,id',
            'to_user_id' => 'required|exists:users,id',
            'callSid' => 'nullable|string',
            'type' => 'required|in:incoming,outgoing',
            'status' => 'required|in:completed,missed,rejected',
            'duration' => 'nullable|integer'
        ]);

        // If it's an outgoing call, from_user_id is the current user
        if ($validated['type'] === 'outgoing') {
            $validated['from_user_id'] = auth()->id();
        }

        Call::create($validated);

        return response()->json(['status' => 'success']);
    }
}
