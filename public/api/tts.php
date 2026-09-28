<?php
require __DIR__.'/lib.php';

// Semakan cURL dimatikan untuk mengelakkan ralat palsu pada Laragon
// if(!function_exists('curl_init')) fail('Extension PHP curl belum aktif');

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$text = mb_substr((string)($in['text'] ?? ''), 0, 500);

if ($text === '') {
    fail('Teks kosong', 400);
}

if (provider() === 'elevenlabs') {
    if (env('ELEVEN_API_KEY') === '' || env('ELEVEN_VOICE_ID') === '') {
        fail('ELEVEN_API_KEY / ELEVEN_VOICE_ID belum diset');
    }

    [$c, $b] = http(
        'https://api.elevenlabs.io/v1/text-to-speech/'.env('ELEVEN_VOICE_ID').'?output_format=mp3_44100_128',
        [
            'xi-api-key: '.env('ELEVEN_API_KEY'),
            'content-type: application/json'
        ],
        json_encode([
            'text' => $text,
            'model_id' => 'eleven_multilingual_v2'
        ])
    );

    if ($c >= 400) {
        fail('ElevenLabs: '.$b);
    }

    header('content-type: audio/mpeg');
    echo $b;
} else {
    $vv = rtrim(env('VOICEVOX_URL', 'http://127.0.0.1:50021'), '/');
    $spk = env('VOICEVOX_SPEAKER', '8');

    [$c, $q] = http("$vv/audio_query?speaker=$spk&text=".rawurlencode($text), ['content-length: 0'], '');

    if ($c >= 400 || !$q) {
        fail('VOICEVOX tidak dijumpai. Pastikan aplikasi VOICEVOX sedang berjalan.');
    }

    [$c, $w] = http("$vv/synthesis?speaker=$spk", ['content-type: application/json'], $q);

    if ($c >= 400) {
        fail('VOICEVOX synthesis gagal');
    }

    header('content-type: audio/wav');
    echo $w;
}