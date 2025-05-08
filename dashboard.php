<?php
session_start();
include "db_connect.php"; // This already connects to your DB

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

try {
    // If db_connect.php already provides $pdo, no need to reconnect.
    if (!isset($pdo)) {
        throw new Exception("Database connection not established.");
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Set the default timezone to your local time (important for PHP's date/time functions)
date_default_timezone_set('Asia/Manila');

// Fetch total users
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Debugging for Recent Activities
$recentActivitiesQuery = "
    SELECT users.user_name, activity_log.activity, activity_log.timestamp
    FROM activity_log
    JOIN users ON users.id = activity_log.user_id
    ORDER BY activity_log.timestamp DESC
    LIMIT 10
";

// Fetch the recent activities
$recentActivities = $pdo->query($recentActivitiesQuery)->fetchAll(PDO::FETCH_ASSOC);

if (!$recentActivities) {
    $recentActivitiesMessage = "No recent activities found.";
} else {
    $recentActivitiesMessage = "Recent activities found.";
}

// Debugging for Active Users Today - TRYING TIMEZONE CONVERSION
$activeUsersTodayQuery = "
    SELECT COUNT(DISTINCT user_id)
    FROM activity_log
    WHERE DATE(CONVERT_TZ(timestamp, '+00:00', '+08:00')) = CURDATE()
";
$totalActivitiesToday = $pdo->query($activeUsersTodayQuery)->fetchColumn();

if (!$totalActivitiesToday) {
    $activeUsersTodayMessage = "No active users today.";
} else {
    $activeUsersTodayMessage = "Active users today: " . $totalActivitiesToday;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css"> </head>
<body>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 bg-dark sidebar">
                <h2 class="text-white text-center py-3">Admin Panel</h2>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="admin.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="food_management.php">Food Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="archived_products.php">Archived Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="tracking_admin.php">Order</a></li>
                    <li class="nav-item"><a class="nav-link" href="transactionhistory_admin.php">Transaction Histories</a></li>
                    <li class="nav-item"><a class="nav-link logout-btn" href="logout.php">Logout</a></li>
                </ul>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Website Dashboard</h1>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text"><?php echo $totalUsers; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Active Users Today</h5>
                                <p class="card-text"><?php echo $activeUsersTodayMessage; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Recent User Activities</h5>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Activity</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentActivities as $activity): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($activity['user_name']); ?></td>
                                                <td><?php echo htmlspecialchars($activity['activity']); ?></td>
                                                <td><?php echo htmlspecialchars($activity['timestamp']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>