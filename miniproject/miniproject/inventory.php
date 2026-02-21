<?php
session_start();

// Database connection
require_once 'config.php';

// Initialize filter variables
$make = isset($_GET['make']) ? $_GET['make'] : '';
$model = isset($_GET['model']) ? $_GET['model'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$price_min = isset($_GET['price_min']) ? $_GET['price_min'] : '';
$price_max = isset($_GET['price_max']) ? $_GET['price_max'] : '';

// Build SQL query with filters
$sql = "SELECT * FROM cars WHERE 1=1";

$params = array();
$types = "";

if (!empty($make)) {
    $sql .= " AND make = ?";
    $params[] = $make;
    $types .= "s";
}

if (!empty($model)) {
    $sql .= " AND model = ?";
    $params[] = $model;
    $types .= "s";
}

if (!empty($year)) {
    $sql .= " AND year = ?";
    $params[] = $year;
    $types .= "i";
}

if (!empty($price_min)) {
    $sql .= " AND price >= ?";
    $params[] = $price_min;
    $types .= "i";
}

if (!empty($price_max)) {
    $sql .= " AND price <= ?";
    $params[] = $price_max;
    $types .= "i";
}

// Get unique makes, models, and years for filter dropdowns
$makes_query = "SELECT DISTINCT make FROM cars ORDER BY make";
$models_query = "SELECT DISTINCT model FROM cars ORDER BY model";
$years_query = "SELECT DISTINCT year FROM cars ORDER BY year DESC";

$makes_result = $conn->query($makes_query);
$models_result = $conn->query($models_query);
$years_result = $conn->query($years_query);

$makes = array();
$models = array();
$years = array();

if ($makes_result->num_rows > 0) {
    while($row = $makes_result->fetch_assoc()) {
        $makes[] = $row["make"];
    }
}

if ($models_result->num_rows > 0) {
    while($row = $models_result->fetch_assoc()) {
        $models[] = $row["model"];
    }
}

if ($years_result->num_rows > 0) {
    while($row = $years_result->fetch_assoc()) {
        $years[] = $row["year"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Elite Motors</title>
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
            <h2>Our Inventory</h2>
            <p>Find your perfect vehicle</p>
        </div>
    </section>

    <div class="container">
        <div class="filter-section">
            <h3>Filter Vehicles</h3>
            <form action="inventory.php" method="get" class="filter-form">
                <div class="form-group">
                    <label for="make">Make</label>
                    <select id="make" name="make">
                        <option value="">All Makes</option>
                        <?php foreach($makes as $make_option): ?>
                            <option value="<?php echo htmlspecialchars($make_option); ?>" <?php echo $make == $make_option ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($make_option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="model">Model</label>
                    <select id="model" name="model">
                        <option value="">All Models</option>
                        <?php foreach($models as $model_option): ?>
                            <option value="<?php echo htmlspecialchars($model_option); ?>" <?php echo $model == $model_option ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($model_option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="year">Year</label>
                    <select id="year" name="year">
                        <option value="">All Years</option>
                        <?php foreach($years as $year_option): ?>
                            <option value="<?php echo htmlspecialchars($year_option); ?>" <?php echo $year == $year_option ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($year_option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price_min">Min Price</label>
                    <input type="number" id="price_min" name="price_min" value="<?php echo htmlspecialchars($price_min); ?>" placeholder="Min $">
                </div>
                
                <div class="form-group">
                    <label for="price_max">Max Price</label>
                    <input type="number" id="price_max" name="price_max" value="<?php echo htmlspecialchars($price_max); ?>" placeholder="Max $">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Apply Filters</button>
                    <a href="inventory.php" class="btn" style="background-color: #e74c3c;">Clear</a>
                </div>
            </form>
        </div>

        <div class="car-grid">
            <?php
            // Prepare statement with dynamic parameters
            if (!empty($params)) {
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $conn->query($sql);
            }
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="car-card">';
                    echo '<img src="images/' . $row["image"] . '" alt="' . $row["make"] . ' ' . $row["model"] . '">';
                    echo '<h3>' . $row["year"] . ' ' . $row["make"] . ' ' . $row["model"] . '</h3>';
                    echo '<p class="price">$' . number_format($row["price"]) . '</p>';
                    echo '<p>' . substr($row["description"], 0, 100) . '...</p>';
                    echo '<a href="car_details.php?id=' . $row["id"] . '" class="btn">View Details</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No vehicles match your search criteria. Please try different filters.</p>';
            }
            
            // Close statement if it exists
            if (isset($stmt)) {
                $stmt->close();
            }
            
            $conn->close();
            ?>
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