<?php
session_start();
include 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .admin-container {
            margin-left: 280px; /* Adjust to match sidebar width + spacing */
            padding: 30px;
            background-color: #f8f9fa; /* Light background for content */
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        h1 {
            color: #343a40;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        p {
            color: #495057;
            font-size: 1rem;
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="food_management.php">Food Management</a></li>
            <li><a href="archived_products.php">Archived Products</a></li>
            <li><a href="tracking_admin.php">Order</a></li>
            <li><a href="transactionhistory_admin.php">Transaction Histories</a></li> <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

    <div class="admin-container">
        <h1>Welcome, Admin</h1>
        <p>This is your administrative dashboard. From here, you can manage food items, view transaction histories, and control archived products.</p>
    </div>
</body>
</html>