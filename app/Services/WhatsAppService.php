<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim Pesan WA Otomatis ke Nomor HP Orang Tua
     */
    public static function sendMessage(string $target, string $message): bool
    {
        $token = env('FONNTE_TOKEN', 'tJKxoemknCeo6gQNyp9U');
        $url   = env('FONNTE_URL', 'https://api.fonnte.com/send');

        // Normalisasi nomor HP ke format 628xxx
        $target = preg_replace('/[^0-9]/', '', $target);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($url, [
                'target'  => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WA Absensi SMA XYZ berhasil terkirim ke {$target}");
                return true;
            }

            Log::error("Gagal kirim WA ke {$target}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error WA Gateway: " . $e->getMessage());
            return false;
        }
    }
}