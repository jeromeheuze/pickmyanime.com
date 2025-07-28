<?php
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/includes/auth.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) exit('Invalid anime ID');

// Fetch anime
$stmt = $DBcon->prepare("SELECT * FROM anime_recommendations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$anime = $stmt->get_result()->fetch_assoc();
if (!$anime) exit('Anime not found');

// Fetch all moods
$moods = $DBcon->query("SELECT * FROM moods ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch selected moods
$stmt2 = $DBcon->prepare("SELECT mood_id FROM anime_moods WHERE anime_id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$selectedMoodIds = array_column($res2->fetch_all(MYSQLI_ASSOC), 'mood_id');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Anime – Admin</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #111; color: #fff; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 1rem; }
        label { font-weight: bold; display: block; margin-bottom: 0.5rem; }
        button { padding: 10px 20px; background: #ff3b3b; border: none; color: #fff; cursor: pointer; }
    </style>
</head>
<body>
<h1>Edit Anime: <?= htmlspecialchars($anime['title']) ?></h1>

<form method="POST" action="update-anime.php">
    <input type="hidden" name="id" value="<?= $anime['id'] ?>">

    <label>Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($anime['title']) ?>" required>

    <label>Synopsis</label>
    <textarea name="synopsis"><?= htmlspecialchars($anime['synopsis']) ?></textarea>

    <label>Poster URL</label>
    <input type="text" name="poster" value="<?= htmlspecialchars($anime['poster']) ?>">

    <label>Streaming (JSON)</label>
    <textarea name="streaming"><?= htmlspecialchars($anime['streaming']) ?></textarea>

    <label>Moods</label>
    <select name="moods[]" multiple size="6">
        <?php foreach ($moods as $mood): ?>
            <option value="<?= $mood['id'] ?>" <?= in_array($mood['id'], $selectedMoodIds) ? 'selected' : '' ?>>
                <?= htmlspecialchars(ucfirst($mood['name'])) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">💾 Save Changes</button>
</form>
</body>
</html>
