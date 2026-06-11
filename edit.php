<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit(); }

// Fetch existing post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Post not found.'];
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']   ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '') {
        $error = 'Title is required.';
    } elseif (strlen($title) > 255) {
        $error = 'Title must be under 255 characters.';
    } elseif ($content === '') {
        $error = 'Content is required.';
    } else {
        $upd = $pdo->prepare(
            "UPDATE posts SET title = :title, content = :content WHERE id = :id"
        );
        $upd->execute([':title' => $title, ':content' => $content, ':id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Post updated successfully!'];
        header("Location: view.php?id=$id");
        exit();
    }

    // Keep new values on error
    $post['title']   = $_POST['title']   ?? $post['title'];
    $post['content'] = $_POST['content'] ?? $post['content'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Post – ApexBlog</title>
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
<div class="container">

    <div class="page-header">
        <div class="page-title">Edit Post</div>
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
                       value="<?= htmlspecialchars($post['title']) ?>"
                       maxlength="255" required autofocus>
            </div>
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-warning">💾 Update Post</button>
                <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</div>
</div>

</body>
</html>
