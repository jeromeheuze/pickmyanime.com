<?php
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/includes/auth.php';
$title = trim($_POST['title'] ?? '');
$synopsis = trim($_POST['synopsis'] ?? '');
$poster = trim($_POST['poster'] ?? '');
$streamingRaw = trim($_POST['streaming'] ?? '');
$moodIds = $_POST['moods'] ?? [];

if (!$title || empty($moodIds)) {
    exit("Title and at least one mood are required.");
}

// Validate JSON
$streaming = json_decode($streamingRaw, true);
if ($streaming === null) {
    exit("Invalid streaming JSON format.");
}
$streamingJson = json_encode($streaming, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Insert anime
$stmt = $DBcon->prepare("INSERT INTO anime_recommendations (title, synopsis, poster, streaming) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $title, $synopsis, $poster, $streamingJson);
$stmt->execute();

$animeId = $stmt->insert_id;

// Insert moods
$stmt2 = $DBcon->prepare("INSERT INTO anime_moods (anime_id, mood_id) VALUES (?, ?)");
foreach ($moodIds as $moodId) {
    $moodId = intval($moodId);
    $stmt2->bind_param("ii", $animeId, $moodId);
    $stmt2->execute();
}

echo "✅ Anime added successfully.<br><br>";
echo "<a href=\"/admin/edit-anime.php?id=$animeId\">Edit</a> | <a href=\"/admin/list.php\">Back to List</a>";
