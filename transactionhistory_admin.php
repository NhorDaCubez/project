<?php
session_start();
include 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// Fetch all users (using 'user_name' instead of 'email')
// Use $mysqli here, as that's the connection variable from db_connect.php
$stmt = $mysqli->prepare("SELECT id, user_name FROM users");  // Changed $conn to $mysqli
$stmt->execute();
$users_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Transaction History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        h1, h2, h3 {
            color: #343a40;
        }

        h1 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h2 {
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .user-list {
            list-style-type: none;
            padding: 0;
        }

        .user-item {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .user-item a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s ease;
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #007bff;
        }

        .user-item a:hover {
            background-color: #007bff;
            color: white;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ced4da;
            background-color: #fff;
        }

        .transaction-table th, .transaction-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .transaction-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .transaction-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .back-button {
            margin-top: 30px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1> Transaction History</h1>

        <h2>Select a user to view their transaction history:</h2>
        <ul class="user-list">
            <?php while ($user = $users_result->fetch_assoc()): ?>
                <li class="user-item">
                    <a href="transactionhistory_admin.php?user_id=<?php echo $user['id']; ?>">User: <?php echo htmlspecialchars($user['user_name']); ?></a>
                </li>
            <?php endwhile; ?>
        </ul>

        <?php
        // If a user ID is selected, show their transaction history
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];

            // Fetch the user's transaction history
            // Use $mysqli here
            $stmt = $mysqli->prepare("SELECT h.*, p.name, p.price FROM history h JOIN products p ON h.product_id = p.id WHERE h.user_id = ? ORDER BY h.order_date DESC"); // Changed $conn to $mysqli
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Display the transaction history header
            echo "<h3>Transaction History for User #{$user_id}</h3>";

            // Check if there are transactions and display them in a table
            if ($result->num_rows > 0) { ?>
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Order Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>₱<?php echo number_format($row['price'], 2); ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo date("F j, Y, g:i a", strtotime($row['order_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="mt-3">No transactions found for this user.</p>
            <?php }
        }
        ?>

        <a class="back-button" href="admin.php"> Back to Admin Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
