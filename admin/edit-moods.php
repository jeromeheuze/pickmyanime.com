<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/dbconnect.php';

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = intval($_POST['update_id']);
    $name = trim($_POST['new_name']);
    if ($name !== '') {
        $stmt = $DBcon->prepare("UPDATE moods SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        header("Location: edit-moods.php");
        exit;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $DBcon->prepare("DELETE FROM moods WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: edit-moods.php");
    exit;
}

// Fetch all moods
$moods = $DBcon->query("SELECT * FROM moods ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Moods</title>
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
            margin-top: 2rem;
        }
        th, td {
            padding: 0.75rem;
            border-bottom: 1px solid #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            background: #222;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
        }
        button {
            padding: 0.5rem 1rem;
            background: #ff3b3b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        a.delete {
            color: #ccc;
            text-decoration: none;
        }
        a.delete:hover {
            color: #ff6666;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/includes/nav.php' ?>
<h1>🏷️ Edit Mood Tags</h1>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Tag Name</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($moods as $mood): ?>
        <tr>
            <td><?= $mood['id'] ?></td>
            <td>
                <form method="post" style="display: flex; gap: 0.5rem;">
                    <input type="hidden" name="update_id" value="<?= $mood['id'] ?>">
                    <input type="text" name="new_name" value="<?= htmlspecialchars($mood['name']) ?>">
                    <button type="submit">Save</button>
                </form>
            </td>
            <td>
                <a class="delete" href="?delete=<?= $mood['id'] ?>"
                   onclick="return confirm('Delete this mood? It will remove links from all anime.');">🗑️ Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<p style="margin-top: 2rem;"><a href="/admin/" style="color: #aaa;">← Back to Admin Panel</a></p>
</body>
</html>
