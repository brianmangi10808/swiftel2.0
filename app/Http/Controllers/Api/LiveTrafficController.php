<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PppoeTraffic;
use Illuminate\Support\Facades\Http;

class LiveTrafficController extends Controller
{
    /**
     * LIVE TRAFFIC API
     * Called every second by your chart
     * Saves one row into DB each call
     */
    public function live($pppoe)
    {
        // IMPORTANT:
        // Replace this section with your REAL MikroTik code
        // ----------------------------------------------------
        $url = "https://swiftel.co.ke/api/mikrotik-live/{$pppoe}";
        $response = Http::get($url);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed fetching traffic'], 500);
        }

        $data = $response->json();   // must return tx_bps and rx_bps
        // ----------------------------------------------------


        // Convert to Mbps
        $upload = round($data['tx_bps'] / 1_000_000, 3);
        $download = round($data['rx_bps'] / 1_000_000, 3);

        // SAVE IN DATABASE
        PppoeTraffic::create([
            'interface' => $pppoe,
            'upload_mbps' => $upload,
            'download_mbps' => $download,
            'logged_at' => now(),
        ]);

        // Return live data to frontend
        return response()->json([
            'tx_bps' => $data['tx_bps'],
            'rx_bps' => $data['rx_bps'],
        ]);
    }


    /**
     * DAILY TRAFFIC HISTORY
     */
    public function daily($pppoe)
    {
        $rows = PppoeTraffic::where('interface', $pppoe)
            ->whereDate('logged_at', now()->toDateString())
            ->orderBy('logged_at')
            ->get();

        return response()->json($rows);
    }


    /**
     * MONTHLY TRAFFIC HISTORY
     */
    public function monthly($pppoe)
    {
        $rows = PppoeTraffic::where('interface', $pppoe)
            ->whereMonth('logged_at', now()->month)
            ->whereYear('logged_at', now()->year)
            ->orderBy('logged_at')
            ->get();

        return response()->json($rows);
    }
}
