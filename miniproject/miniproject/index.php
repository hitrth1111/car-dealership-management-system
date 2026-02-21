<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Motors - Premium Car Dealership</title>
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
                    session_start();
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
            <h2>Welcome to Elite Motors</h2>
            <p>Your destination for premium vehicles</p>
        </div>
    </section>

    <section class="featured-cars">
        <div class="container">
            <h2>Featured Vehicles</h2>
            <div class="car-grid">
                <?php
                    // Database connection
                    include 'config.php';
                    
                    // Get featured cars (limit to 3)
                    $sql = "SELECT * FROM cars WHERE featured = 1 LIMIT 3";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="car-card">';
                            echo '<img src="images/' . $row["image"] . '" alt="' . $row["make"] . ' ' . $row["model"] . '">';
                            echo '<h3>' . $row["year"] . ' ' . $row["make"] . ' ' . $row["model"] . '</h3>';
                            echo '<p class="price">$' . number_format($row["price"]) . '</p>';
                            echo '<a href="car_details.php?id=' . $row["id"] . '" class="btn">View Details</a>';
                            echo '</div>';
                        }
                    } else {
                        echo "No featured cars available at the moment.";
                    }
                    
                    $conn->close();
                ?>
            </div>
            <div class="view-all">
                <a href="inventory.php" class="btn">View All Inventory</a>
            </div>
        </div>
    </section>

    <section class="about-us">
        <div class="container">
            <h2>About Elite Motors</h2>
            <p>Elite Motors is your trusted destination for quality vehicles. We pride ourselves on exceptional customer service and a wide selection of premium cars at competitive prices.</p>
        </div>
    </section>

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