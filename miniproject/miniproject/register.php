<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
require_once 'config.php';

$error = '';
$success = '';

// Process registration form
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate input
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill all required fields";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            
            if($stmt->execute()) {
                $stmt->store_result();
                
                if($stmt->num_rows > 0) {
                    $error = "This email is already registered";
                } else {
                    // Email is available, register the user
                    $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
                    
                    if($stmt = $conn->prepare($sql)) {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt->bind_param("sss", $name, $email, $hashed_password);
                        
                        if($stmt->execute()) {
                            $success = "Registration successful! You can now login.";
                            // Clear input values
                            $name = $email = $password = $confirm_password = "";
                        } else {
                            $error = "Something went wrong. Please try again later.";
                        }
                    }
                }
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
    <title>Register - Elite Motors</title>
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
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Register</h2>
            
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Register</button>
                </div>
                
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
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