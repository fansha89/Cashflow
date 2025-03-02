<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    /**
     * Get the first setting record.
     */
    public function index(): JsonResponse
    {
        if (!Setting::exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No settings found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }

        $setting = Setting::select('id', 'site_name', 'site_url', 'contact_email')->first();

        return response()->json([
            'success' => true,
            'message' => 'Successfully retrieved profile data',
            'data' => $setting
        ], Response::HTTP_OK);
    }
}
