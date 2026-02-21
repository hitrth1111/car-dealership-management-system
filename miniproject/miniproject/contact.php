<?php
session_start();

// Database connection
require_once 'config.php';

$error = '';
$success = '';

// Process contact form
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);
    
    // Validate input
    if(empty($name) || empty($email) || empty($message)) {
        $error = "Please fill all required fields";
    } else {
        // Save the message to database
        $sql = "INSERT INTO contact_messages (name, email, phone, message, created_at) VALUES (?, ?, ?, ?, NOW())";
        
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $name, $email, $phone, $message);
            
            if($stmt->execute()) {
                $success = "Thank you for your message! We will get back to you soon.";
                // Clear input values
                $name = $email = $phone = $message = "";
            } else {
                $error = "Something went wrong. Please try again later.";
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Elite Motors</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Elite Motors</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php 
                    if(isset($_SESSION['user_id'])) { 
                        echo '<li><a href="profile.php">My Profile</a></li>';
                        echo '<li><a href="logout.php">Logout</a></li>';
                    } else { 
                        echo '<li><a href="login.php">Login</a></li>';
                        echo '<li><a href="register.php">Register</a></li>';
                    } 
                    ?>
                </ul>
            </nav>
        </div>
    </header>

    <section class="banner">
        <div class="container">
            <h2>Contact Us</h2>
            <p>We'd love to hear from you</p>
        </div>
    </section>

    <div class="container">
        <div class="form-container">
            <h2>Send us a message</h2>
            
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="name">Full Name*</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="message">Message*</label>
                    <textarea id="message" name="message" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Send Message</button>
                </div>
            </form>
        </div>

        <div class="contact-info">
            <h2>Our Location</h2>
            <p><strong>Address:</strong> 123 Auto Drive</p>
            <p><strong>Phone:</strong> (555) 123-4567</p>
            <p><strong>Email:</strong> info@elitemotors.com</p>
            
            <h3>Hours of Operation</h3>
            <p>Monday-Friday: 9am - 8pm</p>
            <p>Saturday: 10am - 6pm</p>
            <p>Sunday: Closed</p>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Elite Motors</h3>
                    <p>Your trusted car dealership since 2010</p>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>123 Auto Drive</p>
                    <p>Phone: (555) 123-4567</p>
                    <p>Email: info@elitemotors.com</p>
                </div>
                <div class="footer-section">
                    <h3>Hours</h3>
                    <p>Monday-Friday: 9am - 8pm</p>
                    <p>Saturday: 10am - 6pm</p>
                    <p>Sunday: Closed</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 Elite Motors. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>