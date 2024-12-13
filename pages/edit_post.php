
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute([':post_id' => $post_id, ':user_id' => $user_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            echo "Post not found or you do not have permission to edit this post.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error fetching post: " . $e->getMessage();
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $tags = htmlspecialchars(trim($_POST['tags']));
    $user_id = $_SESSION['user_id'];

    if ($title && $content) {
        try {
            $stmt = $conn->prepare("UPDATE posts SET title = :title, content = :content, tags = :tags WHERE post_id = :post_id AND user_id = :user_id");
            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':tags' => $tags,
                ':post_id' => $post_id,
                ':user_id' => $user_id
            ]);
            header("Location: profile.php");
            exit();
        } catch (PDOException $e) {
            echo "Error updating post: " . $e->getMessage();
        }
    } else {
        echo "Title and Content are required!";
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
</head>
<body>
    <h2>Edit Post</h2>
    <form method="POST">
        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">

        <label for="title">Post Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea><br>

        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($post['tags']); ?>"><br>

        <button type="submit">Update Post</button>
    </form>

    <a href="profile.php">Back to Profile</a>
</body>
</html>
