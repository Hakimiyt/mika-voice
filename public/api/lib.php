<?php
function env($k, $d = '') {
    static $E = null;
    if ($E === null) {
        $E = [];
        $f = __DIR__ . '/../../.env';
        if (is_file($f)) {
            foreach (file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
                $l = trim($l);
                if ($l === '' || $l[0] === '#' || strpos($l, '=') === false) continue;
                [$a, $b] = explode('=', $l, 2);
                $E[trim($a)] = trim(preg_replace('/^["\']|["\']$/', '', $b));
            }
        }
    }
    return ($E[$k] ?? '') !== '' ? $E[$k] : $d;
}

function fail($m, $c = 500) {
    http_response_code($c);
    header('content-type: application/json');
    echo json_encode(['error' => $m]);
    exit;
}

function http($url, $headers, $body = null) {
    // Format header menjadi string berbilang baris
    $headerString = implode("\r\n", $headers);

    $opts = [
        'http' => [
            'method'        => 'POST',
            'header'        => $headerString,
            'content'       => $body,
            'timeout'       => 30,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ]
    ];

    $context = stream_context_create($opts);
    $r = @file_get_contents($url, false, $context);

    $code = 500;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $h) {
            if (preg_match('{HTTP\/\S*\s(\d{3})}', $h, $m)) {
                $code = (int)$m[1];
                break;
            }
        }
    }

    if ($r === false) {
        fail('Gagal membuat sambungan ke Groq API server.');
    }

    return [$code, $r];
}

function provider() {
    return env('TTS_PROVIDER', 'voicevox');
}

function reply_lang() {
    return env('REPLY_LANG', 'Malay');
}