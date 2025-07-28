<?php
require_once __DIR__ . '/../includes/dbconnect.php';
require_once __DIR__ . '/includes/auth.php';
// Fetch all anime with their moods
$query = "
  SELECT a.id, a.title, a.poster, GROUP_CONCAT(m.name ORDER BY m.name SEPARATOR ', ') AS moods
  FROM anime_recommendations a
  LEFT JOIN anime_moods am ON a.id = am.anime_id
  LEFT JOIN moods m ON am.mood_id = m.id
  GROUP BY a.id
  ORDER BY a.title ASC
";
$result = $DBcon->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Anime List – Admin</title>
    <style>
        body {
            font-family: sans-serif;
            background: #111;
            color: #fff;
            padding: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #333;
            text-align: left;
        }
        a.edit {
            background: #ff3b3b;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
        }
        img {
            height: 64px;
            border-radius: 8px;
        }
        .actions {
            text-align: right;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/includes/nav.php' ?>
<h1>📚 Anime Library</h1>

<table>
    <thead>
    <tr>
        <th>Poster</th>
        <th>Title</th>
        <th>Moods</th>
        <th class="actions">Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <img src="<?= htmlspecialchars($row['poster']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
            </td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['moods'] ?? '-') ?></td>
            <td class="actions">
                <a class="edit" href="/admin/edit-anime.php?id=<?= $row['id'] ?>">Edit</a>
                &nbsp;
                <a class="edit" style="background:#888;" href="/admin/delete-anime.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Are you sure you want to delete this anime?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
