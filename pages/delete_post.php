
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute([':post_id' => $post_id, ':user_id' => $user_id]);

        if ($stmt->rowCount() > 0) {
            header("Location: profile.php?message=Post deleted successfully");
            exit();
        } else {
            echo "Post not found or you do not have permission to delete this post.";
        }
    } catch (PDOException $e) {
        echo "Error deleting post: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>
