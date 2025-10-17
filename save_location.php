<?php
// ================== KONFIGURASI ==================
$botToken   = '8484589113:AAFcTLM7_0N6jgPfNTqZkO-zJYqOTQ3naj0'; // TOKEN TERBARU dari @agenn8nnew_bot
$chatID     = '@winssave12'; // Username channel Telegram kamu (pastikan publik)
$ipinfoToken = '8a1c5306f41989'; // Token ipinfo.io kamu
$logFile    = __DIR__ . '/data_pengunjung_' . date('Y-m-d') . '.txt';

// ================== CORS HEADER ==================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ================== AMBIL IP PENGUNJUNG ==================
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$ip = explode(',', $ip)[0];
if ($ip === '::1' || strpos($ip, '127.') === 0) {
    $ip = trim(@file_get_contents('https://api.ipify.org'));
}

// ================== AMBIL LOKASI DARI IPINFO ==================
$city = 'Tidak diketahui';
$country = 'Unknown';
$url = "https://ipinfo.io/{$ip}/json?token={$ipinfoToken}";
$response = @file_get_contents($url);
if ($response !== false) {
    $data = json_decode($response, true);
    $city = $data['city'] ?? $city;
    $country = $data['country'] ?? $country;
}

// Fallback ke ip-api.com bila ipinfo gagal
if ($city === 'Tidak diketahui' || $country === 'Unknown') {
    $alt = @file_get_contents("http://ip-api.com/json/{$ip}");
    if ($alt !== false) {
        $a = json_decode($alt, true);
        $city = $a['city'] ?? $city;
        $country = $a['countryCode'] ?? $country;
    }
}

// ================== DATA TAMBAHAN ==================
$time = date('Y-m-d H:i:s');
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? ($_POST['ref'] ?? $_GET['ref'] ?? '');

// ================== PESAN UNTUK TELEGRAM ==================
$message = "📢 *Visitor Baru Detected!*\n"
         . "🕒 Waktu: {$time}\n"
         . "🌐 IP: `{$ip}`\n"
         . "🏙️ Lokasi: *{$city}, {$country}*\n"
         . "📱 Device: " . str_replace(['`','*','_'], '', $userAgent) . "\n"
         . (!empty($referer) ? "🔗 Sumber: {$referer}\n" : "");

// ================== KIRIM KE TELEGRAM ==================
$apiURL = "https://api.telegram.org/bot{$botToken}/sendMessage";
$payload = [
    'chat_id' => $chatID,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init($apiURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// ================== SIMPAN LOG ==================
$logData = "{$time} | {$ip} | {$city} | {$country} | {$userAgent} | {$referer}\n";
@file_put_contents($logFile, $logData, FILE_APPEND);

// ================== OUTPUT UNTUK BROWSER ==================
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'ip' => $ip,
    'city' => $city,
    'country' => $country,
    'telegram_response' => json_decode($response, true),
    'curl_error' => $error ?: null
]);
?>
