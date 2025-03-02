<?php

namespace App\Http\Controllers;

use App\Notifications\SendEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Notification;
use Exception;

class Controller extends BaseController
{
    /**
     * Send email notification.
     */
    public function index(): JsonResponse
    {
        try {
            Notification::route('mail', env('ADMIN_EMAIL', 'fansha1@gmail.com'))
                ->notify(new SendEmail());

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil terkirim'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
