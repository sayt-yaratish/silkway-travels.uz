<?php
// === SOZLAMALAR / SETTINGS ===
$BOT_TOKEN = 'PASTE_BOT_TOKEN_HERE';      // @BotFather bergan token
$CHAT_ID   = 'PASTE_GROUP_CHAT_ID_HERE';  // masalan: -1001234567890
// =============================

header('Content-Type: application/json; charset=utf-8');
$in = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (!empty($in['website'])) { echo json_encode(['ok' => true]); exit; } // spam tuzoq

$name    = mb_substr(trim($in['name'] ?? ''), 0, 60);
$phone   = mb_substr(trim($in['phone'] ?? ''), 0, 20);
$service = mb_substr(trim($in['service'] ?? ''), 0, 80);

if (mb_strlen($name) < 2 || strlen(preg_replace('/\D/', '', $phone)) < 7) {
    http_response_code(400); echo json_encode(['ok' => false]); exit;
}

$e = fn($s) => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
$text = "📩 <b>Yangi ariza</b>\n\n👤 Ism: {$e($name)}\n📞 Raqam: {$e($phone)}\n🛎 Xizmat: {$e($service)}";

$ch = curl_init("https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_POSTFIELDS => ['chat_id' => $CHAT_ID, 'text' => $text, 'parse_mode' => 'HTML'],
]);
$res = json_decode(curl_exec($ch), true);
curl_close($ch);

echo json_encode(['ok' => !empty($res['ok'])]);
