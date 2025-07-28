<?php
require_once __DIR__ . '/includes/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PickMyAnime Admin</title>
    <style>
        body {
            font-family: sans-serif;
            background: #111;
            color: #fff;
            padding: 2rem;
        }
        h1 {
            margin-bottom: 1.5rem;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0 auto;
            max-width: 400px;
        }
        li {
            margin-bottom: 1rem;
        }
        a.button {
            display: block;
            padding: 1rem;
            background: #111;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1rem;
            text-align: center;
            transition: background 0.3s;
            border: 1px solid #222;
        }
        a.button:hover {
            background: #222;
        }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/includes/nav.php' ?>
<h1>📋 Admin Panel</h1>
<p>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</p>

<ul>
    <li><a class="button" href="list.php">📚 View All Anime</a></li>
    <li><a class="button" href="add-anime.php">➕ Add New Anime</a></li>
    <li><a class="button" href="dashboard.php">📊 Dashboard</a></li>
    <li><a class="button" href="logout.php">🚪 Log Out</a></li>
</ul>

</body>
</html>
