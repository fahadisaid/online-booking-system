<?php
require_once '../configuration/database.php';
require_once '../configuration/auth.php';

if (is_logged_in()) {
    redirect_by_role();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT users.user_id, users.username, users.password, role.rolename
         FROM users
         INNER JOIN user_role ON user_role.user_id = users.user_id
         INNER JOIN role ON role.role_id = user_role.role_id
         WHERE users.username = ?
         LIMIT 1"
    );
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['username'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['rolename'];
        redirect_by_role();
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Booking System</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="../assets/login.js"></script>
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <h1>Online Booking System</h1>
            <p class="muted">Sign in as passenger or admin</p>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="form">
                <label>
                    Username
                    <input type="text" name="username" required placeholder="Enter username">
                </label>

                <label>
                    Password
                    <input type="password" name="password" required placeholder="Enter password">
                </label>

                <button type="submit">Login</button>
            </form>
        </section>
    </main>
</body>
</html>
