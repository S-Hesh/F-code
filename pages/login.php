<?php
require '../includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = htmlspecialchars(trim($_POST['email_or_username']));
    $password = $_POST['password'];

    if (!$email_or_username || !$password) {
        echo "Please fill in all fields.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email_or_username OR username = :email_or_username");
            $stmt->execute([':email_or_username' => $email_or_username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                echo "Login successful! Redirecting to dashboard...";
                header("Refresh: 2; url=dashboard.php");
            } else {
                echo "Invalid email/username or password.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Internal CSS -->
    <style>
        /* General styles for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0faff; /* Light blue background for the page */
            color: #333; /* Black text color for readability */
        }

        h2 {
            color: #333; /* Black color for header */
            text-align: center;
            margin-top: 50px;
        }

        /* Form container */
        form {
            background-color: white; /* White background for form */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 40%;
            margin: auto;
            box-sizing: border-box;
            margin-top: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #333; /* Black text for labels */
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9; /* Light background for input fields */
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #5bc0de; /* Light blue border when input is focused */
            outline: none;
        }

        button {
            background-color: #5bc0de; /* Light blue button background */
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #31b0d5; /* Darker blue on hover */
        }

        button:active {
            background-color: #2a8ea3; /* Slightly darker blue on button click */
        }

        /* Error message styling */
        .error {
            color: red;
            font-weight: bold;
        }

        /* Optional spacing between elements */
        br {
            margin-bottom: 10px;
        }

        /* Container for messages */
        .message {
            text-align: center;
            margin-top: 20px;
        }
    </style>

</head>
<body>

    <h2>Login</h2>

    <form method="POST" action="">
        <label for="email_or_username">Email or Username:</label>
        <input type="text" id="email_or_username" name="email_or_username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>

</body>
</html>

