
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $tags = htmlspecialchars(trim($_POST['tags']));
    $user_id = $_SESSION['user_id'];

    if ($title && $content) {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, tags) VALUES (:user_id, :title, :content, :tags)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':title' => $title,
                ':content' => $content,
                ':tags' => $tags
            ]);
            header("Location: profile.php");
            exit();
        } catch (PDOException $e) {
            echo "Error adding post: " . $e->getMessage();
        }
    } else {
        echo "Title and Content are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
</head>
<body>
    <h2>Add New Post</h2>
    <form method="POST">
        <label for="title">Post Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br>

        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags"><br>

        <button type="submit">Add Post</button>
    </form>

    <a href="profile.php">Back to Profile</a>
</body>
</html>
