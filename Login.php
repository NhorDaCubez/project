<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $mysqli->prepare("SELECT id, password, user_name, is_admin FROM users WHERE user_name = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $mysqli->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Error getting result: " . $mysqli->error);
    }

    if ($row = $result->fetch_assoc()) {
        // Directly compare passwords (⚠️ Not secure, but per your request)
        if ($password === $row['password']) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['is_admin'] = $row['is_admin']; // Store admin status in session

            // --- START OF ACTIVITY LOGGING ---
            $user_id = $_SESSION['id'];
            $activity = "Logged in";
            $timestamp = date("Y-m-d H:i:s");

            $logStmt = $mysqli->prepare("INSERT INTO activity_log (user_id, activity, timestamp) VALUES (?, ?, ?)");
            if ($logStmt === false) {
                die("Error preparing statement: " . $mysqli->error);
            }
            $logStmt->bind_param("iss", $user_id, $activity, $timestamp);
            $logStmt->execute();
            $logStmt->close();
            // --- END OF ACTIVITY LOGGING ---

            // Restore cart items from database
            $stmt = $mysqli->prepare("SELECT product_id, quantity FROM carts WHERE user_id = ?");
             if ($stmt === false) {
                die("Error preparing statement: " . $mysqli->error);
            }
            $stmt->bind_param("i", $_SESSION['id']);
            $stmt->execute();
            $result = $stmt->get_result();
             if ($result === false) {
                die("Error getting result: " . $mysqli->error);
            }

            $_SESSION['cart'] = [];
            while ($cartItem = $result->fetch_assoc()) {
                $_SESSION['cart'][$cartItem['product_id']] = $cartItem['quantity'];
            }
            $stmt->close();

            // Redirect based on role
            if ($_SESSION['is_admin'] == 1) {
                header("Location: admin.php"); // Admins go to admin panel
            } else {
                header("Location: home.php"); // Regular users go to categories
            }
            exit();
        } else {
            header("Location: index.php?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: index.php?error=invalid_credentials");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Foodie Express</title>
    <link rel="stylesheet" href="Login.css">
</head>
<body>
    <div class="container">
        <div class="login-section">
            <form id="login-form" method="POST" action="">
            <h2><a href="index.html" class="btn">Back</a></h2>
                <h2>Login</h2>

                <?php if (isset($_GET['error'])) { ?>
                    <p class="error"><?php echo $_GET['error']; ?></p>
                <?php } ?>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <br>
                <button type="submit" class="login-btn"><b>Log-In</b></button>
                <p class="register-text">Don't have an account? <em><a href="Register.php">Register</a></em></p>

            </form>
        </div>

        <div class="branding-section">
            <img class="logo" src="Nhormes.jpg" alt="Foodie Express Logo">
        </div>
    </div>
<?php
if(isset($mysqli)){
    $mysqli->close();
}
?>
</body>
</html>
