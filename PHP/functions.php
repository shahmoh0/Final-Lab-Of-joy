<?php

// Load DB and session
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

// Escape output to prevent XSS
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Redirect to a given URL
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// Redirect to login if user is not logged in
function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/LabOfJoy/fatimah/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

// Get total item count in the user's cart
function getCartCount(): int {
    if (!isLoggedIn()) return 0;
    $stmt = getDB()->prepare('SELECT COALESCE(SUM(quantity),0) FROM cart_items WHERE user_id = ?');
    $stmt->execute([getUserId()]);
    return (int) $stmt->fetchColumn();
}

// Add a product to cart or increase its quantity
function addToCart(int $userId, int $productId, int $qty = 1): void {
    $db = getDB();
    $stmt = $db->prepare('SELECT id, quantity FROM cart_items WHERE user_id=? AND product_id=?');
    $stmt->execute([$userId, $productId]);
    $row = $stmt->fetch();
    if ($row) {
        $db->prepare('UPDATE cart_items SET quantity=? WHERE id=?')
           ->execute([$row['quantity'] + $qty, $row['id']]);
    } else {
        $db->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?,?,?)')
           ->execute([$userId, $productId, $qty]);
    }
}

// Remove a specific item from the cart
function removeFromCart(int $cartItemId, int $userId): void {
    getDB()->prepare('DELETE FROM cart_items WHERE id=? AND user_id=?')
           ->execute([$cartItemId, $userId]);
}

// Get all cart items with product details
function getCartItems(int $userId): array {
    $stmt = getDB()->prepare(
        'SELECT ci.id, ci.quantity, p.name, p.price, p.image,
                (ci.quantity * p.price) AS subtotal
         FROM cart_items ci
         JOIN products p ON p.id = ci.product_id
         WHERE ci.user_id = ?'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Get the cart grand total for a user
function getCartTotal(int $userId): float {
    $stmt = getDB()->prepare(
        'SELECT COALESCE(SUM(ci.quantity * p.price),0)
         FROM cart_items ci JOIN products p ON p.id=ci.product_id
         WHERE ci.user_id=?'
    );
    $stmt->execute([$userId]);
    return (float) $stmt->fetchColumn();
}

// Fetch live weather from Open-Meteo API — free, no API key required
// API URL: https://api.open-meteo.com/v1/forecast
// Query parameters: latitude, longitude, current_weather=true, hourly fields
function getWeather(string $city): ?array {
    // Jubail coordinates (latitude & longitude)
    $lat = 27.0;
    $lon = 49.66;

    // Construct the API URL with query parameters
    $url = 'https://api.open-meteo.com/v1/forecast'
         . '?latitude='  . $lat
         . '&longitude=' . $lon
         . '&current_weather=true'
         . '&hourly=relativehumidity_2m,apparent_temperature,visibility,windspeed_10m,precipitation_probability'
         . '&timezone=Asia%2FRiyadh'
         . '&forecast_days=1';

    // Send HTTP GET request to the API
    $jasonWeather = @file_get_contents($url);
    if (!$jasonWeather) return null;

    // Decode JSON response into PHP array
    $data = json_decode($jasonWeather, true);
    return (isset($data['current_weather'])) ? $data : null;
}

// Return weather emoji based on WMO weather code from Open-Meteo
function getWeatherEmoji(int $code): string {
    if ($code === 0)                        return '☀️';
    if (in_array($code, [1,2]))             return '⛅';
    if ($code === 3)                        return '☁️';
    if (in_array($code, [45,48]))           return '🌫️';
    if (in_array($code, [51,53,55]))        return '🌦️';
    if (in_array($code, [61,63,65]))        return '🌧️';
    if (in_array($code, [71,73,75,77]))     return '❄️';
    if (in_array($code, [80,81,82]))        return '🌧️';
    if (in_array($code, [85,86]))           return '🌨️';
    if (in_array($code, [95,96,99]))        return '⛈️';
    return '🌤️';
}

// Return weather description based on WMO code
function getWeatherDesc(int $code): string {
    $map = [
        0  => 'Clear Sky',
        1  => 'Mainly Clear', 2 => 'Partly Cloudy', 3 => 'Overcast',
        45 => 'Foggy',        48 => 'Icy Fog',
        51 => 'Light Drizzle',53 => 'Drizzle',      55 => 'Heavy Drizzle',
        61 => 'Light Rain',   63 => 'Rain',          65 => 'Heavy Rain',
        71 => 'Light Snow',   73 => 'Snow',          75 => 'Heavy Snow',
        80 => 'Rain Showers', 81 => 'Rain Showers',  82 => 'Heavy Showers',
        95 => 'Thunderstorm', 96 => 'Thunderstorm',  99 => 'Thunderstorm',
    ];
    return $map[$code] ?? 'Clear Sky';
}
