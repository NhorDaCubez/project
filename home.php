<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['user_name'])) {
    header("Location: index.php?error=session_expired");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HOME</title>
    <link rel="stylesheet" type="text/css" href="home.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex; /* For sidebar layout */
            min-height: 100vh; /* Ensure full viewport height */
        }

        /* Sidebar Styles (Assuming sidebar.php creates an element with class 'sidebar') */
        .sidebar {
            width: 220px;
            background-color: #333;
            color: white;
            padding: 20px;
            position: fixed; /* Keep sidebar fixed */
            top: 0;
            left: 0;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto; /* In case sidebar content is long */
        }

        .sidebar h2 {
            margin-bottom: 20px;
            color: #eee;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #00ff00; /* Or a more subtle highlight color */
        }

        /* Main Content Area */
        .container {
            margin-left: 240px; /* Adjust to sidebar width */
            padding: 40px;
            text-align: left;
            background-color: #fff; /* Optional: Background for the main content */
            flex-grow: 1; /* Take remaining width */
            box-sizing: border-box;
        }

        .container h1 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .container p {
            color: #555;
            line-height: 1.7;
        }

        /* Hero Section */
        .hero {
            padding: 80px 40px;
            background-image: url('path/to/your/hero-image.jpg'); /* Add your image path */
            background-size: cover;
            background-position: center;
            color: black;
            text-align: center;
            margin-left: 240px; /* Adjust to sidebar width */
            box-sizing: border-box;
        }

        .hero h1 {
            color: black;
            font-size: 2.5em;
            margin-bottom: 20px;
            border-bottom: none;
        }

        .hero p {
            font-size: 1.2em;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .hero .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #ff6b6b; /* Example button color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .hero .btn:hover {
            background-color: #e45757;
        }

        /* Responsive Design (Basic) */
        @media (max-width: 768px) {
            body {
                flex-direction: column; /* Stack sidebar and content */
            }

            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                margin-bottom: 20px;
                box-shadow: none;
            }

            .container, .hero {
                margin-left: 0;
                padding: 20px;
            }

            .hero h1 {
                font-size: 2em;
            }

            .hero p {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1> <p>Explore our delicious menu and order your favorite meals quickly and easily.</p>
    </div>

    <section class="hero">
        <h1>Delicious Food, Delivered Fast!</h1>
        <p>Satisfy your cravings with our wide selection of mouth-watering dishes. Order now and enjoy a delightful meal at your doorstep.</p>
        <a href="menu.php" class="btn">Order Now</a>
    </section>

</body>
</html>