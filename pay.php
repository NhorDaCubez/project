<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php?error=not_logged_in");
    exit();
}

$user_id = $_SESSION['id'];

// fetch cart items
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
$total = 0;
$delivery_fee = 50.00;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$stmt->close();

// if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php?empty=true");
    exit();
}

// generate unique order number (e.g., USERID + timestamp)
$order_number = "ORD-" . $user_id . "-" . time();
$grand_total = $total + $delivery_fee;

// insert into history
$order_status = 'Pending'; // default status
foreach ($cart_items as $item) {
    $product_id = $item['product_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $total_price = $price * $quantity;

    $stmt = $mysqli->prepare("INSERT INTO history (order_number, user_id, product_id, quantity, price, total, order_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("siiidds", $order_number, $user_id, $product_id, $quantity, $price, $total_price, $order_status);
    $stmt->execute();
    $stmt->close(); // Close the statement inside the loop
}

// ✅ insert into order_tracking table
$track_status = 'Pending';
$stmt = $mysqli->prepare("INSERT INTO order_tracking (order_number, status) VALUES (?, ?)");
if ($stmt === false) {
    die("Error preparing statement: " . $mysqli->error);
}
$stmt->bind_param("ss", $order_number, $track_status);
$stmt->execute();
$stmt->close();

// clear the cart
$stmt = $mysqli->prepare("DELETE FROM carts WHERE user_id = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $mysqli->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();
$mysqli->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Foodie Express - Your Bill</title>
    <link rel="stylesheet" href="pay.css">
</head>
<body>
<div class="bill-container">
    <h1>🍔 Foodie Express - Order Summary</h1>
    <p><strong>Order Number:</strong> <?= $order_number ?></p>
    <table>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($cart_items as $item):
            $subtotal = $item['price'] * $item['quantity'];
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($subtotal, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td colspan="3">Subtotal</td>
            <td>₱<?= number_format($total, 2) ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="3">Delivery Fee</td>
            <td>₱<?= number_format($delivery_fee, 2) ?></td>
        </tr>
        <tr class="grand-total">
            <td colspan="3">Grand Total</td>
            <td>₱<?= number_format($grand_total, 2) ?></td>
        </tr>
    </table>
    <div class="thank-you">
        <h2>Thank you for ordering with Foodie Express! 🚀</h2>
        <p>Your food will arrive shortly. Enjoy your meal!</p>
    </div>
    <div class="continue-ordering">
        <a href="menu.php" class="continue-btn">Continue Ordering</a>
    </div>
</div>
</body>
</html>
