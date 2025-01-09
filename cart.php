<?php
session_start();
require 'db_connection.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $cartItems = [];
    $message = "Your cart is empty.";
} else {
    // Fetch product details for items in the cart
    $ids = implode(',', array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle "Remove from Cart"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = (int)$_POST['remove_id'];
    unset($_SESSION['cart'][$removeId]);
    header("Location: cart.php");
    exit;
}

// Calculate total price
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $_SESSION['cart'][$item['id']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fdf6e3;
            color: #333;
        }

        header {
            background-color: #ff7f0e;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        header a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        header a:hover {
            text-decoration: underline;
        }

        h1 {
            text-align: center;
            color: #ff7f0e;
            margin-top: 20px;
        }

        .message {
            text-align: center;
            color: red;
            font-weight: bold;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #ff7f0e;
            color: white;
        }

        table td {
            color: #555;
        }

        table td form {
            display: inline-block;
        }

        table td button {
            background-color: #ff7f0e;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        table td button:hover {
            background-color: #cc6600;
        }

        .total-price {
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px;
            color: #333;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #ff7f0e;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        footer a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <a href="shop.php">Continue Shopping</a>
        <a href="checkout.php">Checkout</a>
    </header>

    <h1>Your Cart</h1>
    <p class="message"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></p>

    <?php if (!empty($cartItems)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                        <td><?php echo $_SESSION['cart'][$item['id']]; ?></td>
                        <td>$<?php echo $item['price'] * $_SESSION['cart'][$item['id']]; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="remove_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                <button type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="total-price">Total Price: $<?php echo $totalPrice; ?></p>
    <?php endif; ?>

    <footer>
        &copy; <?php echo date('Y'); ?> My E-Commerce Shop | <a href="contact.php">Contact Us</a>
    </footer>
</body>
</html>
