<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

$stmt = $conn->prepare("SELECT h.*, p.name, p.price FROM history h JOIN products p ON h.product_id = p.id WHERE h.user_id = ? ORDER BY h.order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction History</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4C3B3B;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

       
        .cart-list {
            color: white;

        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📜 Your Transaction History</h1>
        <?php if ($result->num_rows > 0): ?>
            <ul class="cart-list">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <li class="cart-item">
                        <span><?php echo htmlspecialchars($row['name']) . " - ₱" . number_format($row['price'], 2); ?></span><br>
                        <span>Quantity: <?php echo $row['quantity']; ?></span><br>
                        <span>Date: <?php echo date("F j, Y, g:i a", strtotime($row['order_date'])); ?></span>
                    </li>
                <?php } ?>
            </ul>
        <?php else: ?>
            <p>No transactions yet.</p>
        <?php endif; ?>

        <a class="back-button" href="home.php">🏠 Back to Home</a>
    </div>
</body>
</html>
