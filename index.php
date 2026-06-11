<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; }
        .post { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .btn { padding: 5px 10px; color: white; border: none; cursor: pointer; border-radius: 3px; text-decoration: none; }
        .btn-edit { background: #008CBA; }
        .btn-delete { background: #f44336; }
        .btn-create { background: #4CAF50; }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <a href="create_post.php" class="btn btn-create">Create New Post</a>
    <a href="logout.php">Logout</a>
    <h3>All Posts</h3>
    <?php if(empty($posts)): ?>
        <p>No posts yet. Create your first post!</p>
    <?php else: ?>
        <?php foreach($posts as $post): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <small>By <?php echo $post['username']; ?> on <?php echo $post['created_at']; ?></small><br>
                <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this post?')">Delete</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>