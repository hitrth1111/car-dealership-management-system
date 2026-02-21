<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'config.php';

$user_id = $_SESSION['user_id'];
$message = '';

// Get user data
$sql = "SELECT name, email, created_at FROM users WHERE id = ?";

if($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    
    if($stmt->execute()) {
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $user = $result->fetch_assoc();
        } else {
            // User not found
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }
    
    $stmt->close();
}

// Handle profile update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    
    if(empty($name)) {
        $message = "Name cannot be empty";
    } else {
        // Update user name
        $update_sql = "UPDATE users SET name = ? WHERE id = ?";
        
        if($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param("si", $name, $user_id);
            
            if($update_stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $user['name'] = $name;
                $message = "Profile updated successfully";
            } else {
                $message = "Something went wrong. Please try again later.";
            }
            
            $update_stmt->close();
        }
    }
}

// Handle password change
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if(empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "Please fill all password fields";
    } elseif($new_password !== $confirm_password) {
        $message = "New passwords do not match";
    } elseif(strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters long";
    } else {
        // Verify current password
        $verify_sql = "SELECT password FROM users WHERE id = ?";
        
        if($verify_stmt = $conn->prepare($verify_sql)) {
            $verify_stmt->bind_param("i", $user_id);
            
            if($verify_stmt->execute()) {
                $verify_result = $verify_stmt->get_result();
                
                if($verify_result->num_rows == 1) {
                    $row = $verify_result->fetch_assoc();
                    $hashed_password = $row["password"];
                    
                    if(password_verify($current_password, $hashed_password)) {
                        // Current password is correct, update to new password
                        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        $password_sql = "UPDATE users SET password = ? WHERE id = ?";
                        
                        if($password_stmt = $conn->prepare($password_sql)) {
                            $password_stmt->bind_param("si", $new_hashed_password, $user_id);
                            
                            if($password_stmt->execute()) {
                                $message = "Password changed successfully";
                            } else {
                                $message = "Something went wrong. Please try again later.";
                            }
                            
                            $password_stmt->close();
                        }
                    } else {
                        $message = "Current password is incorrect";
                    }
                }
            }
            
            $verify_stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Elite Motors</title>
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
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>My Profile</h2>
            
            <?php if(!empty($message)): ?>
                <div class="<?php echo strpos($message, 'successfully') !== false ? 'success-message' : 'error-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-info">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            </div>
            
            <h3>Update Profile</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="update_profile" class="btn">Update Profile</button>
                </div>
            </form>
            
            <h3>Change Password</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="change_password" class="btn">Change Password</button>
                </div>
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