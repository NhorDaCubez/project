<?php
session_start();
include 'db_connect.php';

// Check for session variables. Redirect to index.php with error if not set.
if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php?error=session_expired");
    exit();
}

$user_id = $_SESSION['id'];

// SQL query to fetch order information for the logged-in user, retrieving only the latest tracking status.
$sql = "SELECT 
            h.order_number,
            p.name AS product_name,
            h.quantity,
            h.order_date,
            ot.status AS tracking_status,
            ot.updated_at AS tracking_updated_at
        FROM history h
        JOIN products p ON h.product_id = p.id
        LEFT JOIN (
            SELECT 
                order_number,
                status,
                updated_at,
                ROW_NUMBER() OVER(PARTITION BY order_number ORDER BY updated_at DESC) AS rn
            FROM order_tracking
        ) ot ON h.order_number = ot.order_number AND ot.rn = 1  
        WHERE h.user_id = ?
        ORDER BY h.order_date DESC, ot.updated_at DESC";

// Prepare the SQL statement. Handle potential errors during preparation.
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    die("Error preparing SQL statement: " . $mysqli->error);
}

// Bind the user ID parameter.
$stmt->bind_param("i", $user_id);

// Execute the query. Handle potential errors during execution.
if (!$stmt->execute()) {
    die("Error executing SQL statement: " . $stmt->error);
}

// Get the result set.
$result = $stmt->get_result();
if ($result === false) {
    die("Error getting result set: " . $mysqli->error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Order Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Light background */
            color: #1f2937; /* Darker text for better readability */
            margin: 0;
            padding: 0;
        }
        h2 {
            margin-bottom: 24px;
            color: #4b5563; /* Slightly darker heading */
            text-align: center;
        }
        .container {
            max-width: 1000px; /* Increased max-width */
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px; /* Slightly more rounded corners */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* More subtle shadow */
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            border-radius: 10px; /* Rounded corners for table */
            overflow: hidden; /* Needed for rounded corners on table */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1); /* Shadow for table */
        }
        th, td {
            padding: 16px; /* Increased padding */
            border-bottom: 1px solid #e5e7eb; /* Lighter border */
            text-align: left;
        }
        th {
            background-color: #f9fafb; /* Very light background for header */
            color: #6b7280; /* Muted header text color */
            font-weight: 500; /* Medium font weight for header */
        }
        td {
            color: #374151; /* Slightly darker body text */
        }
        tr:nth-child(even) {
            background-color: #f7fafc; /* Even row background */
        }
        tr:hover {
            background-color: #edf2f7; /* Slightly lighter hover background */
            transition: background-color 0.2s ease; /* Smooth transition */
        }
        .back-button {
            margin-top: 24px; /* Increased margin */
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            background-color: #4CAF50; /* Green back button */
            color: white;
            border: none;
            border-radius: 6px; /* Rounded corners for button */
            cursor: pointer;
            font-weight: 500; /* Medium font weight for button */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08); /* Subtle shadow for button */
            transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transition */
        }
        .back-button:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: translateY(-1px); /* Slight lift on hover */
        }
        .back-button svg {
            margin-right: 8px; /* Space between icon and text */
            width: 16px; /* Icon size */
            height: 16px;
            stroke: white; /* Icon color */
            stroke-width: 2;
            fill: none;
            vertical-align: middle; /* Vertically align icon with text */
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Order Tracking</h2>
        <a href="home.php" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"></path>
                <path d="M12 19l-7-7 7-7"></path>
            </svg>
            Back to Home
        </a>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Tracking Status</th>
                        <th>Tracking Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['tracking_status'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['tracking_updated_at'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="mt-4 text-gray-500">No orders found.</p> <?php endif; ?>
    </div>
</body>
</html>
<?php
// Close the statement and connection. Added check for $stmt.
if (isset($stmt)) {
    $stmt->close();
}
if (isset($mysqli)) {
    $mysqli->close();
}
?>
