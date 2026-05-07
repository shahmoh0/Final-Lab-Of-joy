<?php
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Protect page — login required
if (!isLoggedIn()) {
    header('Location: /LabOfJoy/fatimah/login.php');
    exit;
}

$cityName = "Jubail";
$lat      = 27.0;
$lon      = 49.66;

// 1. API Key  : not required (Open-Meteo is free)
// 2. Query    : latitude, longitude, current_weather, hourly fields
// 3. URL      : https://api.open-meteo.com/v1/forecast
// 4. Format   : JSON

// Construct the URL with query parameters
$apiUrl = "https://api.open-meteo.com/v1/forecast"
        . "?latitude="  . $lat
        . "&longitude=" . $lon
        . "&current_weather=true"
        . "&hourly=relativehumidity_2m,apparent_temperature,visibility,precipitation_probability,windspeed_10m"
        . "&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode"
        . "&timezone=Asia/Riyadh"
        . "&forecast_days=5";

// Send HTTP GET request to the API
$jasonWeather = file_get_contents($apiUrl);

// Turn JSON response into PHP variable
$weather = json_decode($jasonWeather, true);

$current     = $weather['current_weather']  ?? null;
$currentHour = (int) date('H');
$humidity    = $weather['hourly']['relativehumidity_2m'][$currentHour]      ?? '—';
$feelsLike   = $weather['hourly']['apparent_temperature'][$currentHour]      ?? '—';
$visibility  = $weather['hourly']['visibility'][$currentHour]                ?? '—';
$rainChance  = $weather['hourly']['precipitation_probability'][$currentHour] ?? '—';
$daily       = $weather['daily'] ?? [];

$dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather — Lab of Joy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink-1: #ffe6f1;
            --pink-2: #ffc7df;
            --pink-3: #ff8fc4;
            --purple: #b9a2ff;
            --ink:    #1e4d3f;
            --shadow: rgba(53,22,72,0.15);
            --glass:  rgba(255,255,255,0.55);
            --border: rgba(255,255,255,0.70);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Poppins", Arial, sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background:
                radial-gradient(circle at 12% 10%, var(--pink-1), transparent 55%),
                radial-gradient(circle at 88% 18%, rgba(185,162,255,0.45), transparent 55%),
                radial-gradient(circle at 30% 95%, rgba(191,242,216,0.55), transparent 52%),
                linear-gradient(135deg, var(--pink-1), var(--pink-2));
            background-attachment: fixed;
        }

        /* ── Top bar ── */
        .topBar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px;
            background: rgba(255,255,255,0.35);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
            flex-wrap: wrap;
            gap: 10px;
        }

        .topBar .brand { font-size: 1.2rem; font-weight: 700; }

        .backBtn {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 999px;
            text-decoration: none;
            background: linear-gradient(135deg, var(--pink-3), var(--purple));
            color: #fff;
            font-weight: 600;
            font-size: 0.88rem;
            box-shadow: 0 4px 12px rgba(255,143,196,0.35);
            transition: 0.2s;
        }

        .backBtn:hover { opacity: 0.88; transform: translateY(-1px); }

        /* ── Page wrapper ── */
        .page {
            width: min(900px, 94%);
            margin: 36px auto 60px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pageTitle {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            opacity: .8;
        }

        /* ── Main weather card ── */
        .mainCard {
            background: linear-gradient(135deg, rgba(255,143,196,0.30), rgba(185,162,255,0.35));
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 36px 32px;
            box-shadow: 0 16px 48px var(--shadow);
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
        }

        .mainLeft { display: flex; align-items: center; gap: 20px; }

        .bigEmoji {
            font-size: 6rem;
            line-height: 1;
            filter: drop-shadow(0 6px 14px rgba(53,22,72,0.20));
        }

        .mainInfo { display: flex; flex-direction: column; gap: 4px; }

        .mainCity {
            font-size: 0.9rem;
            font-weight: 600;
            opacity: .7;
        }

        .mainTemp {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
        }

        .mainDesc {
            font-size: 1.1rem;
            font-weight: 500;
            opacity: .75;
            text-transform: capitalize;
        }

        .mainTime {
            font-size: 0.8rem;
            opacity: .55;
            margin-top: 4px;
        }

        /* ── Stat grid ── */
        .statsGrid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .statCard {
            background: rgba(255,255,255,0.60);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .statEmoji { font-size: 1.8rem; }

        .statBody { display: flex; flex-direction: column; gap: 1px; }

        .statVal   { font-size: 1.15rem; font-weight: 800; }
        .statLabel { font-size: 0.75rem; opacity: .60; font-weight: 600; }

        /* ── 5-day forecast ── */
        .forecastTitle {
            font-size: 1rem;
            font-weight: 700;
            opacity: .7;
            margin-bottom: 4px;
        }

        .forecastRow {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }

        .forecastDay {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 16px 10px;
            text-align: center;
            box-shadow: 0 6px 18px var(--shadow);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .forecastDay .fDay   { font-size: 0.8rem; font-weight: 700; opacity: .65; }
        .forecastDay .fEmoji { font-size: 1.8rem; }
        .forecastDay .fMax   { font-size: 1rem; font-weight: 800; }
        .forecastDay .fMin   { font-size: 0.82rem; opacity: .60; font-weight: 600; }
        .forecastDay .fRain  { font-size: 0.75rem; opacity: .55; }

        /* ── Error ── */
        .errorCard {
            background: var(--glass);
            border-radius: 28px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 16px 48px var(--shadow);
        }

        .errorCard h2 { color: #c0392b; margin-bottom: 10px; }

        @media (max-width: 640px) {
            .mainCard    { flex-direction: column; text-align: center; }
            .mainLeft    { flex-direction: column; }
            .statsGrid   { grid-template-columns: 1fr 1fr; }
            .forecastRow { grid-template-columns: repeat(3, 1fr); }
            .bigEmoji    { font-size: 4rem; }
            .mainTemp    { font-size: 3.5rem; }
        }
    </style>
</head>
<body>

<!-- Top navigation bar -->
<div class="topBar">
    <span class="brand">🎁 Lab of Joy — Weather</span>
    <a href="/LabOfJoy/aljury/categories.php" class="backBtn">← Back to Shop</a>
</div>

<div class="page">

    <p class="pageTitle">🌤️ Live Weather — <?= htmlspecialchars($cityName, ENT_QUOTES, 'UTF-8') ?>, Saudi Arabia</p>

    <?php if ($current): ?>

    <?php
    $code  = (int) $current['weathercode'];
    $emoji = getWeatherEmoji($code);
    $desc  = getWeatherDesc($code);
    $temp  = round($current['temperature']);
    $wind  = $current['windspeed'];
    ?>

    <!-- Main current weather card -->
    <div class="mainCard">
        <div class="mainLeft">
            <div class="bigEmoji"><?= $emoji ?></div>
            <div class="mainInfo">
                <span class="mainCity">📍 <?= htmlspecialchars($cityName, ENT_QUOTES, 'UTF-8') ?>, Saudi Arabia</span>
                <span class="mainTemp"><?= $temp ?>°C</span>
                <span class="mainDesc"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></span>
                <span class="mainTime">Updated: <?= date('D, d M Y — H:i') ?></span>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="statsGrid">
            <div class="statCard">
                <span class="statEmoji">🌡️</span>
                <div class="statBody">
                    <span class="statVal"><?= is_numeric($feelsLike) ? round($feelsLike) . '°C' : $feelsLike ?></span>
                    <span class="statLabel">Feels Like</span>
                </div>
            </div>
            <div class="statCard">
                <span class="statEmoji">💧</span>
                <div class="statBody">
                    <span class="statVal"><?= $humidity ?>%</span>
                    <span class="statLabel">Humidity</span>
                </div>
            </div>
            <div class="statCard">
                <span class="statEmoji">🌬️</span>
                <div class="statBody">
                    <span class="statVal"><?= $wind ?> km/h</span>
                    <span class="statLabel">Wind Speed</span>
                </div>
            </div>
            <div class="statCard">
                <span class="statEmoji">🌂</span>
                <div class="statBody">
                    <span class="statVal"><?= $rainChance ?>%</span>
                    <span class="statLabel">Rain Chance</span>
                </div>
            </div>
            <div class="statCard">
                <span class="statEmoji">👁️</span>
                <div class="statBody">
                    <span class="statVal"><?= is_numeric($visibility) ? round($visibility / 1000, 1) . ' km' : $visibility ?></span>
                    <span class="statLabel">Visibility</span>
                </div>
            </div>
            <div class="statCard">
                <span class="statEmoji">🧭</span>
                <div class="statBody">
                    <span class="statVal"><?= $current['winddirection'] ?>°</span>
                    <span class="statLabel">Wind Direction</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 5-day forecast -->
    <?php if (!empty($daily['time'])): ?>
    <p class="forecastTitle">📅 5-Day Forecast</p>
    <div class="forecastRow">
        <?php for ($i = 0; $i < 5 && $i < count($daily['time']); $i++):
            $dayDate  = new DateTime($daily['time'][$i]);
            $dayName  = $i === 0 ? 'Today' : $dayNames[(int)$dayDate->format('w')];
            $dCode    = (int) $daily['weathercode'][$i];
            $dEmoji   = getWeatherEmoji($dCode);
            $dMax     = round($daily['temperature_2m_max'][$i]);
            $dMin     = round($daily['temperature_2m_min'][$i]);
            $dRain    = $daily['precipitation_sum'][$i] ?? 0;
        ?>
        <div class="forecastDay">
            <span class="fDay"><?= $dayName ?></span>
            <span class="fEmoji"><?= $dEmoji ?></span>
            <span class="fMax"><?= $dMax ?>°C</span>
            <span class="fMin"><?= $dMin ?>°C</span>
            <span class="fRain">🌧 <?= $dRain ?> mm</span>
        </div>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>

    <div class="errorCard">
        <h2>⚠️ Weather Unavailable</h2>
        <p>Could not fetch weather data. Please check your internet connection.</p>
    </div>

    <?php endif; ?>

</div>

</body>
</html>
