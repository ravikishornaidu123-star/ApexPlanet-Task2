<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Post deleted successfully.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Invalid post ID.'];
}

header('Location: index.php');
exit();
