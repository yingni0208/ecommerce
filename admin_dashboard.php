<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFFAF0; /* Light orange background */
            color: #333;
            text-align: center;
            padding: 50px;
        }
        h2 {
            color: #FFA500; /* Orange color for the header */
        }
        .container {
            background-color: #FFF; /* White background for the container */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-top: 30px;
            width: 100%;
            max-width: 600px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #FF7F50; /* Coral orange color */
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        a:hover {
            background-color: #FF6347; /* Tomato orange color on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p>Welcome Admin, <?php echo $_SESSION['username']; ?></p>
        <a href="manage_products.php">Manage Products</a>
        <br><br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
