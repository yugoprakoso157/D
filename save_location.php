<?php
// Token dan chat ID Telegram kamu
$botToken = "8484589113:AAFcTLM7_0N6jgPfNTqZkO-zJYqOTQ3naj0";
$chatID = "@winssave12"; // ganti sesuai username channel kamu

// Ambil IP asli pengunjung
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// Ambil detail lokasi dari ipinfo.io
$response = @file_get_contents("https://ipinfo.io/{$ip}/json");

if ($response !== false) {
    $details = json_decode($response, true);
    $city = $details['city'] ?? 'Tidak Diketahui';
    $country = $details['country'] ?? 'Unknown';
} else {
    // Fallback ke API lain jika ipinfo.io gagal
    $alt = @file_get_contents("http://ip-api.com/json/{$ip}");
    if ($alt !== false) {
        $alt_details = json_decode($alt, true);
        $city = $alt_details['city'] ?? 'Tidak Diketahui';
        $country = $alt_details['country'] ?? 'Unknown';
    } else {
        $city = 'Tidak Diketahui';
        $country = 'Unknown';
    }
}

// Format teks pesan ke Telegram
$text = "📍 <b>Status:</b> OK\n"
      . "<b>IP:</b> $ip\n"
      . "<b>City:</b> $city\n"
      . "<b>Country:</b> $country";

// Kirim ke Telegram
$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatID&text=" . urlencode($text) . "&parse_mode=HTML";
$response = @file_get_contents($url);

// Balikkan hasil JSON untuk testing manual
if ($response) {
    echo json_encode(["status" => "ok", "ip" => $ip, "city" => $city, "country" => $country, "sent" => true]);
} else {
    echo json_encode(["status" => "ok", "ip" => $ip, "city" => $city, "country" => $country, "sent" => false]);
}
?>
