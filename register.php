<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (isLoggedIn()) { header('Location: index.php'); exit(); }

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username taken
        $check = $pdo->prepare("SELECT id FROM users WHERE username = :u");
        $check->execute([':u' => $username]);
        if ($check->fetch()) {
            $error = 'Username already taken. Choose another.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $pdo->prepare("INSERT INTO users (username, password) VALUES (:u, :p)");
            $ins->execute([':u' => $username, ':p' => $hash]);
            $success = 'Account created! You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – ApexBlog</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <h1>🌐 ApexBlog</h1>
            <p>Create your account</p>
        </div>

        <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min. 6 characters" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;padding:.7rem">
                ✅ Register
            </button>
        </form>

        <div class="auth-switch">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>
</body>
</html>
