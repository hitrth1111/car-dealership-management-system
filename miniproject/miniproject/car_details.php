<?php
session_start();

// Database connection
require_once 'config.php';

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: inventory.php");
    exit();
}

$car_id = $_GET['id'];

// Get car details
$sql = "SELECT * FROM cars WHERE id = ?";

if($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $car_id);
    
    if($stmt->execute()) {
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $car = $result->fetch_assoc();
        } else {
            // Car not found
            header("Location: inventory.php");
            exit();
        }
    } else {
        echo "Something went wrong. Please try again later.";
        exit();
    }
    
    $stmt->close();
} else {
    echo "Something went wrong. Please try again later.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car["year"] . ' ' . $car["make"] . ' ' . $car["model"]; ?> - Elite Motors</title>
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

    <div class="container">
        <div class="car-details">
            <div class="car-images">
                <img src="images/<?php echo $car["image"]; ?>" alt="<?php echo $car["make"] . ' ' . $car["model"]; ?>">
            </div>
            
            <div class="car-info">
                <h2><?php echo $car["year"] . ' ' . $car["make"] . ' ' . $car["model"]; ?></h2>
                <p class="price">$<?php echo number_format($car["price"]); ?></p>
                
                <ul class="car-specs">
                    <li><strong>Year:</strong> <?php echo $car["year"]; ?></li>
                    <li><strong>Make:</strong> <?php echo $car["make"]; ?></li>
                    <li><strong>Model:</strong> <?php echo $car["model"]; ?></li>
                    <li><strong>Mileage:</strong> <?php echo number_format($car["mileage"]); ?> miles</li>
                    <li><strong>Color:</strong> <?php echo $car["color"]; ?></li>
                    <li><strong>Transmission:</strong> <?php echo $car["transmission"]; ?></li>
                    <li><strong>Engine:</strong> <?php echo $car["engine"]; ?></li>
                    <li><strong>VIN:</strong> <?php echo $car["vin"]; ?></li>
                </ul>
                
                <div class="car-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br($car["description"]); ?></p>
                </div>
                
                <div class="car-actions">
                    <a href="contact.php" class="btn">Inquire About This Vehicle</a>
                    <a href="inventory.php" class="btn" style="background-color: #34495e;">Back to Inventory</a>
                </div>
            </div>
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