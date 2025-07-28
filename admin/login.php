<?php
session_start();
require_once __DIR__ . '/../includes/dbconnect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $DBcon->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        header('Location: list.php');
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            background: #111; color: #fff; font-family: sans-serif; padding: 2rem;
        }
        form {
            max-width: 400px; margin: auto; background: #222; padding: 2rem; border-radius: 8px;
        }
        input { width: 100%; padding: 0.75rem; margin-bottom: 1rem; }
        button { padding: 0.75rem 1.5rem; background: #ff3b3b; color: white; border: none; border-radius: 4px; }
        .error { color: red; }
    </style>
</head>
<body>
<form method="post">
    <h2>🔐 Admin Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Log In</button>
</form>
</body>
</html>
