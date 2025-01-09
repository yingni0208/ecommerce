<?php
session_start();
require 'db_connection.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Directory to store uploaded images
$imageDirectory = 'uploads/';

// Ensure the upload directory exists
if (!is_dir($imageDirectory)) {
    mkdir($imageDirectory, 0777, true);
}

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];  // Get quantity
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = $imageDirectory . $imageName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $message = "Error uploading image.";
        }
    }

    // Validate and insert product
    if (!empty($name) && is_numeric($price) && is_numeric($quantity)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $quantity, $imagePath);

        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $message = "Error adding product: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Invalid product details.";
    }
}

// Fetch all products
$result = $conn->query("SELECT * FROM products");
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = []; // If the query failed, ensure $products is an empty array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        h1, h2 {
            color: #ff7f0e;
            text-align: center;
        }

        a {
            color: #ff7f0e;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            color: #333;
            font-size: 1.1rem;
            text-align: center;
            margin: 20px;
        }

        .form-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            width: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            margin-bottom: 20px;
        }

        .form-section label {
            display: block;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #333;
        }

        .form-section input[type="text"],
        .form-section input[type="number"],
        .form-section input[type="file"],
        .form-section button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-section button {
            background-color: #ff7f0e;
            color: white;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }

        .form-section button:hover {
            background-color: #cc6600;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #ff7f0e;
            color: white;
        }

        table td {
            color: #333;
        }

        table img {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
        }

        table a {
            color: #ff7f0e;
            text-decoration: none;
            font-weight: bold;
        }

        table a:hover {
            text-decoration: underline;
        }

        form button[type="submit"] {
            width: auto;
            margin-top: 10px;
        }

        form input[type="file"] {
            display: inline-block;
        }

        form input[type="text"],
        form input[type="number"] {
            width: auto;
            display: inline-block;
            margin-right: 10px;
        }

        form input[type="file"] {
            width: auto;
        }
    </style>
</head>
<body>
    <h1>Manage Products</h1>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <p class="message"><?php echo isset($message) ? $message : ''; ?></p>

    <!-- Add Product Form -->
    <div class="form-section">
        <h2>Add New Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>
            <br><br>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <br><br>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>
            <br><br>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <br><br>
            <button type="submit">Add Product</button>
        </form>
    </div>

    <!-- Products Table -->
    <h2>Product List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td> <!-- Show quantity -->
                    <td>
                        <?php if ($product['image']): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" enctype="multipart/form-data" style="display: inline-block;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required> <!-- Editable quantity -->
                            <input type="file" name="image" accept="image/*">
                            <button type="submit">Update</button>
                        </form>
                        <a href="?delete_id=<?php echo htmlspecialchars($product['id']); ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
