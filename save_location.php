<?php
// ================== CONFIG ==================
$botToken   = getenv('8484589113:AAFcTLM7_0N6jgPfNTqZkO-zJYqOTQ3naj0') ?: 'ISI_TOKEN_BOT_TELEGRAM_KAMU';
$chatID     = getenv('@winssave12') ?: '@winssave12';
$ipinfoToken = getenv('8a1c5306f41989') ?: 'ISI_TOKEN_IPINFO_KAMU'; // opsional
$logFile    = __DIR__ . '/data_pengunjung_' . date('Y-m-d') . '.txt';

// ================== CORS ==================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ================== AMBIL IP ==================
$ip = null;
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// fallback: jika IP masih kosong atau local (::1)
if (empty($ip) || $ip === '::1' || strpos($ip, '127.') === 0) {
    $ip_try = @file_get_contents('https://api.ipify.org');
    if ($ip_try && filter_var($ip_try, FILTER_VALIDATE_IP)) {
        $ip = trim($ip_try);
    }
}
if (empty($ip)) $ip = 'Unknown';

// ================== AMBIL LOKASI ==================
$city = 'Tidak Diketahui';
$country = 'Unknown';

$url = "https://ipinfo.io/{$ip}/json";
if (!empty($ipinfoToken)) $url .= "?token={$ipinfoToken}";
$response = @file_get_contents($url);
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['city'])) $city = $data['city'];
    if (isset($data['country'])) $country = $data['country'];
}

// fallback ke ip-api.com jika ipinfo gagal
if ($city === 'Tidak Diketahui' || $country === 'Unknown') {
    $alt = @file_get_contents("http://ip-api.com/json/{$ip}");
    if ($alt !== false) {
        $a = json_decode($alt, true);
        $city = $a['city'] ?? $city;
        $country = $a['countryCode'] ?? $country;
    }
}

// ================== BENTUK PESAN ==================
$time = date('Y-m-d H:i:s');
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referer = $_SERVER['HTTP_REFERER'] ?? ($_POST['ref'] ?? $_GET['ref'] ?? '');

$message = "📢 *Visitor Baru*\n"
         . "🕒 {$time}\n"
         . "🌐 IP: `{$ip}`\n"
         . "🏙️ Lokasi: *{$city}* ({$country})\n"
         . "📱 Device: " . str_replace(['`','*','_'], '', $userAgent) . "\n"
         . (!empty($referer) ? "🔗 Referer: {$referer}\n" : "");

// ================== KIRIM KE TELEGRAM ==================
$tele_api = "https://api.telegram.org/bot{$botToken}/sendMessage";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tele_api);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'chat_id' => $chatID,
    'text' => $message,
    'parse_mode' => 'Markdown'
]);
$resp = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

// ================== SIMPAN KE LOG ==================
$log_line = "{$time} | {$ip} | {$city} | {$country} | {$userAgent} | {$referer}\n";
@file_put_contents($logFile, $log_line, FILE_APPEND | LOCK_EX);

// ================== OUTPUT UNTUK BROWSER ==================
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'ip' => $ip,
    'city' => $city,
    'country' => $country,
    'telegram_sent' => $resp ? true : false,
    'curl_error' => $curl_err ?: null
]);
?>
