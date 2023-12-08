<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public $secret_key = 'x409z636R3vFRPttwT26jkdwbdewidJN1bncwi2gpT';
    public function getAllUsers(Request $request)
    {
        $api_key = trim($request->api_key);

        if (empty($api_key)) {
            $response = [
                'status' => 'not found',
                'response' => 'Api Key not found',
            ];

            return response()->json($response);
        }

        if ($api_key != $this->secret_key) {
            $response = [
                'status' => 'invalid',
                'response' => 'Api Key is invalid',
            ];

            return response()->json($response);
        }

        $users = User::all();

        $response = [
            'status' => 'ok',
            'response' => 'ok',
            'data' => $users,
        ];

        return response()->json($response);
    }

    public function getSalesforceData(Request $request)
    {
        $api_key = trim($request->api_key);

        if (empty($api_key)) {
            $response = [
                'status' => 'not found',
                'response' => 'Api Key not found',
            ];

            return response()->json($response);
        }

        if ($api_key != $this->secret_key) {
            $response = [
                'status' => 'invalid',
                'response' => 'Api Key is invalid',
            ];

            return response()->json($response);
        }
        $accessToken = $this->getAccessToken();

        $salesforceData = $this->getSalesforceDataUsingToken($accessToken);

        return response()->json($salesforceData);
    }

    private function getAccessToken()
    {
        $response = Http::asForm()->post(config('salesforce.login_url') . '/services/oauth2/token', [
            'grant_type' => 'password',
            'client_id' => config('salesforce.client_id'),
            'client_secret' => config('salesforce.client_secret'),
            'username' => config('salesforce.username'),
            'password' => config('salesforce.password'),
        ]);

        $responseData = $response->json();

        return $responseData['access_token'];
    }

    private function getSalesforceDataUsingToken($accessToken)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://zoominsurancebrokers--dw.sandbox.my.salesforce.com/services/apexrest/getUpdates', [
            'ids' => ['001D600001jFw3IIAS'],
            'reqtype' => 'CLIENT_POLICY_SCHEMA',
        ]);

        return $response->json();
    }
}
