
<?php
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $username = htmlspecialchars(trim($_POST['username']));
    $univ_reg_no = htmlspecialchars(trim($_POST['univ_reg_no']));
    $university = htmlspecialchars(trim($_POST['university']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Validate required fields
    if (!$full_name || !$username || !$univ_reg_no || !$university || !$email || !$password) {
        echo "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%^&+=]).{8,}$/', $password)) {
        echo "Password must be at least 8 characters long, contain one uppercase letter, one number, and one special character.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the database
        try {
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, university_reg_no, university_name, email, password) VALUES (:full_name, :username, :univ_reg_no, :university, :email, :password)");
            $stmt->execute([
                ':full_name' => $full_name,
                ':username' => $username,
                ':univ_reg_no' => $univ_reg_no,
                ':university' => $university,
                ':email' => $email,
                ':password' => $password_hash
            ]);

            echo "Registration successful! Redirecting to login page...";
            header("Refresh: 2; url=login.php");
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
    <title>Registration</title><!-- Internal CSS -->
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
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9; /* Light background for input fields */
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
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
    <h2>User Registration</h2>
    <form method="POST" action="">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required><br>

        <label for="username">User Name:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="univ_reg_no">University Registration No:</label>
        <input type="text" id="univ_reg_no" name="univ_reg_no" required><br>

        <label for="university">University:</label>
        <input type="text" id="university" name="university" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
