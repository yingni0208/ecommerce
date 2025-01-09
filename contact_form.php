<?php
session_start();
require 'db_connection.php';

// Initialize message variable
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input to avoid security issues
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $messageContent = htmlspecialchars($_POST['message']);

    // Validate input
    if (empty($name) || empty($email) || empty($messageContent)) {
        $message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        // Save the message into the database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $messageContent);

        if ($stmt->execute()) {
            $message = "Thank you for contacting us! We'll get back to you soon.";
        } else {
            $message = "There was an error submitting your message. Please try again later.";
        }

        $stmt->close();
    }
}

// Fetch all user messages from the database
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($query);
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa; /* Light grey background */
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #ff7f50; /* Orange border */
        }

        label {
            font-size: 14px;
            margin-bottom: 5px;
            display: inline-block;
            color: #333;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ff7f50; /* Orange border */
            border-radius: 4px;
            font-size: 16px;
            background-color: #fff; /* White background for inputs */
        }

        input[type="email"] {
            font-family: Arial, sans-serif;
        }

        textarea {
            font-family: Arial, sans-serif;
            min-height: 120px;
        }

        button {
            background-color: #ff8c00; /* Bright Orange */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #e07b00; /* Darker Orange on hover */
        }

        .message {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #d9534f; /* Red color for error messages */
        }

        .message.success {
            color: #28a745; /* Green color for success */
        }

        .messages-list {
            margin-top: 40px;
        }

        .message-item {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .message-item p {
            margin: 5px 0;
        }

        .message-item .meta {
            font-size: 12px;
            color: #777;
        }

        .return-button {
            display: block;
            width: 100%;
            background-color: #ff8c00; /* Orange */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }

        .return-button:hover {
            background-color: #e07b00;
        }
    </style>
</head>
<body>

    <h1>Contact Us</h1>
    <div class="form-container">
        <p class="message <?php echo isset($message) ? (strpos($message, 'Thank you') !== false ? 'success' : '') : ''; ?>">
            <?php echo $message; ?>
        </p>
        
        <form method="POST" action="contact_form.php">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Your Message:</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>

    <!-- Display User Messages -->
    <div class="messages-list">
        <h2>Recent Messages</h2>
        <?php if (count($messages) > 0): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-item">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($msg['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($msg['email']); ?></p>
                    <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    <p class="meta">Received on: <?php echo $msg['created_at']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>

    <!-- Return to Index Button -->
    <a href="index.php" class="return-button">Return to Home</a>

</body>
</html>
