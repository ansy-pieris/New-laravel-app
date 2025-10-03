<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;

class GoogleOAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope('https://www.googleapis.com/auth/gmail.send');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope('https://www.googleapis.com/auth/gmail.send');
        $client->setAccessType('offline');

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            
            if (isset($token['error'])) {
                return response()->json([
                    'error' => true,
                    'message' => 'Error getting access token: ' . $token['error']
                ]);
            }

            if (isset($token['refresh_token'])) {
                return response()->json([
                    'success' => true,
                    'refresh_token' => $token['refresh_token'],
                    'message' => 'OAuth setup successful! Copy the refresh_token to your .env file as GOOGLE_REFRESH_TOKEN'
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'No refresh token received. Make sure to revoke previous access and try again.'
                ]);
            }
        }

        return response()->json([
            'error' => true,
            'message' => 'No authorization code received'
        ]);
    }
}