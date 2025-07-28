<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/dbconnect.php';

// Get total anime count
$totalAnime = $DBcon->query("SELECT COUNT(*) AS total FROM anime_recommendations")->fetch_assoc()['total'] ?? 0;

// Get mood usage count
$moodStats = $DBcon->query("
  SELECT m.name, COUNT(am.anime_id) AS total
  FROM moods m
  LEFT JOIN anime_moods am ON m.id = am.mood_id
  GROUP BY m.id
  ORDER BY total DESC
")->fetch_all(MYSQLI_ASSOC);

// Get 5 most recently added
$recent = $DBcon->query("
  SELECT id, title, created_at
  FROM anime_recommendations
  ORDER BY created_at DESC
  LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard – Admin</title>
    <style>
        body {
            background: #111;
            color: #fff;
            font-family: sans-serif;
            padding: 2rem;
        }
        h1 {
            margin-bottom: 1rem;
        }
        .stats {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .card {
            background: #222;
            padding: 1.5rem;
            border-radius: 8px;
            min-width: 200px;
        }
        h2 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        ul li {
            margin-bottom: 0.3rem;
        }
        a {
            color: #00ffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/includes/nav.php' ?>
<h1>📊 Admin Dashboard</h1>

<div class="stats">
    <div class="card">
        <h2>Total Anime</h2>
        <p style="font-size: 2rem;"><?= $totalAnime ?></p>
    </div>

    <div class="card">
        <h2>Mood Tag Usage</h2>
        <ul>
            <?php foreach ($moodStats as $row): ?>
                <li><?= htmlspecialchars(ucfirst($row['name'])) ?>: <?= $row['total'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="card">
        <h2>Recently Added</h2>
        <ul>
            <?php foreach ($recent as $row): ?>
                <li>
                    <a href="edit-anime.php?id=<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['title']) ?>
                    </a> <small>(<?= substr($row['created_at'], 0, 10) ?>)</small>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

</body>
</html>
