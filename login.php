<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – ApexBlog</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <h1>🌐 ApexBlog</h1>
            <p>Sign in to continue</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Enter username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.7rem">
                🔐 Login
            </button>
        </form>

        <div class="auth-switch">
            Don't have an account? <a href="register.php">Register here</a>
        </div>

        <div style="margin-top:1rem;padding:.7rem;background:#f4f6f7;border-radius:7px;font-size:.82rem;color:#7f8c8d;text-align:center">
            Demo: <strong>admin</strong> / <strong>admin123</strong>
        </div>
    </div>
</div>
</body>
</html>
