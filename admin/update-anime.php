<?php
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/includes/auth.php';
$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$synopsis = trim($_POST['synopsis'] ?? '');
$poster = trim($_POST['poster'] ?? '');
$streamingRaw = trim($_POST['streaming'] ?? '');
$moodIds = $_POST['moods'] ?? [];

if (!$id || !$title) {
    exit("Missing required fields.");
}

// Validate streaming JSON
$streaming = json_decode($streamingRaw, true);
if ($streaming === null) {
    exit("Invalid JSON in streaming field.");
}
$streamingJson = json_encode($streaming, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

// Update anime info
$stmt = $DBcon->prepare("UPDATE anime_recommendations SET title = ?, synopsis = ?, poster = ?, streaming = ? WHERE id = ?");
$stmt->bind_param("ssssi", $title, $synopsis, $poster, $streamingJson, $id);
$stmt->execute();

// Update mood tags
// 1. Remove existing
$DBcon->query("DELETE FROM anime_moods WHERE anime_id = $id");

// 2. Insert selected
if (!empty($moodIds)) {
    $stmt2 = $DBcon->prepare("INSERT INTO anime_moods (anime_id, mood_id) VALUES (?, ?)");
    foreach ($moodIds as $moodId) {
        $moodId = intval($moodId);
        $stmt2->bind_param("ii", $id, $moodId);
        $stmt2->execute();
    }
}

echo "✅ Anime updated successfully.<br><br>";
echo "<a href=\"/admin/edit-anime.php?id=$id\">← Back to Edit</a> | <a href=\"/admin/\">Home</a>";
