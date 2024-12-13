<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $university = htmlspecialchars(trim($_POST['university']));
    $univ_reg_no = htmlspecialchars(trim($_POST['univ_reg_no']));

    if ($full_name && $email && $university && $univ_reg_no) {
        try {
            $stmt = $conn->prepare("UPDATE users SET full_name = :full_name, email = :email, university_name = :university, university_reg_no = :univ_reg_no WHERE user_id = :user_id");
            $stmt->execute([
                ':full_name' => $full_name,
                ':email' => $email,
                ':university' => $university,
                ':univ_reg_no' => $univ_reg_no,
                ':user_id' => $user_id
            ]);
            echo "Profile updated successfully!";
        } catch (PDOException $e) {
            echo "Error updating profile: " . $e->getMessage();
        }
    } else {
        echo "All fields are required!";
    }
}

// Fetch user data
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user posts
    $posts_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = :user_id");
    $posts_stmt->execute([':user_id' => $user_id]);
    $posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- Internal CSS -->
    <style>
        /* General page styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0faff; /* Light blue background */
            color: #333; /* Black text for readability */
            margin: 0;
            padding: 0;
        }

        /* Heading styles */
        h2 {
            text-align: center;
            margin-top: 50px;
            color: #333; /* Black color for headings */
        }

        /* Input fields and buttons */
        input, textarea, button {
            width: 80%;
            margin: 10px auto;
            padding: 10px;
            display: block;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Label for inputs */
        label {
            font-size: 18px;
            margin-top: 20px;
            display: block;
            color: #333;
        }

        /* Button hover and active states */
        button {
            background-color: #5bc0de; /* Light blue */
            color: black;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #31b0d5; /* Darker blue on hover */
        }

        button:active {
            background-color: #2a8ea3; /* Slightly darker blue when clicked */
        }

        /* Posts List */
        ul {
            list-style-type: none;
            margin: 30px;
            padding: 0;
        }

        li {
            background-color: #ffffff; /* White background for posts */
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        h3 {
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }

        /* Links for navigation */
        a {
            display: block;
            text-align: center;
            padding: 10px;
            background-color: #5bc0de;
            color: black;
            text-decoration: none;
            width: 200px;
            margin: 30px auto;
            border-radius: 5px;
        }

        a:hover {
            background-color: #31b0d5;
        }

        a:active {
            background-color: #2a8ea3;
        }
    </style>
</head>
<body>

    <h2>Profile Management</h2>
    <form method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <label for="univ_reg_no">University Registration Number:</label>
        <input type="text" id="univ_reg_no" name="univ_reg_no" value="<?php echo htmlspecialchars($user['university_reg_no']); ?>" required><br>

        <label for="university">University:</label>
        <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($user['university_name']); ?>" required><br>

        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <h2>Your Posts</h2>
    <form method="POST" action="add_post.php">
        <label for="title">Post Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea><br>

        <label for="tags">Tags:</label>
        <input type="text" id="tags" name="tags"><br>

        <button type="submit">Add New Post</button>
    </form>

    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <p>Tags: <?php echo htmlspecialchars($post['tags']); ?></p>
                <form method="POST" action="edit_post.php">
                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit">Edit</button>
                </form>
                <form method="POST" action="delete_post.php">
                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>

