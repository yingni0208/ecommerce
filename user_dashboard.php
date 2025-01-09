<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

echo "Welcome User, " . htmlspecialchars($_SESSION['username']);
echo "<br><a href='logout.php' class='logout'>Logout</a>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fdf6e3;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #ff7f0e;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }

        h2 {
            color: #ff7f0e;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #ff7f0e;
            font-weight: bold;
        }

        a:hover {
            color: #cc6600;
        }

        .logout {
            display: inline-block;
            margin-top: 10px;
            background-color: #ff7f0e;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
        }

        .logout:hover {
            background-color: #cc6600;
        }

        .content {
            text-align: center;
            margin-top: 20px;
        }

        .shop-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff7f0e;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }

        .shop-link:hover {
            background-color: #cc6600;
        }
    </style>
</head>
<body>
    <header>
        <h1>User Dashboard</h1>
    </header>
    <div class="content">
        <h2>Welcome to your dashboard!</h2>
        <a href="shop.php" class="shop-link">Shop Products</a>
    </div>
</body>
</html>
