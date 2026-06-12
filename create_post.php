<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $content, $user_id])) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Failed to create post!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">📝 Blog App</span>
    <a href="index.php" class="btn btn-outline-light btn-sm">Back to Posts</a>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="mb-4">✏️ Create New Post</h3>
                    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Post Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter post title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Post Content</label>
                            <textarea name="content" class="form-control" rows="6" placeholder="Enter post content" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Create Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>