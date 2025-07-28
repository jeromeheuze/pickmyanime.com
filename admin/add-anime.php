<?php
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/includes/auth.php';
// Fetch all moods for the select menu
$moods = $DBcon->query("SELECT * FROM moods ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Anime – Admin</title>
    <style>
        body {
            font-family: sans-serif;
            background: #111;
            color: #fff;
            padding: 2rem;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 1rem;
            border-radius: 4px;
            border: none;
        }
        label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
        }
        button {
            padding: 10px 20px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/includes/nav.php' ?>
<h1>➕ Add New Anime</h1>

<form method="POST" action="insert-anime.php">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Synopsis</label>
    <textarea name="synopsis" rows="4"></textarea>

    <label>Poster URL</label>
    <input type="text" name="poster">

    <label>Streaming (JSON format)</label>
    <textarea name="streaming" rows="3" placeholder='e.g. {"Crunchyroll": "https://..." }'></textarea>

    <label>Moods</label>
    <select name="moods[]" multiple size="6">
        <?php foreach ($moods as $mood): ?>
            <option value="<?= $mood['id'] ?>"><?= htmlspecialchars(ucfirst($mood['name'])) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">💾 Save Anime</button>
</form>
</body>
</html>
