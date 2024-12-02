<?php
require '../config.php';
session_start();



// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the total amount from the form
    $total = $_POST['total'];

    // Insert the transaction into the "transaksi" table
    $sql = "INSERT INTO transaksi (user_id, total_amount, transaction_date) VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $total]);

    // Redirect to a confirmation page or display a success message
    // header("Location: confirmation.php");
    // exit();
}



// Retrieve cart items for the current user
$sql = "SELECT products.name, products.price, cart.quantity FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Clear the cart after checkout
$sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
$stmt_clear_cart = $pdo->prepare($sql_clear_cart);
$stmt_clear_cart->execute([$_SESSION['user_id']]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url(rotbak.png);
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            overflow: hidden;
        }

        .container {
            
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 7px 7px 0 rgba(0, 0, 0, 0.7);
            padding-top: 20px;
        }

        .btn-home {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Checkout Confirmed</h1>
        <p>Your order has been confirmed.</p>
        <h3>Total: Rp<?php echo number_format($total, 2, ',', '.'); ?></h3>
        <a href="../index.php" class="btn btn-primary btn-home">Back to Home</a>
    </div>
</body>

</html>