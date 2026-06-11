<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit(); }

$stmt = $pdo->prepare(
    "SELECT p.*, u.username FROM posts p
     LEFT JOIN users u ON p.author_id = u.id
     WHERE p.id = :id LIMIT 1"
);
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Post not found.'];
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($post['title']) ?> – ApexBlog</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="navbar-brand" href="index.php">Apex<span>Blog</span></a>
    <div class="nav-links">
        <a href="index.php">📝 Posts</a>
        <a href="create.php">➕ New Post</a>
        <span class="nav-user">👤 <?= htmlspecialchars(getCurrentUser()) ?></span>
        <a href="logout.php">🚪 Logout</a>
    </div>
</nav>

<div class="page-wrapper">
<div class="container" style="max-width:800px">

    <div class="page-header">
        <a href="index.php" class="btn btn-secondary">← Back to Posts</a>
        <div style="display:flex;gap:.5rem">
            <a href="edit.php?id=<?= $post['id'] ?>"   class="btn btn-warning">✏️ Edit</a>
            <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-danger"
               onclick="return confirm('Are you sure you want to delete this post?')">🗑 Delete</a>
        </div>
    </div>

    <div class="post-full">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-full-meta">
            <span>📅 <?= date('F j, Y \a\t g:i A', strtotime($post['created_at'])) ?></span>
            <span>✍️ <?= htmlspecialchars($post['username'] ?? 'Unknown') ?></span>
        </div>
        <div class="post-full-body"><?= htmlspecialchars($post['content']) ?></div>
    </div>

</div>
</div>

</body>
</html>
