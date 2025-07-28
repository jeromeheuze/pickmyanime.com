<?php
require_once '../includes/dbconnect.php';

$json = file_get_contents('anime-moods.json');
$data = json_decode($json, true);

$stmt = $DBcon->prepare("INSERT INTO anime_recommendations (mood, title, synopsis, poster, streaming) VALUES (?, ?, ?, ?, ?)");

foreach ($data as $mood => $entries) {
    foreach ($entries as $entry) {
        $streaming = json_encode($entry['streaming'] ?? []);
        $stmt->bind_param(
            "sssss",
            $mood,
            $entry['title'],
            $entry['synopsis'],
            $entry['poster'],
            $streaming
        );
        $stmt->execute();
    }
}

echo "Import complete.";
?>
