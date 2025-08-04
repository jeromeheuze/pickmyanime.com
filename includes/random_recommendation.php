<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once 'dbconnect.php';

function getStreamingPlatforms($title) {
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
    $cacheFile = __DIR__ . '/../cache/justwatch/' . $slug . '.json';

    // Try reading from cache first
    if (file_exists($cacheFile)) {
        $json = json_decode(file_get_contents($cacheFile), true);
    } else {
        $url = 'https://apis.justwatch.com/content/titles/en_US/popular';
        $payload = json_encode([
            "query" => $title,
            "page_size" => 1,
            "page" => 1,
            "content_types" => ["show"]
        ]);

        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $payload,
                'timeout' => 10
            ]
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);

        if (!$result) return null;

        file_put_contents($cacheFile, $result); // log to disk
        $json = json_decode($result, true);
    }

    $item = $json['items'][0] ?? null;
    if (!$item || empty($item['offers'])) return null;

    $providerMap = [
        8 => "Netflix",
        9 => "Amazon Prime Video",
        15 => "Crunchyroll",
        337 => "HIDIVE",
        384 => "Disney+",
        386 => "Apple TV"
    ];

    $streaming = [];
    foreach ($item['offers'] as $offer) {
        if ($offer['monetization_type'] === 'flatrate') {
            $id = $offer['provider_id'];
            $name = $providerMap[$id] ?? "Provider #$id";
            $streaming[] = $name;
        }
    }
    return implode(', ', array_unique($streaming));
}

// Step 1: Fetch a random anime from Jikan
$jikanUrl = 'https://api.jikan.moe/v4/random/anime';
$response = @file_get_contents($jikanUrl);
if (!$response) die('Failed to fetch random anime.');
$data = json_decode($response, true);
if (empty($data['data'])) die('Invalid response from Jikan.');
$anime = $data['data'];

// Step 2: Extract fields
$title     = $anime['title'] ?? null;
$synopsis  = $anime['synopsis'] ?? null;
$poster    = $anime['images']['jpg']['image_url'] ?? null;
$year      = $anime['year'] ?? null;
$score     = $anime['score'] ?? null;
$mal_id    = $anime['mal_id'] ?? null;
// Get genres from Jikan API
$genres = $anime['genres'] ?? [];
$genreNames = array_map(fn($g) => $g['name'], $genres);
$genreString = implode(', ', $genreNames);
$source    = 'jikan';

$posterLocalPath = null;
if ($poster) {
    $filename = 'poster-' . $mal_id . '.jpg';
    $savePath = __DIR__ . '/../data/posters/' . $filename;
    if (!file_exists($savePath)) {
        $imgData = @file_get_contents($poster);
        if ($imgData !== false) {
            file_put_contents($savePath, $imgData);
        }
    }
    $posterLocalPath = '/data/posters/' . $filename;
}

$checkStreamingStmt = $DBcon->prepare(
    'SELECT streaming, streaming_last_checked FROM anime_recommendations 
     WHERE source = ? AND external_id = ? LIMIT 1'
);
$checkStreamingStmt->bind_param('si', $source, $mal_id);
$checkStreamingStmt->execute();
$checkStreamingStmt->bind_result($existingStreaming, $lastChecked);
$checkStreamingStmt->fetch();
$checkStreamingStmt->close();

// only fetch JustWatch if no data or old data
$needsStreamingUpdate = false;
if (empty($existingStreaming)) {
    $needsStreamingUpdate = true;
} elseif (!empty($lastChecked)) {
    try {
        $daysOld = (new DateTime())->diff(new DateTime($lastChecked))->days;
        if ($daysOld > 30) $needsStreamingUpdate = true;
    } catch (Exception $e) {
        $needsStreamingUpdate = true; // fallback in case of invalid date
    }
}

$streaming = $existingStreaming;
if ($needsStreamingUpdate) {
    $streaming = getStreamingPlatforms($title);

    $updateStreaming = $DBcon->prepare(
        'UPDATE anime_recommendations SET streaming = ?, streaming_last_checked = NOW() WHERE external_id = ? AND source = ?'
    );
    $updateStreaming->bind_param('sis', $streaming, $mal_id, $source);
    $updateStreaming->execute();
    $updateStreaming->close();
}

// Step 3: Insert or update record
$checkStmt = $DBcon->prepare('SELECT id, title, synopsis, poster, year, score, streaming FROM anime_recommendations WHERE source = ? AND external_id = ?');
$checkStmt->bind_param('si', $source, $mal_id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$existing = $result->fetch_assoc();
$checkStmt->close();

if (!$existing) {
    $insertStmt = $DBcon->prepare(
        'INSERT INTO anime_recommendations 
     (title, synopsis, poster, streaming, created_at, external_id, source, year, score, genres)
     VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)'
    );
    $insertStmt->bind_param('ssssisidss', $title, $synopsis, $posterLocalPath, $streaming, $mal_id, $source, $year, $score, $genreString);

    $insertStmt->execute();
    $recId = $DBcon->insert_id;
    $insertStmt->close();
} else {
    $recId = $existing['id'];
    $updates = [];
    $params = [];
    $types  = '';

    if (empty($existing['title']) && $title) {
        $updates[] = 'title=?'; $params[] = $title; $types .= 's';
    }
    if (empty($existing['synopsis']) && $synopsis) {
        $updates[] = 'synopsis=?'; $params[] = $synopsis; $types .= 's';
    }
    if (empty($existing['poster']) && $posterLocalPath) {
        $updates[] = 'poster=?'; $params[] = $posterLocalPath; $types .= 's';
    }
    if (empty($existing['year']) && $year) {
        $updates[] = 'year=?'; $params[] = $year; $types .= 'i';
    }
    if (empty($existing['score']) && $score) {
        $updates[] = 'score=?'; $params[] = $score; $types .= 'd';
    }
    if (empty($existing['streaming']) && $streaming) {
        $updates[] = 'streaming=?'; $params[] = $streaming; $types .= 's';
    }
    if (empty($existing['genres']) && $genreString) {
        $updates[] = 'genres=?'; $params[] = $genreString; $types .= 's';
    }

    if (!empty($updates)) {
        $sql = "UPDATE anime_recommendations SET " . implode(', ', $updates) . " WHERE id=?";
        $types .= 'i';
        $params[] = $recId;
        $updateStmt = $DBcon->prepare($sql);
        $updateStmt->bind_param($types, ...$params);
        $updateStmt->execute();
        $updateStmt->close();
    }
}

// Step 4: Fetch the stored recommendation
$stmt = $DBcon->prepare(
    'SELECT id, title, synopsis, poster, year, score, streaming FROM anime_recommendations WHERE id = ?'
);
$stmt->bind_param('i', $recId);
$stmt->execute();
$result = $stmt->get_result();
$rec = $result->fetch_assoc();
$stmt->close();

$displayTitle = $rec['title'] ?? 'Untitled';
?>
<div class="anime-recommendation">
    <h2>Recommendation for You:</h2>
    <h3><?= htmlspecialchars($displayTitle) ?></h3>
    <?php if (!empty($rec['poster'])): ?>
        <img src="<?= htmlspecialchars($rec['poster']) ?>" alt="<?= htmlspecialchars($displayTitle) ?>" style="max-width:300px; border-radius:12px;" />
    <?php endif; ?>
    <?php if (!empty($rec['synopsis'])): ?>
        <p><?= nl2br(htmlspecialchars($rec['synopsis'])) ?></p>
    <?php endif; ?>
    <p><strong>Score:</strong> <?= $rec['score'] ?? 'N/A' ?> | <strong>Year:</strong> <?= $rec['year'] ?? 'N/A' ?></p>
    <?php if (!empty($rec['streaming'])): ?>
        <p><strong>Available on:</strong> <?= htmlspecialchars($rec['streaming']) ?></p>
    <?php endif; ?>
</div>
