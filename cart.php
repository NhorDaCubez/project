<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

$user_id = $_SESSION['id'];

// Function to update or add items to the cart
function updateCart($mysqli, $user_id, $product_id, $quantity) {
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Check if the product is already in the cart
    $stmt = $mysqli->prepare("SELECT quantity FROM carts WHERE user_id = ? AND product_id = ?");
     if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
     if ($result === false) {
        die("Error getting result: " . $mysqli->error);
    }

    if ($row = $result->fetch_assoc()) {
        // Update quantity
        $new_quantity = $row['quantity'] + $quantity;
        $stmt = $mysqli->prepare("UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?");
         if ($stmt === false) {
            die("Error preparing statement: " . $mysqli->error);
        }
        $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $stmt->execute();
    } else {
        // Insert new product
        $stmt = $mysqli->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)");
         if ($stmt === false) {
            die("Error preparing statement: " . $mysqli->error);
        }
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }
    $stmt->close();
}

// Add to cart action
if (isset($_GET['action']) && $_GET['action'] == "add" && isset($_GET['id']) && isset($_GET['quantity'])) {
    $product_id = intval($_GET['id']);
    $quantity = intval($_GET['quantity']);
    updateCart($mysqli, $user_id, $product_id, $quantity);
    header("Location: cart.php");
    exit();
}



// Remove item from cart
if (isset($_GET['action']) && $_GET['action'] == "remove" && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $mysqli->prepare("DELETE FROM carts WHERE user_id = ? AND product_id = ?");
     if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: cart.php");
    exit();
}

// Fetch cart items from the database
$stmt = $mysqli->prepare("SELECT c.product_id, c.quantity, p.name, p.price FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
 if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
 if ($result === false) {
        die("Error getting result: " . $mysqli->error);
    }

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();

$is_cart_empty = count($cart_items) === 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background-color: #333;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            box-sizing: border-box;
        }

        .sidebar .logo {
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar h2 {
            margin: 0;
            font-size: 1.5em;
            color: #eee;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #fff;
            font-size: 1em;
            transition: background-color 0.2s ease;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #00ff00;
        }

        .sidebar ul li a .icon {
            margin-right: 8px;
            font-size: 1.2em;
            vertical-align: middle;
        }

        .sidebar ul li a.logout-link {
            color: #ff5555;
            font-weight: bold;
        }

        .sidebar ul li a.logout-link:hover {
            background-color: rgba(255, 85, 85, 0.1);
            color: #fff;
        }

        .container {
            margin-left: 240px;
            padding: 40px;
            text-align: center;
            background-color: #fff;
            flex-grow: 1;
            box-sizing: border-box;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
            font-size: 2em;
        }

        .cart-list {
            list-style: none;
            padding: 0;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-details {
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

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .quantity-btn:hover {
            background-color: #0056b3;
        }

        .quantity-span {
            width: 30px;
            text-align: center;
            font-size: 1em;
            color: #333;
        }

        .remove-from-cart {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .remove-from-cart:hover {
            background-color: #c82333;
        }

        .cart-actions {
            margin-top: 30px;
            text-align: center;
        }

        .cancel-action, .pay-action {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s ease;
            font-size: 1em;
            display: inline-block;
            margin: 0 10px;
        }

        .cancel-action {
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .cancel-action:hover {
            opacity: 0.8;
        }

        .pay-action {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .pay-action:hover {
            opacity: 0.8;
        }

        .empty-cart-message {
            text-align: center;
            font-style: italic;
            color: #777;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 20px;
            }

            h1 {
                font-size: 1.8em;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Your Cart</h1>
        <?php if ($is_cart_empty): ?>
            <p class="empty-cart-message">Your cart is currently empty.</p>
        <?php else: ?>
            <ul class="cart-list">
                <?php foreach ($cart_items as $item) { ?>
                    <li class="cart-item">
                        <div class="item-details">
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                            <span>$<?php echo number_format($item['price'], 2); ?></span>
                        </div>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">−</button>
                            <span class="quantity-span" id="quantity-<?php echo $item['product_id']; ?>"><?php echo $item['quantity']; ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                        </div>
                        <button class="remove-from-cart" onclick="window.location.href='cart.php?action=remove&id=<?php echo $item['product_id']; ?>'">Cancel</button>
                    </li>
                <?php } ?>
            </ul>

            <div class="cart-actions">
                <button class="cancel-action" onclick="window.location.href='categories.php'">Continue Shopping</button>
                <button class="pay-action" onclick="window.location.href='pay.php'">Order</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQuantity(productId, change) {
            let quantitySpan = document.getElementById("quantity-" + productId);
            let newQuantity = parseInt(quantitySpan.innerText) + change;
            if (newQuantity < 1) newQuantity = 1;

            window.location.href = "cart.php?action=add&id=" + productId + "&quantity=" + change;
        }
    </script>
<?php
$mysqli->close();
?>
</body>
</html>
