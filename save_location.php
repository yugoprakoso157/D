<?php
$botToken = "8484589113:AAFcTLM7_0N6jgPfNTqZkO-zJYqOTQ3naj0"; // Token bot kamu
$chatID = "@winssave12"; // Username channel kamu

$ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("https://ipinfo.io/{$ip}/json"));
$city = $details->city ?? 'Unknown';

$text = "Status: ok\nIP: $ip\nCity: $city";

$url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatID&text=" . urlencode($text);

$response = @file_get_contents($url);

if ($response) {
    echo json_encode(["status" => "ok", "ip" => $ip, "city" => $city, "response" => "sent"]);
} else {
    echo json_encode(["status" => "ok", "ip" => $ip, "city" => $city, "error" => "Could not send to Telegram"]);
}
?>
