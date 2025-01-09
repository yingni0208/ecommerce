<?php
session_start();
require 'db_connection.php';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}

// Fetch product details for items in the cart
$ids = implode(',', array_keys($_SESSION['cart']));

// Assuming the correct column name is `quantity`, update the SQL query:
$result = $conn->query("SELECT id, name, price, quantity FROM products WHERE id IN ($ids)");
$cartItems = $result->fetch_all(MYSQLI_ASSOC);

// Ensure $cartItems is not null or empty
if (!$cartItems) {
    $cartItems = [];
}

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect to login if not logged in
        exit;
    }

    // Check if payment method is selected
    if (!isset($_POST['payment_method'])) {
        $message = "Please select a payment method.";
    } else {
        $paymentMethod = $_POST['payment_method'];  // Capture payment method

        // Begin transaction
        $conn->begin_transaction();

        try {
            $totalPrice = 0;
            // Reduce stock for each product in the cart
            foreach ($cartItems as $item) {
                $productId = $item['id'];
                $quantity = $_SESSION['cart'][$productId];

                // Ensure the quantity (or stock) key exists
                if (!isset($item['quantity'])) {
                    throw new Exception("Stock information is missing for product: " . $item['name']);
                }

                // Check if enough stock is available
                if ($item['quantity'] < $quantity) {
                    throw new Exception("Not enough stock for product: " . $item['name']);
                }

                // Update the stock in the database
                $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $stmt->bind_param("ii", $quantity, $productId);
                $stmt->execute();

                // Calculate total price for the product in the cart
                $subtotal = $item['price'] * $quantity;

                // Insert order into the orders table
                $userId = $_SESSION['user_id']; // Assuming user_id is stored in session
                $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, payment_method) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiids", $userId, $productId, $quantity, $subtotal, $paymentMethod);
                $stmt->execute();

                // Add to the total order price
                $totalPrice += $subtotal;
            }

            // Commit the transaction
            $conn->commit();

            // Clear the cart after successful checkout
            unset($_SESSION['cart']);

            // Log the user out and redirect to index
            session_destroy(); // Destroy session to log out the user
            header("Location: index.php"); // Redirect to homepage
            exit;

        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $conn->rollback();
            $message = "Error placing order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
            padding: 10px;
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

        .total-price {
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 20px;
            color: #333;
        }

        .payment-method {
            text-align: center;
            margin: 20px;
        }

        .payment-method h3 {
            color: #ff7f0e;
        }

        .payment-method label {
            display: block;
            margin-bottom: 10px;
            font-size: 1rem;
            color: #333;
        }

        .payment-method input {
            margin-right: 10px;
        }

        .button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #ff7f0e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .button:hover {
            background-color: #cc6600;
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
        <a href="cart.php">Back to Cart</a>
    </header>

    <h1>Checkout</h1>
    <p class="message"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></p>

    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalPrice = 0; ?>
                <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo $_SESSION['cart'][$item['id']]; ?></td>
                            <td>$<?php echo $_SESSION['cart'][$item['id']] * $item['price']; ?></td>
                        </tr>
                        <?php $totalPrice += $_SESSION['cart'][$item['id']] * $item['price']; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No items in your cart.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p class="total-price">Total: $<?php echo $totalPrice; ?></p>

        <!-- Payment Method Selection -->
        <div class="payment-method">
            <h3>Select Payment Method</h3>
            <label>
                <input type="radio" name="payment_method" value="Credit Card" required> Credit Card
            </label>
            <label>
                <input type="radio" name="payment_method" value="PayPal" required> PayPal
            </label>
            <label>
                <input type="radio" name="payment_method" value="Bank Transfer" required> Bank Transfer
            </label>
        </div>

        <button type="submit" name="confirm_order" class="button">Confirm Order</button>
    </form>

    <footer>
        &copy; <?php echo date('Y'); ?> My E-Commerce Shop | <a href="contact_form.php">Contact Us</a>
    </footer>
</body>
</html>
