<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

// Get category from URL and validate
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $_GET['category'];

    // Validate category before querying (avoiding SQL injection)
    $stmt = $mysqli->prepare("SELECT DISTINCT category FROM products WHERE category = ? LIMIT 1");
    if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        // Category not found, redirect to categories page
        header("Location: categories.php?error=invalid_category");
        exit();
    }
    $stmt->close();

    // Fetch products under the category
    $stmt = $mysqli->prepare("SELECT id, name, price FROM products WHERE category = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
     if ($result === false) {
        die("Error getting result: " . $mysqli->error);
    }
} else {
    // Redirect if no category is provided
    header("Location: categories.php?error=no_category");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> - Menu</title>
    <link rel="stylesheet" href="menu.css">
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

        .menu-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #eee;
            background-color: #fff;
        }

        .menu-table th, .menu-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .menu-table th {
            background-color: #f9f9f9;
            color: #333;
            font-weight: bold;
        }

        .menu-table tr:last-child td {
            border-bottom: none;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        .quantity-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .quantity-btn:hover {
            background-color: #0056b3;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            font-size: 1em;
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
    </style>
</head>
<body>

    <nav class="navbar">
        <button class="nav-btn" onclick="window.location.href='categories.php'">Back to Categories</button>
        <h1><?php echo htmlspecialchars($category); ?></h1>
        <div class="nav-buttons">
        </div>
    </nav>

    <div class="container">
        <table class="menu-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                 if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="decreaseQuantity(<?php echo $row['id']; ?>)">−</button>
                                <input type="number" id="quantity-<?php echo $row['id']; ?>" class="quantity-input" value="1" min="1">
                                <button class="quantity-btn" onclick="increaseQuantity(<?php echo $row['id']; ?>)">+</button>
                            </div>
                        </td>
                        <td>
                            <button class="add-to-cart" onclick="confirmAddToCart(<?php echo $row['id']; ?>)">Add to Cart</button>
                        </td>
                    </tr>
                <?php 
                    }
                  } else{
                    echo  "<tr><td colspan='4' style='text-align:center'>No items found in this category.</td></tr>";
                  }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function increaseQuantity(id) {
            let quantityInput = document.getElementById("quantity-" + id);
            let currentQuantity = parseInt(quantityInput.value);
            quantityInput.value = currentQuantity + 1;
        }

        function decreaseQuantity(id) {
            let quantityInput = document.getElementById("quantity-" + id);
            let currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
            }
        }

        function confirmAddToCart(id) {
            let quantity = document.getElementById("quantity-" + id).value;
            if (confirm("Add " + quantity + " of this item to cart?")) {
                window.location.href = "cart.php?action=add&id=" + id + "&quantity=" + quantity;
            }
        }
    </script>
<?php
  if (isset($stmt)) {
     $stmt->close();
  }
  $mysqli->close();
?>
</body>
</html>
