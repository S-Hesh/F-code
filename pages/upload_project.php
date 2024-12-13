<?php
session_start();
if (!isset($_SESSION['user_id']) || $user['role'] != 'seller') {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handling file upload
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] == 0) {
        $image_name = time() . '-' . $_FILES['project_image']['name'];
        $upload_path = 'uploads/' . $image_name;
        move_uploaded_file($_FILES['project_image']['tmp_name'], $upload_path);
    }

    // Insert project info into database
    $stmt = $conn->prepare("INSERT INTO marketplace (title, description, price, image, user_id) VALUES (:title, :description, :price, :image, :user_id)");
    $stmt->execute([
        ':title' => $_POST['title'],
        ':description' => $_POST['description'],
        ':price' => $_POST['price'],
        ':image' => $image_name,
        ':user_id' => $_SESSION['user_id']
    ]);

    echo "Project uploaded successfully!";
    header("Location: marketplace.php"); // Redirect to marketplace page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Project</title>
</head>
<body>
    <h2>Upload a New Project</h2>

    <form action="upload_project.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Project Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="number" name="price" placeholder="Price" required>
        <input type="file" name="project_image" required>
        <button type="submit">Upload Project</button>
    </form>
</body>
</html>
