<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

// --- Logic to Determine Popular Dishes ---
// This is a simplified example. In a real application, you would likely:
// 1. Track order history and the frequency of items ordered.
// 2. Have a dedicated 'is_popular' field in your 'products' table.
// 3. Use a more sophisticated algorithm to determine popularity (e.g., based on recent orders, ratings, etc.).

// For this example, we'll just fetch a few specific items that we've manually decided are popular.
$popular_dish_ids = [3, 7, 12]; // Replace with actual popular product IDs

$stmt = $mysqli->prepare("SELECT id, name, price FROM products WHERE id IN (" . implode(',', $popular_dish_ids) . ")");
if ($stmt === false) {
    die("Error preparing statement: " . $mysqli->error);
}
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    die("Error getting result: " . $mysqli->error);
}

if ($result->num_rows === 0) {
    $no_popular_dishes = true;
} else {
    $no_popular_dishes = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Dishes</title>
    <link rel="stylesheet" href="popular.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        .navbar {
            background-color: #fdd835;
            color: #222;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: bold;
        }

        .navbar button {
            background-color: #fff;
            color: #007bff;
            border: 1px solid #007bff;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .navbar button:hover {
            background-color: #007bff;
            color: white;
            border-color: #0056b3;
        }

        .container {
            max-width: 960px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .popular-list {
            list-style: none;
            padding: 0;
        }

        .popular-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .popular-item:last-child {
            border-bottom: none;
        }

        .popular-item .item-details {
            flex-grow: 1;
            margin-right: 15px;
            font-size: 1em;
            color: #333;
            display: flex;
            flex-direction: column;
        }

        .item-details span:first-child {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .item-details span:last-child {
            color: #666;
            font-size: 0.9em;
        }

        .add-to-cart {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #218838;
        }

        .no-popular {
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <button class="nav-btn" onclick="window.location.href='home.php'">Back to Home</button>
        <h1>Popular Dishes</h1>
        <div class="nav-buttons">
        </div>
    </nav>

    <div class="container">
        <h2>Our Most Loved Dishes</h2>

        <?php if ($no_popular_dishes): ?>
            <p class="no-popular">No popular dishes available at the moment.</p>
        <?php else: ?>
            <ul class="popular-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="popular-item">
                        <div class="item-details">
                            <span><?php echo htmlspecialchars($row['name']); ?></span>
                            <span>$<?php echo number_format($row['price'], 2); ?></span>
                        </div>
                        <button class="add-to-cart" onclick="confirmAddToCart(<?php echo $row['id']; ?>)">Add to Cart</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script>
        function confirmAddToCart(id) {
            if (confirm("Add this item to your cart?")) {
                window.location.href = "cart.php?action=add&id=" + id + "&quantity=1"; // Default quantity is 1 for popular dishes
            }
        }
    </script>
<?php
$stmt->close();
$mysqli->close();
?>
</body>
</html>
