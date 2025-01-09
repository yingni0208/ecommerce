<?php
// Initialize message variable
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input to avoid security issues
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $messageContent = htmlspecialchars($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($messageContent)) {
        $message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        // Send email (You can use mail() function or integrate with an email service)
        $to = "your-email@example.com";  // Replace with your email address
        $subject = "Contact Form Submission: $name";
        $body = "Name: $name\nEmail: $email\nMessage: $messageContent";
        $headers = "From: no-reply@yourdomain.com\r\n";
        
        // Use mail() function to send the email
        if (mail($to, $subject, $body, $headers)) {
            $message = "Thank you for contacting us! We'll get back to you soon.";
        } else {
            $message = "There was an error sending your message. Please try again later.";
        }
    }
}
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
            background-color: #fff8f0;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff7f50; /* Orange color */
        }

        .form-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 14px;
            margin-bottom: 5px;
            display: inline-block;
            color: #ff7f50; /* Orange color */
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ff7f50; /* Orange border */
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="email"] {
            font-family: Arial, sans-serif;
        }

        textarea {
            font-family: Arial, sans-serif;
            min-height: 120px;
        }

        button {
            background-color: #ff7f50; /* Orange background */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #ff5722; /* Darker orange on hover */
        }

        .message {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #d9534f;
        }

        .message.success {
            color: #28a745; /* Green color for success */
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

</body>
</html>
