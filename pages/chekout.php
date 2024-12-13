<?php
session_start();
require 'vendor/autoload.php'; // Include Stripe PHP SDK

// Stripe keys (replace these with your own keys)
$stripeSecretKey = 'sk_test_YOUR_SECRET_KEY';
$stripePublishableKey = 'pk_test_YOUR_PUBLISHABLE_KEY';

\Stripe\Stripe::setApiKey($stripeSecretKey);

// If the cart is empty, redirect back to cart
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Simulate projects data (this would come from your database)
$projects = [
    1 => ['title' => 'Project 1', 'price' => 20.99],
    2 => ['title' => 'Project 2', 'price' => 30.00],
    3 => ['title' => 'Project 3', 'price' => 50.00]
];

// Calculate the total price of the cart items
$total = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    if (isset($projects[$cart_item])) {
        $total += $projects[$cart_item]['price'];
    }
}

// Prepare data to be sent to Stripe
$amount = $total * 100; // Convert dollars to cents

// Handle the Stripe payment intent on the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create a new payment intent with the calculated total amount
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method' => $_POST['payment_method_id'],
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        // If payment successful, clear cart and redirect user (for example)
        if ($paymentIntent->status == 'succeeded') {
            $_SESSION['cart'] = [];
            header('Location: success.php'); // Redirect to a success page
            exit();
        }

    } catch (\Stripe\Exception\CardException $e) {
        $error = 'Payment failed: ' . $e->getError()->message;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- Include Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0faff;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }

        .checkout-container {
            width: 60%;
            margin: 20px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .checkout-details {
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 24px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Checkout</h1>

<div class="checkout-container">
    <h2>Review Your Cart</h2>

    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="checkout-details">
        <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
        <!-- Loop through cart items and display them -->
        <?php foreach ($_SESSION['cart'] as $cart_item): ?>
            <?php $project = $projects[$cart_item]; ?>
            <p><?php echo htmlspecialchars($project['title']); ?> - $<?php echo number_format($project['price'], 2); ?></p>
        <?php endforeach; ?>
    </div>

    <!-- Payment form -->
    <form action="checkout.php" method="POST" id="payment-form">
        <div id="payment-request-button"></div> <!-- Stripe Elements Payment Request button -->

        <!-- Hidden input for payment method id (sent after Stripe client-side processing) -->
        <input type="hidden" name="payment_method_id" id="payment_method_id">
        <button type="submit" class="btn">Pay Now</button>
    </form>
</div>

<script>
// Stripe Setup
var stripe = Stripe('<?php echo $stripePublishableKey; ?>'); // Your Stripe publishable key
var elements = stripe.elements();

// Create a Payment Request Button with Stripe
var paymentRequest = stripe.paymentRequest({
    country: 'US',
    currency: 'usd',
    total: {
        label: 'Total amount',
        amount: <?php echo $amount; ?>,  // Total amount (cents)
    },
    requestPayerName: true,
    requestPayerEmail: true,
});

// Create an instance of the Payment Request Button
var prButton = elements.create('paymentRequestButton', {
    paymentRequest: paymentRequest,
});

// Check if the Payment Request Button can be displayed
paymentRequest.canMakePayment().then(function(result) {
    if (result && result.bool) {
        prButton.mount('#payment-request-button');
    } else {
        // Fallback UI if Payment Request is not available
        document.getElementById('payment-request-button').style.display = 'none';
    }
});

// Handle form submission
prButton.on('paymentmethod', async function(ev) {
    ev.preventDefault();

    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: ev.complete,
    });

    if (error) {
        console.log(error);
        alert("There was an error. Please try again.");
    } else {
        document.getElementById('payment_method_id').value = paymentMethod.id;
        document.getElementById('payment-form').submit();
    }
});
</script>

</body>
</html>
