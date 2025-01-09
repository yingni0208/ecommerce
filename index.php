<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to E-commerce</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #FFFAF0; /* Light orange background */
            color: #333;
        }
        .container {
            margin: 0 auto;
            max-width: 400px;
        }
        h1 {
            color: #FFA500; /* Orange color for the header */
        }
        p {
            color: #FF8C00; /* Slightly darker orange for the paragraph */
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: #FF7F50; /* Coral orange color */
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #FF6347; /* Tomato orange color on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our E-commerce System</h1>
        <p>Choose your role and proceed to register or log in.</p>
        <div class="button-group">
            <button onclick="window.location.href='register.php?role=user'">Register as User</button>
            <button onclick="window.location.href='register.php?role=admin'">Register as Admin</button>
            <button onclick="window.location.href='login.php'">Login</button>
        </div>
    </div>
</body>
</html>
