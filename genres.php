<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Pragma: no-cache");

// Load or cache genre list
$cacheFile = __DIR__ . '/cache/genres.json';
$cacheDuration = 60 * 60 * 24; // 1 day

if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > $cacheDuration)) {
    $apiData = @file_get_contents('https://api.jikan.moe/v4/genres/anime');
    if ($apiData) {
        @mkdir(dirname($cacheFile), 0777, true);
        file_put_contents($cacheFile, $apiData);
    }
}

$genres = [];
if (file_exists($cacheFile)) {
    $data = json_decode(file_get_contents($cacheFile), true);
    if (isset($data['data'])) {
        $genres = $data['data'];
    }
}

function slugify($text) {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $text), '-'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Anime Genres – PickMyAnime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Explore anime by genre including Action, Romance, Fantasy, Slice of Life, and more.">
    <link rel="canonical" href="https://pickmyanime.com/genres.php" />
    <style>
        body {
            background: #111;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 2rem;
            max-width: 800px;
            margin: auto;
        }
        h1 {
            text-align: center;
        }
        .genre-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        .genre-item {
            background: #1e1e1e;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: background 0.3s;
        }
        .genre-item:hover {
            background: #292929;
        }
        .genre-item a {
            color: #ffcc66;
            text-decoration: none;
            font-weight: bold;
        }
        a.back-link {
            color: #ffcc66;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<p style="text-align: center; margin-bottom: 1rem;">
    <a href="/" class="back-link">← Back Home</a>
</p>
<h1>Browse Anime by Genre</h1>
<div class="genre-list">
    <?php foreach ($genres as $genre): ?>
        <div class="genre-item">
            <a href="/genres/<?= slugify($genre['name']) ?>"><?= htmlspecialchars($genre['name']) ?></a>
            <div style="font-size: 0.9rem; color: #999;"><?= number_format($genre['count']) ?> anime</div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
