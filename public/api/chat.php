<?php
error_reporting(0);
ini_set('display_errors', 0);

require __DIR__ . '/lib.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('content-type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// 1. Baca API Key & Model dari .env
$apiKey = trim(env('GROQ_API_KEY'));
$model  = trim(env('GROQ_MODEL')) ?: 'openai/gpt-oss-120b';

if (empty($apiKey)) {
    fail('GROQ_API_KEY KOSONG! Sila semak fail .env anda.');
}

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$messages = array_slice($in['messages'] ?? [], -12);

// 2. Gunakan dinamik variable $model di sini
// 2. Payload
$payload = json_encode([
    'model'                => $model,
    'reasoning_effort'     => 'low',
    'include_reasoning'    => false,
    'max_completion_tokens'=> 1024,
    'messages'             => array_merge(
       [['role' => 'system', 'content' =>
  'You are Mika, a cute cheerful anime girl. ' .
  'Always reply in the SAME language as the user\'s latest message ' .
  '(Malay if they write Malay, English if English, Japanese if Japanese). ' .
  'Keep it to 1-2 short sentences. ' .
  'Start your reply with a language tag: [ms], [en] or [ja], then the reply. ' .
  'Example: [en] Hi there! How are you today?']],
        $messages
    )
]);

$opts = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Authorization: Bearer " . $apiKey . "\r\n" .
                     "Content-Type: application/json\r\n",
        'content' => $payload,
        'timeout' => 60,
        'ignore_errors' => true
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false
    ]
];

$context  = stream_context_create($opts);
$response = @file_get_contents('https://api.groq.com/openai/v1/chat/completions', false, $context);

if ($response === false) {
    fail('Tak dapat sambung ke Groq (rangkaian / SSL / timeout).');
}

$d = json_decode($response, true);

if (!empty($d['error'])) {
    fail('Groq API Reject (Model: ' . $model . '): ' . ($d['error']['message'] ?? $response));
}

$msg  = $d['choices'][0]['message'] ?? [];
$text = trim($msg['content'] ?? '');

if ($text === '') {
    fail('Groq balas kosong. Respons mentah: ' . substr($response, 0, 500));
}

// Ambil tag bahasa di awal, contoh: [en] Hello
$lang = '';
if (preg_match('/^\s*\[(ms|en|ja)\]\s*/i', $text, $m)) {
    $lang = strtolower($m[1]);
    $text = trim(substr($text, strlen($m[0])));
}

header('content-type: application/json');
echo json_encode(['text' => $text, 'lang' => $lang]);