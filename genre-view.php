<?php
$slug = basename($_SERVER['REQUEST_URI']); // e.g. 'action'
$genreCache = __DIR__ . '/cache/genres.json';

// Load cached genre list
$genreData = file_exists($genreCache) ? json_decode(file_get_contents($genreCache), true) : [];
$genreMap = [];

foreach ($genreData['data'] ?? [] as $g) {
    $gSlug = slugify($g['name']);
    $genreMap[$gSlug] = [
        'id' => $g['mal_id'],
        'name' => $g['name']
    ];
}
//echo "<pre>";
//print_r(array_keys($genreMap));
//echo "</pre>";
//exit;
if (!isset($genreMap[$slug])) {
    http_response_code(404);
    echo "<h1>Genre Not Found</h1>";
    exit;
}

$genreId = $genreMap[$slug]['id'];
$genreName = $genreMap[$slug]['name'];

// Fetch anime by genre
$animeList = [];
$url = "https://api.jikan.moe/v4/anime?genres=$genreId&type=tv&order_by=score&sort=desc&limit=24";
$json = @file_get_contents($url);
if ($json) {
    $data = json_decode($json, true);
    $animeList = $data['data'] ?? [];
}

function slugify($text) {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $text), '-'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($genreName) ?> Anime – PickMyAnime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Top rated <?= htmlspecialchars($genreName) ?> anime shows to explore.">
    <link rel="canonical" href="https://pickmyanime.com/genres/<?= $slug ?>" />
    <style>
        body {
            background: #111;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 2rem;
            max-width: 1000px;
            margin: auto;
        }
        a.back-link {
            color: #ffcc66;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
        h1 {
            text-align: center;
        }
        .anime-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .anime-card {
            background: #1e1e1e;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
        }
        .anime-card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
        }
        .anime-card p {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
    </style>
</head>
<body>
<p style="text-align: center; margin-bottom: 1rem;">
    <a href="/genres.php" class="back-link">← Back to Genres</a>
</p>
<h1><?= htmlspecialchars($genreName) ?> Anime</h1>
<div class="anime-grid">
    <?php foreach ($animeList as $anime): ?>
        <div class="anime-card">
            <img src="<?= $anime['images']['jpg']['image_url'] ?>" alt="<?= htmlspecialchars($anime['title']) ?>" loading="lazy">
            <p><?= htmlspecialchars($anime['title']) ?></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
