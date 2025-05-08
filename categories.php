<!DOCTYPE html>
<html>
<head>
    <title>Food Categories</title>
    <link rel="stylesheet" href="categories.css">
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
            box-sizing: border-box;
        }

        .sidebar .logo {
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar h2 {
            margin: 0;
            font-size: 1.5em;
            color: #eee;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #fff;
            font-size: 1em;
            transition: background-color 0.2s ease;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #00ff00; /* Or a more subtle highlight */
        }

        .sidebar ul li a .icon {
            margin-right: 8px; /* Space between icon and text */
            font-size: 1.2em; /* Adjust icon size */
            vertical-align: middle; /* Align icon with text */
        }

        .sidebar ul li a.logout-link {
            color: #ff5555; /* Keep the red color for logout */
            font-weight: bold; /* Make it stand out */
        }

        .sidebar ul li a.logout-link:hover {
            background-color: rgba(255, 85, 85, 0.1); /* Slightly lighter red on hover */
            color: #fff; /* Change text color on hover if desired */
        }

        /* Main Content Area */
        .container {
            margin-left: 240px; /* Adjust to sidebar width */
            padding: 40px;
            text-align: center; /* Center category list */
            background-color: #fff; /* Optional: Background for the main content */
            flex-grow: 1; /* Take remaining width */
            box-sizing: border-box;
        }

        .container h1 {
            color: #333;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
            font-size: 2em;
        }

        .category-list {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Responsive grid */
            gap: 20px;
            margin-top: 20px;
        }

        .category-list li a {
            display: block;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 1.1em;
            font-weight: bold;
            text-align: center;
        }

        .category-list li a:hover {
            background-color: #eee;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Responsive adjustments for main content */
        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 20px;
            }

            .container h1 {
                font-size: 1.8em;
                margin-bottom: 20px;
            }

            .category-list {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
            }

            .category-list li a {
                padding: 15px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?> <div class="container">
        <h1>Select a Food Category</h1>
        <ul class="category-list">
            <li><a href="menu.php?category=Entrees">🍖 Entrees</a></li>
            <li><a href="menu.php?category=Sides">🍟 Sides</a></li>
            <li><a href="menu.php?category=Desserts">🍰 Desserts</a></li>
            <li><a href="menu.php?category=Beverages">🥤 Beverages</a></li>
            <li><a href="menu.php?category=Specials">🔥 Specials</a></li>
            <li><a href="menu.php?category=Kids Menu">👶 Kids Menu</a></li>
        </ul>
    </div>
</body>
</html>