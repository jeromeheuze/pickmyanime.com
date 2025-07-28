<?php
require_once './includes/dbconnect.php';

require_once 'dbconnect.php';

$mood = $_GET['mood'] ?? '';
$mood = strtolower(trim($mood));

$stmt = $DBcon->prepare("SELECT a.*
FROM anime_recommendations a
JOIN anime_moods am ON a.id = am.anime_id
JOIN moods m ON m.id = am.mood_id
WHERE m.name = ?
ORDER BY RAND()
LIMIT 1;");
$stmt->bind_param("s", $mood);
$stmt->execute();
$result = $stmt->get_result();

if ($entry = $result->fetch_assoc()) {
    $entry['streaming'] = json_decode($entry['streaming'], true);
} else {
    http_response_code(404);
    echo "No recommendations found for this mood.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($entry['title']) ?> – PickMyAnime</title>
    <meta name="description" content="<?= htmlspecialchars($entry['synopsis']) ?>" />
    <link rel="canonical" href="https://pickmyanime.com/anime-recommendation/<?= urlencode($mood) ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($entry['title']) ?> – PickMyAnime" />
    <meta property="og:description" content="<?= htmlspecialchars($entry['synopsis']) ?>" />
    <meta property="og:image" content="https://pickmyanime.com<?= htmlspecialchars($entry['poster']) ?>" />
    <meta property="og:url" content="https://pickmyanime.com/anime-recommendation/<?= urlencode($mood) ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <link rel="icon" href="/favicon-32x32.png" sizes="32x32" />

    <!-- Shoelace UI -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.1/cdn/shoelace.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.1/cdn/themes/dark.css" />

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #1a1a1a, #000000);
            color: #fff;
            text-align: center;
            padding: 4rem 1rem;
        }
        .container {
            max-width: 640px;
            margin: auto;
        }
        .anime-image {
            width: 100%;
            max-width: 320px;
            border-radius: 16px;
            margin-bottom: 1rem;
        }
        .share-box {
            margin-top: 2rem;
        }
        sl-input::part(base) {
            background: #1a1a1a;
            color: #eee;
        }
        sl-input.glow-pulse::part(base) {
            animation: glow-pulse 0.8s ease-in-out;
            box-shadow: 0 0 8px rgba(0, 255, 153, 0.8);
            border-radius: 6px;
        }
        @keyframes glow-pulse {
            0%   { box-shadow: 0 0 0px rgba(0, 255, 153, 0.4); }
            50%  { box-shadow: 0 0 8px rgba(0, 255, 153, 0.9); }
            100% { box-shadow: 0 0 0px rgba(0, 255, 153, 0.4); }
        }
    </style>
</head>
<body class="sl-theme-dark">
<div class="container">
    <h1>You’re feeling <strong><?= ucfirst(htmlspecialchars($mood)) ?></strong>?</h1>

    <sl-card class="anime-card" style="--padding: 1rem;">
        <img src="<?= htmlspecialchars($entry['poster']) ?>" alt="<?= htmlspecialchars($entry['title']) ?>" class="anime-image" loading="lazy" />

        <h2><?= htmlspecialchars($entry['title']) ?></h2>
        <p><?= htmlspecialchars($entry['synopsis']) ?></p>

        <h3>Watch on:</h3>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($entry['streaming'] as $platform => $url): ?>
                <li><a href="<?= htmlspecialchars($url) ?>" target="_blank" style="color: #00ffff;"><?= htmlspecialchars($platform) ?></a></li>
            <?php endforeach; ?>
        </ul>

        <sl-button href="/anime-recommendation/<?= urlencode($mood) ?>" variant="default" size="large" style="margin-top: 1.5rem;">
            🎲 Show Me Another
        </sl-button>
    </sl-card>

    <div class="share-box">
        <p>Share this recommendation:</p>
        <!-- Shoelace styled input for visual -->
        <sl-input class="d-block" id="shareLink" readonly size="medium"
                  value="https://pickmyanime.com/anime-recommendation/<?= urlencode($mood) ?>"
                  style="max-width: 400px; cursor: pointer;margin: 0 auto;"></sl-input>

        <!-- Hidden plain input to copy from -->
        <input id="realCopyTarget" type="text"
               value="https://pickmyanime.com/anime-recommendation/<?= urlencode($mood) ?>"
               style="position: absolute; left: -9999px;" inert tabindex="-1">

        <sl-toast id="toast" duration="2000" style="--width: auto;"></sl-toast>
    </div>

    <div style="margin-top: 2rem;">
        <a href="/" style="color: #ccc;">← Back to homepage</a>
    </div>
</div>

<script type="module">
    const visualInput = document.getElementById('shareLink');
    const hiddenInput = document.getElementById('realCopyTarget');
    const toast = document.getElementById('toast');

    visualInput.addEventListener('click', async () => {
        hiddenInput.select();

        try {
            await navigator.clipboard.writeText(hiddenInput.value);
            await customElements.whenDefined('sl-toast');
            toast.variant = 'default';
            toast.textContent = '✅ Copied to clipboard!';
            toast.show();
            visualInput.classList.add('glow-pulse');
            setTimeout(() => visualInput.classList.remove('glow-pulse'), 800);
        } catch (err) {
            try {
                document.execCommand('copy');
                await customElements.whenDefined('sl-toast');
                toast.variant = 'default';
                toast.textContent = '✅ Copied (fallback)';
                toast.show();
            } catch {
                alert("❌ Copy failed. Try selecting manually.");
            }
        }
    });
</script>

</body>
</html>
