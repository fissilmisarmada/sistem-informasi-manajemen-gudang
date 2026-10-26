<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:1024',
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
        ]);

        $request->user()->updatePushSubscription(
            $data['endpoint'],
            $data['keys']['p256dh'],
            $data['keys']['auth'],
        );

        return response()->noContent();
    }

    public function unsubscribe(Request $request)
    {
        $data = $request->validate([
            'endpoint' => 'required|string|max:1024',
        ]);

        $request->user()->deletePushSubscription($data['endpoint']);

        return response()->noContent();
    }
}
