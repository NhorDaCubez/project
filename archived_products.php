<?php
session_start();
include 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// Handle product restore action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restore'])) {
    $id = $_POST['id'];
    $stmt = $mysqli->prepare("UPDATE products SET archived = 0 WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
     if ($stmt->error) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();
}

// Fetch all archived products
$result = $mysqli->query("SELECT * FROM products WHERE archived = 1");
if ($result === false) {
    die("Error executing query: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            border-bottom: 2px solid #6c757d; /* Muted border color */
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #dee2e6;
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background-color: #6c757d; /* Muted header color */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .restore-btn {
            background-color: #28a745; /* Green for restore */
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .restore-btn:hover {
            background-color: #218838;
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
            <li><a href="transactionhistory_admin.php">Transaction Histories</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

    <div class="admin-container">
        <h1>Archived Products</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="restore" class="btn btn-sm btn-success restore-btn">Restore</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-3">No products are currently archived.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php
$result->free_result();
$mysqli->close();
?>
</body>
</html>
