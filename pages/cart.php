<?php
session_start();

// Simulated list of projects (this would normally be fetched from the database)
$projects = [
    1 => ['title' => 'Project 1', 'price' => 20.99],
    2 => ['title' => 'Project 2', 'price' => 30.00],
    3 => ['title' => 'Project 3', 'price' => 50.00]
];

// Initialize or retrieve the cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding projects to cart
if (isset($_GET['add'])) {
    $project_id = intval($_GET['add']);
    if (array_key_exists($project_id, $projects)) {
        $_SESSION['cart'][] = $project_id;
        header('Location: cart.php'); // Redirect to prevent re-adding on refresh
        exit();
    }
}

// Handle removing projects from cart
if (isset($_GET['remove'])) {
    $project_id = intval($_GET['remove']);
    $index = array_search($project_id, $_SESSION['cart']);
    if ($index !== false) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header('Location: cart.php');
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>

    <!-- Internal CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0faff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        .cart-container {
            width: 80%;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 10px 20px;
            margin-top: 20px;
            display: inline-block;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #218838;
        }

        .remove-btn {
            color: #e74c3c;
            cursor: pointer;
        }

    </style>
</head>
<body>

<h1>Shopping Cart</h1>

<div class="cart-container">
    <?php if (!empty($_SESSION['cart'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display cart items
                foreach ($_SESSION['cart'] as $cart_item):
                    $project = $projects[$cart_item];
                    $total += $project['price'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td>$<?php echo number_format($project['price'], 2); ?></td>
                        <td><a href="cart.php?remove=<?php echo $cart_item; ?>" class="remove-btn">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

</body>
</html>
