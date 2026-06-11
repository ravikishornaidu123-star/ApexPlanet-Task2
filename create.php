<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']   ?? '');
    $content = trim($_POST['content'] ?? '');

    // Server-side validation
    if ($title === '') {
        $error = 'Title is required.';
    } elseif (strlen($title) > 255) {
        $error = 'Title must be under 255 characters.';
    } elseif ($content === '') {
        $error = 'Content is required.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO posts (title, content, author_id) VALUES (:title, :content, :author)"
        );
        $stmt->execute([
            ':title'   => $title,
            ':content' => $content,
            ':author'  => $_SESSION['user_id'],
        ]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Post created successfully!'];
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Post – ApexBlog</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="navbar-brand" href="index.php">Apex<span>Blog</span></a>
    <div class="nav-links">
        <a href="index.php">📝 Posts</a>
        <a href="create.php" class="active">➕ New Post</a>
        <span class="nav-user">👤 <?= htmlspecialchars(getCurrentUser()) ?></span>
        <a href="logout.php">🚪 Logout</a>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <div class="page-header">
        <div class="page-title">Create New Post</div>
        <a href="index.php" class="btn btn-secondary">← Back to Posts</a>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST">
            <div class="form-group">
                <label for="title">Post Title *</label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="Enter an engaging title…"
                       maxlength="255" required autofocus>
            </div>
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content"
                          placeholder="Write your post content here…"
                          required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Publish Post</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</div>
</div>

</body>
</html>
