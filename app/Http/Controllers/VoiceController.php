<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VoiceController extends Controller
{
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|max:10240|mimes:webm,wav,mp3,mp4,m4a,ogg,flac',
        ]);

        $groqKey = config('services.groq.key');
        $openaiKey = config('services.openai.key');
        if (!$groqKey && !$openaiKey) {
            return response()->json(['error' => 'Transkripsi belum dikonfigurasi (GROQ_API_KEY atau OPENAI_API_KEY). Daftar gratis di console.groq.com/keys lalu isi .env GROQ_API_KEY=gsk_... lalu php artisan config:clear. Sementara gunakan mic keyboard.'], 503);
        }

        $file = $request->file('audio');
        $filename = $file->getClientOriginalName() ?: 'voice.webm';

        // ponytail: prioritaskan Groq (free) bila ada, fallback OpenAI
        $endpoint = $groqKey ? 'https://api.groq.com/openai/v1/audio/transcriptions' : 'https://api.openai.com/v1/audio/transcriptions';
        $model = $groqKey ? 'whisper-large-v3' : 'whisper-1';
        $key = $groqKey ?: $openaiKey;

        try {
            $res = Http::timeout(30)->withToken($key)
                ->attach('file', file_get_contents($file->getRealPath()), $filename)
                ->post($endpoint, [
                    'model' => $model,
                    'language' => 'id',
                ]);

            if (!$res->successful()) {
                return response()->json(['error' => 'Gagal transkripsi: '.$res->body()], $res->status());
            }

            return response()->json(['text' => trim((string) ($res->json('text') ?? ''))]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal transkripsi: '.$e->getMessage()], 500);
        }
    }
}
