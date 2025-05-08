<?php
session_start();
include 'db_connect.php';

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];

    // Get order_number for tracking log
    $stmt = $mysqli->prepare("SELECT order_number FROM history WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($order_number);
    $stmt->fetch();
    $stmt->close();

    // 1. Update history table
    $stmt = $mysqli->prepare("UPDATE history SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();

    // 2. Insert into order_tracking table
    // Assuming no need for admin_id anymore, if it's not required
    $remarks = null;  // Optionally add remarks here

    $stmt = $mysqli->prepare("INSERT INTO order_tracking (order_number, status, updated_by, remarks) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $order_number, $new_status, $remarks, $remarks); // Since no admin_id, pass null for updated_by
    $stmt->execute();
    $stmt->close();
}

// Fetch all orders
$result = $mysqli->query("SELECT h.id, h.order_number, h.user_id, h.product_id, h.quantity, h.order_date, h.order_status, p.name AS product_name
    FROM history h
    JOIN products p ON h.product_id = p.id
    ORDER BY h.order_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Tracking - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        h2 {
            margin-bottom: 24px;
            color: #4b5563;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
        }
        th {
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: 500;
        }
        td {
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        tr:hover {
            background-color: #edf2f7;
            transition: background-color 0.2s ease;
        }
        form {
            margin: 0;
            display: inline-block;
        }
        select {
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background-color: #fff;
            color: #374151;
            font-size: 14px;
            transition: border-color 0.2s ease;
            width: 100%;
            max-width: 200px;
        }
        select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }
        input[type="submit"] {
            padding: 10px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 8px;
            width: 100%;
            max-width: 120px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
            transform: translateY(-1px);
        }
        .status-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: 500;
            color: white;
            text-align: center;
            min-width: 100px;
        }
        .status-pending {
            background-color: #f59e0b;
        }
        .status-delivering {
            background-color: #3b82f6;
        }
        .status-delivered {
            background-color: #16a34a;
        }
        .back-button {
            margin-top: 24px;
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #45a049;
            transform: translateY(-1px);
        }
        .back-button svg {
            margin-right: 8px;
            width: 16px;
            height: 16px;
            stroke: white;
            stroke-width: 2;
            fill: none;
            vertical-align: middle;
        }
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 10px;
            }
            table {
                display: block;
                overflow-x: auto;
            }
            form {
                display: block;
                margin-top: 10px;
            }
            select, input[type="submit"]{
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Tracking Panel</h2>
        <a href="dashboard.php" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>User ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['order_number']) ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['order_date'] ?></td>
                    <td>
                        <span class="status-badge <?= $row['order_status'] == 'Pending' ? 'status-pending' : ($row['order_status'] == 'Delivering' ? 'status-delivering' : 'status-delivered') ?>">
                            <?= htmlspecialchars($row['order_status']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <select name="order_status">
                                <option value="Pending" <?= $row['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Delivering" <?= $row['order_status'] == 'Delivering' ? 'selected' : '' ?>>Delivering</option>
                                <option value="Delivered" <?= $row['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                            </select>
                            <input type="submit" name="update_status" value="Update">
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $mysqli->close(); ?>
