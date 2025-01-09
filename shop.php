<?php
session_start();
require 'db_connection.php';

// Fetch products from the database
$result = $conn->query("SELECT * FROM products");
$products = $result->fetch_all(MYSQLI_ASSOC);

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle "Add to Cart"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Add to cart or update quantity
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    $message = "Product added to cart successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Products</title>
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
            color: green;
            font-weight: bold;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px;
        }

        .product {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            width: 250px;
        }

        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product h3 {
            color: #333;
            font-size: 1.2rem;
        }

        .product p {
            color: #666;
            font-size: 1rem;
        }

        .product form {
            margin-top: 10px;
        }

        .product input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .product button {
            background-color: #ff7f0e;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .product button:hover {
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
    </style>
</head>
<body>
    <header>
        <a href="user_dashboard.php">Back to Dashboard</a>
        <a href="cart.php">View Cart</a>
    </header>
    <h1>Shop Products</h1>
    <p class="message"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></p>

    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" required>
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> My E-Commerce Shop
    </footer>
</body>
</html>
