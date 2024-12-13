<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require '../includes/db_connect.php';

$user_id = $_SESSION['user_id'];

try {
    // Fetch marketplace projects associated with the logged-in user
    $stmt = $conn->prepare("SELECT * FROM marketplace WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    
    // Fetch all results as an associative array
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no projects are found, define sample projects
    if (!$projects) {
        $projects = [
            [
                'project_id' => 1,
                'title' => 'Project 1',
                'image' => 'sample1.jpg',
                'description' => 'Sample project 1 description.',
                'price' => 20.99
            ],
            [
                'project_id' => 2,
                'title' => 'Project 2',
                'image' => 'sample2.jpg',
                'description' => 'Sample project 2 description.',
                'price' => 30.00
            ],
            [
                'project_id' => 3,
                'title' => 'Project 3',
                'image' => 'sample3.jpg',
                'description' => 'Sample project 3 description.',
                'price' => 50.00
            ]
        ];
    }
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
    <title>Marketplace</title>

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

        h1 {
            text-align: center;
            margin-top: 50px;
            color: #333; /* Black color for headings */
        }

        /* Container for project listings */
        .projects {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px;
            padding: 20px;
        }

        /* Styles for each project */
        .project {
            background-color: #fff; /* White background for projects */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .project img {
            width: 200px;
            height: auto;
            border-radius: 8px;
        }

        .project h3 {
            font-size: 24px;
            color: #333;
            margin-top: 15px;
        }

        .project p {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }

        .project a {
            display: inline-block;
            margin-top: 15px;
            color: #5bc0de; /* Light blue color for links */
            text-decoration: none;
            padding: 8px 12px;
            border: 1px solid #5bc0de;
            border-radius: 5px;
        }

        .project a:hover {
            background-color: #5bc0de;
            color: black;
        }

        .add-to-cart-btn {
            margin-top: 15px;
            display: inline-block;
            background-color: #28a745; /* Green background for Add to Cart button */
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .add-to-cart-btn:hover {
            background-color: #218838; /* Darker green for hover effect */
        }

        /* Empty projects message */
        p {
            text-align: center;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>

<h1>Marketplace</h1>

<?php if ($projects): ?>
    <div class="projects">
        <?php foreach ($projects as $project): ?>
            <div class="project">
                <img src="uploads/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" style="width:200px;">
                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                <p><?php echo htmlspecialchars($project['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($project['price']); ?></p>
                
                <!-- Add to Cart Button -->
                <a href="cart.php?add=<?php echo $project['project_id']; ?>" class="add-to-cart-btn">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No projects available.</p>
<?php endif; ?>

</body>
</html>

