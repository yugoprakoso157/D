<?php
$BOT_TOKEN  = '8484589113:AAFcTLM7_0N6jgPfNTqZkO-zJYqOTQ3naj0';
$CHANNEL_ID = '@winssave12'; // username channel kamu

$ip = $_SERVER['REMOTE_ADDR'];
$details = @json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
$city = $details->city ?? 'Unknown';
$country = $details->country ?? 'Unknown';

$message = "📍 <b>New Visitor Detected</b>\n"
          . "🧠 IP: <code>$ip</code>\n"
          . "🏙️ City: <b>$city</b>\n"
          . "🌍 Country: <b>$country</b>\n"
          . "🕒 Time: " . date("Y-m-d H:i:s");

$apiURL = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiURL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'chat_id' => $CHANNEL_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " | IP: $ip | City: $city | Response: $response | Error: $error\n", FILE_APPEND);

header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'ip' => $ip,
    'city' => $city,
    'response' => json_decode($response, true),
    'error' => $error
]);
?>
