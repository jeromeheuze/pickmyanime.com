<?php
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/includes/auth.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    exit("Invalid ID.");
}

// Confirm existence
$stmt = $DBcon->prepare("SELECT title FROM anime_recommendations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    exit("Anime not found.");
}

// Delete it
$stmt = $DBcon->prepare("DELETE FROM anime_recommendations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "🗑️ Anime <strong>" . htmlspecialchars($result['title']) . "</strong> deleted.<br><br>";
echo "<a href='/admin/list.php'>← Back to List</a>";
