<?php
// /cron/fetch_genre_batches.php
require_once './../includes/dbconnect.php';

function logMessage($message) {
    $logFile = __DIR__ . '/fetch_genres.log';
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents($logFile, "$timestamp $message\n", FILE_APPEND);
}
logMessage("=== Genre batch fetch started ===");
echo "=== Genre batch fetch started ===<br>";
function saveAnimeFromJikan($genreId, $genreName, $pages = 1) {
    global $DBcon;
    $baseUrl = "https://api.jikan.moe/v4/anime?genres=$genreId&type=tv&order_by=score&sort=desc&page=";

    for ($page = 1; $page <= $pages; $page++) {
        $url = $baseUrl . $page;
        $json = @file_get_contents($url);
        if (!$json) continue;

        $data = json_decode($json, true);
        if (empty($data['data'])) break;

        foreach ($data['data'] as $anime) {
            $malId = $anime['mal_id'];
            $title = $anime['title'];
            $synopsis = $anime['synopsis'] ?? '';
            $posterUrl = $anime['images']['jpg']['image_url'] ?? '';
            $posterFilename = 'poster-' . $malId . '.jpg';
            $posterLocalPath = '/data/posters/' . $posterFilename;
            $savePath = __DIR__ . '/../data/posters/' . $posterFilename;

            if ($posterUrl && !file_exists($savePath)) {
                @mkdir(dirname($savePath), 0777, true);
                $imgData = @file_get_contents($posterUrl);
                if ($imgData) {
                    file_put_contents($savePath, $imgData);
                } else {
                    logMessage("Failed to download poster for $title");
                }
            }
            $year = $anime['year'] ?? null;
            $score = $anime['score'] ?? null;
            $genres = array_map(fn($g) => ucfirst(strtolower(trim($g['name']))), $anime['genres']);
            $genreString = implode(', ', $genres);

            // Check if already in DB
            $res = $DBcon->query("SELECT id FROM anime_recommendations WHERE external_id = $malId");
            if ($res && $res->num_rows > 0) {
                logMessage("Skipped: $title (already exists)");
                echo "Skipped: $title (already exists)<br>";
                continue;
            }

            $stmt = $DBcon->prepare("INSERT INTO anime_recommendations (title, synopsis, poster, external_id, source, year, score, genres, created_at) VALUES (?, ?, ?, ?, 'jikan', ?, ?, ?, NOW())");
            $stmt->bind_param("sssidds", $title, $synopsis, $posterLocalPath, $malId, $year, $score, $genreString);
            $stmt->execute();
            echo "Saved: ".$title." (ID ".$malId.") under ".$genreName."<br>";
            logMessage("Saved: $title (ID $malId) under $genreName");
        }

        sleep(1); // Respect Jikan rate limits
    }
}

// Example fetch for a few major genres
$genreMap = [
    27 => 'Shounen',
    36 => 'Slice of Life',
    22 => 'Romance',
    10 => 'Fantasy',
    1  => 'Action',
    24 => 'Sci-Fi'
];

foreach ($genreMap as $id => $name) {
    saveAnimeFromJikan($id, $name, 2); // 2 pages per genre
}

echo "=== Genre batch fetch finished ===<br>";
logMessage("=== Genre batch fetch finished ===");