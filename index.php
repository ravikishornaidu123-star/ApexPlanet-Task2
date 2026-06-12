<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination
$posts_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Count total posts
if ($search) {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?");
    $count_stmt->execute(["%$search%", "%$search%"]);
} else {
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM posts");
}
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts
if ($search) {
    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.title LIKE ? OR posts.content LIKE ? ORDER BY posts.created_at DESC LIMIT $posts_per_page OFFSET $offset");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC LIMIT $posts_per_page OFFSET $offset");
    $stmt->execute([]);
}
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">📝 Blog App</span>
    <span class="text-white">Welcome, <?php echo $_SESSION['username']; ?>!</span>
    <div>
        <a href="create_post.php" class="btn btn-success btn-sm me-2">+ Create Post</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">

    <!-- Search Form -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
            <?php if($search): ?>
                <a href="index.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Posts -->
    <?php if($search): ?>
        <h5>Search results for: <strong><?php echo htmlspecialchars($search); ?></strong></h5>
    <?php endif; ?>

    <?php if(empty($posts)): ?>
        <div class="alert alert-info">No posts found!</div>
    <?php else: ?>
        <?php foreach($posts as $post): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                    <small class="text-muted">By <?php echo $post['username']; ?> on <?php echo $post['created_at']; ?></small>
                    <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                        <div class="mt-2">
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this post?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>