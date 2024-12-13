<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Dashboard</title>

    <!-- Internal CSS -->
    <style>
        /* General page styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0faff; /* Light blue background for the page */
            color: #333; /* Black text color for better readability */
            margin: 0;
            padding: 0;
        }

        /* Heading styles */
        h2 {
            text-align: center;
            margin-top: 50px;
            color: #333; /* Black color for the heading */
        }

        /* Paragraph styles */
        p {
            font-size: 18px;
            color: #333;
            line-height: 1.6;
            text-align: center;
        }

        /* Links styling */
        a {
            display: block;
            width: 200px;
            margin: 10px auto;
            text-align: center;
            padding: 10px;
            background-color: #5bc0de; /* Light blue background for links */
            color: black;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
        }

        a:hover {
            background-color: #31b0d5; /* Darker blue on hover */
        }

        a:active {
            background-color: #2a8ea3; /* Slightly darker blue when clicked */
        }

        /* Optional Container for displaying the info */
        .profile-info {
            background-color: white; /* White background for the profile details */
            padding: 30px;
            width: 60%;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Additional styling for logout notice */
        .logout-link {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>

    <div class="profile-info">
        <p>Full Name: <?php echo htmlspecialchars($user['full_name']); ?></p>
        <p>University: <?php echo htmlspecialchars($user['university_name']); ?></p>
        <p>Registration Number: <?php echo htmlspecialchars($user['university_reg_no']); ?></p>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <!-- Navigation links -->
    <a href="profile.php">Manage Profile</a>
    <a href="knowledge_sharing.php">Knowledge Sharing</a>
    <a href="marketplace.php">Marketplace</a>
    <div class="logout-link">
        <a href="logout.php">Logout</a>
    </div>

</body>
</html>
