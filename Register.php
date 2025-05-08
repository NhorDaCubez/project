<?php
session_start();
include "db_connect.php";

function validate($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = validate($_POST["firstname"]);
    $lastname = validate($_POST["lastname"]);
    $email = validate($_POST["email"]);
    $birthdate = validate($_POST["birthdate"]);
    $gender = validate($_POST["gender"]);
    $contact = validate($_POST["contact"]);
    $country = validate($_POST["country"]);
    $username = validate($_POST["username"]);
    $password = validate($_POST["password"]);
    $repeat_password = validate($_POST["repeat-password"]);
    $admin_code = isset($_POST["admin_code"]) ? trim($_POST["admin_code"]) : "";

    $secret_admin_code = "is_admin";
    $is_admin = ($admin_code === $secret_admin_code) ? 1 : 0;

    if (empty($firstname) || empty($lastname) || empty($email) || empty($birthdate) || empty($gender) ||
        empty($contact) || empty($country) || empty($username) || empty($password) || empty($repeat_password)) {
        header("Location: register.php?error=All fields are required");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=Invalid email format");
        exit();
    }

    if ($password !== $repeat_password) {
        header("Location: register.php?error=Passwords do not match");
        exit();
    }

    // Check for existing email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=Email already registered");
        exit();
    }
    $stmt->close();

    // Check username
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=Username already exists");
        exit();
    }
    $stmt->close();

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, birthdate, gender, contact, country, user_name, password, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $firstname, $lastname, $email, $birthdate, $gender, $contact, $country, $username, $password, $is_admin);

    if ($stmt->execute()) {
        header("Location: login.php?success=Account created successfully! Please log in.");
    } else {
        header("Location: register.php?error=Registration failed. Try again.");
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Foodie Express</title>
    <link rel="stylesheet" href="Register.css">
</head>
<body>
    <div class="container">
        <div class="register-section">
            <form id="register-form" method="POST" action="">
                <h2>Create Account</h2>

                <?php if (isset($_GET['error'])) { ?>
                    <p class="error"><?php echo $_GET['error']; ?></p>
                <?php } ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" id="lastname" name="lastname" required>
                    </div>
                </div>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <div class="form-row">
                    <div class="form-group">
                        <label for="birthdate">Birthdate:</label>
                        <input type="date" id="birthdate" name="birthdate" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="">-</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact">Contact Number:</label>
                        <input type="tel" id="contact" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" id="country" name="country" required>
                    </div>
                </div>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="repeat-password">Repeat Password:</label>
                        <input type="password" id="repeat-password" name="repeat-password" required>
                    </div>
                </div>

                  <!-- Admin Code Field (Optional) -->
                  <label for="admin_code">Admin Code (Optional):</label>
                <input type="text" id="admin_code" name="admin_code">

                <button type="submit" class="register-btn">Register</button>
            </form>
        </div>

        <div class="branding-section">
            <img class="logo" src="Nhormes.jpg" alt="Foodie Express Logo">
            <p class="login-link">Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>
</body>
</html>
