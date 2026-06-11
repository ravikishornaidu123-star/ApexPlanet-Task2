<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

// ── Pagination config ──
define('POSTS_PER_PAGE', 5);

// ── Get search query ──
$search     = trim($_GET['search'] ?? '');
$searchType = $_GET['type'] ?? 'all';   // all | title | content
$page       = max(1, (int)($_GET['page'] ?? 1));
$offset     = ($page - 1) * POSTS_PER_PAGE;

// ── Build query dynamically ──
$params      = [];
$whereClause = '';

if ($search !== '') {
    switch ($searchType) {
        case 'title':
            $whereClause = 'WHERE p.title LIKE :search';
            $params[':search'] = "%$search%";
            break;
        case 'content':
            $whereClause = 'WHERE p.content LIKE :search';
            $params[':search'] = "%$search%";
            break;
        default: // 'all'
            $whereClause = 'WHERE (p.title LIKE :search OR p.content LIKE :search)';
            $params[':search'] = "%$search%";
    }
}

// ── Count total matching posts ──
$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM posts p $whereClause"
);
$countStmt->execute($params);
$totalPosts = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalPosts / POSTS_PER_PAGE));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * POSTS_PER_PAGE;

// ── Fetch posts for current page ──
$stmt = $pdo->prepare(
    "SELECT p.id, p.title, p.content, p.created_at, u.username
     FROM posts p
     LEFT JOIN users u ON p.author_id = u.id
     $whereClause
     ORDER BY p.created_at DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit',  POSTS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,        PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// ── Helper: highlight search term in text ──
function highlightSearch(string $text, string $term): string {
    if ($term === '') return htmlspecialchars($text);
    $escaped = preg_quote($term, '/');
    $highlighted = preg_replace(
        "/($escaped)/i",
        '<span class="highlight">$1</span>',
        htmlspecialchars($text)
    );
    return $highlighted;
}

// ── Build pagination URL helper ──
function pageUrl(int $p, string $search, string $type): string {
    $q = http_build_query(array_filter([
        'page'   => $p,
        'search' => $search,
        'type'   => $type !== 'all' ? $type : '',
    ]));
    return 'index.php' . ($q ? "?$q" : '');
}

// ── Flash message ──
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Posts – ApexPlanet Blog</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a class="navbar-brand" href="index.php">Apex<span>Blog</span></a>
    <div class="nav-links">
        <a href="index.php" class="active">📝 Posts</a>
        <a href="create.php">➕ New Post</a>
        <span class="nav-user">👤 <?= htmlspecialchars(getCurrentUser()) ?></span>
        <a href="logout.php">🚪 Logout</a>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <!-- Flash -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            All Posts
            <span><?= $totalPosts ?> post<?= $totalPosts !== 1 ? 's' : '' ?></span>
        </div>
        <a href="create.php" class="btn btn-primary">➕ New Post</a>
    </div>

    <!-- SEARCH BAR (Task 3) -->
    <form method="GET" action="index.php" class="search-bar">
        <input
            type="text"
            name="search"
            placeholder="Search posts…"
            value="<?= htmlspecialchars($search) ?>"
            autocomplete="off"
        >
        <select name="type">
            <option value="all"     <?= $searchType === 'all'     ? 'selected' : '' ?>>All fields</option>
            <option value="title"   <?= $searchType === 'title'   ? 'selected' : '' ?>>Title only</option>
            <option value="content" <?= $searchType === 'content' ? 'selected' : '' ?>>Content only</option>
        </select>
        <button type="submit" class="btn btn-primary">🔍 Search</button>
        <?php if ($search): ?>
        <a href="index.php" class="btn btn-secondary">✕ Clear</a>
        <?php endif; ?>
    </form>

    <!-- Search result info -->
    <?php if ($search): ?>
    <div class="search-results-info">
        <?php if ($totalPosts > 0): ?>
            Found <strong><?= $totalPosts ?></strong> result<?= $totalPosts !== 1 ? 's' : '' ?>
            for <strong>"<?= htmlspecialchars($search) ?>"</strong>
            <?php if ($searchType !== 'all'): ?> in <em><?= htmlspecialchars($searchType) ?></em><?php endif; ?>
        <?php else: ?>
            No results found for <strong>"<?= htmlspecialchars($search) ?>"</strong>. Try a different keyword.
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- POSTS LIST -->
    <?php if (empty($posts)): ?>
    <div class="empty-state">
        <div class="icon">📭</div>
        <h3><?= $search ? 'No matching posts found' : 'No posts yet' ?></h3>
        <p><?= $search ? 'Try different search terms.' : 'Be the first to write something!' ?></p>
        <?php if (!$search): ?>
        <a href="create.php" class="btn btn-primary" style="margin-top:1rem">Create First Post</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="posts-grid">
        <?php foreach ($posts as $post):
            $excerpt = mb_substr($post['content'], 0, 160);
            if (mb_strlen($post['content']) > 160) $excerpt .= '…';
        ?>
        <div class="post-card">
            <div class="post-card-header">
                <a href="view.php?id=<?= $post['id'] ?>" class="post-card-title">
                    <?= highlightSearch($post['title'], $search) ?>
                </a>
                <div class="post-card-actions">
                    <a href="view.php?id=<?= $post['id'] ?>"   class="btn btn-secondary btn-sm">👁 View</a>
                    <a href="edit.php?id=<?= $post['id'] ?>"   class="btn btn-warning  btn-sm">✏️ Edit</a>
                    <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-danger    btn-sm"
                       onclick="return confirm('Delete this post?')">🗑 Delete</a>
                </div>
            </div>
            <p class="post-card-excerpt">
                <?= highlightSearch($excerpt, $search) ?>
            </p>
            <div class="post-card-meta">
                <span>📅 <?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                <span>✍️ <?= htmlspecialchars($post['username'] ?? 'Unknown') ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- PAGINATION (Task 3) -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-wrapper">

        <!-- Previous -->
        <?php if ($page > 1): ?>
        <a href="<?= pageUrl($page - 1, $search, $searchType) ?>" class="page-btn wide">← Prev</a>
        <?php else: ?>
        <span class="page-btn wide disabled">← Prev</span>
        <?php endif; ?>

        <!-- Page numbers -->
        <?php
        $start = max(1, $page - 2);
        $end   = min($totalPages, $page + 2);
        if ($start > 1): ?>
            <a href="<?= pageUrl(1, $search, $searchType) ?>" class="page-btn">1</a>
            <?php if ($start > 2): ?><span class="page-btn disabled">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
        <a href="<?= pageUrl($i, $search, $searchType) ?>"
           class="page-btn <?= $i === $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span class="page-btn disabled">…</span><?php endif; ?>
            <a href="<?= pageUrl($totalPages, $search, $searchType) ?>" class="page-btn"><?= $totalPages ?></a>
        <?php endif; ?>

        <!-- Next -->
        <?php if ($page < $totalPages): ?>
        <a href="<?= pageUrl($page + 1, $search, $searchType) ?>" class="page-btn wide">Next →</a>
        <?php else: ?>
        <span class="page-btn wide disabled">Next →</span>
        <?php endif; ?>

    </div>
    <p class="pagination-info">
        Page <?= $page ?> of <?= $totalPages ?>
        &nbsp;·&nbsp;
        Showing <?= count($posts) ?> of <?= $totalPosts ?> posts
    </p>
    <?php endif; ?>

    <?php endif; ?>

</div><!-- /container -->
</div><!-- /page-wrapper -->

</body>
</html>
