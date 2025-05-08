<?php
session_start();
include 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: home.php");
    exit();
}

// Fetch categories for dropdown
$categoryQuery = $mysqli->query("SELECT DISTINCT category FROM products");
if ($categoryQuery === false) {
    die("Error executing query: " . $mysqli->error);
}
$categories = [];
while ($row = $categoryQuery->fetch_assoc()) {
    $categories[] = $row['category'];
}
$categoryQuery->free_result();

// Handle form actions (Create, Update, Archive)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $stmt = $mysqli->prepare("INSERT INTO products (name, price, category, archived) VALUES (?, ?, ?, 0)");
        if ($stmt === false) {
            die("Error preparing statement: " . $mysqli->error);
        }
        $stmt->bind_param("sds", $name, $price, $category);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $stmt = $mysqli->prepare("UPDATE products SET name=?, price=?, category=? WHERE id=?");
        if ($stmt === false) {
            die("Error preparing statement: " . $mysqli->error);
        }
        $stmt->bind_param("sdsi", $name, $price, $category, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['archive'])) {
        $id = $_POST['id'];
        $stmt = $mysqli->prepare("UPDATE products SET archived = 1 WHERE id=?");
        if ($stmt === false) {
            die("Error preparing statement: " . $mysqli->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all non-archived products
$sql = "SELECT * FROM products WHERE archived = 0";

// Apply filter
$filterCategory = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';
if ($filterCategory) {
    $sql .= " AND category = ?";
}

// Apply search
$searchKeyword = isset($_GET['search_keyword']) ? $_GET['search_keyword'] : '';
if ($searchKeyword) {
    $sql .= " AND name LIKE ?";
}

$sql .= " ORDER BY name ASC"; // Added Order by Name

$stmt = $mysqli->prepare($sql);

if ($filterCategory && $searchKeyword) {
    $searchKeywordParam = "%" . $searchKeyword . "%";
    $stmt->bind_param("ss", $filterCategory, $searchKeywordParam);
} elseif ($filterCategory) {
    $stmt->bind_param("s", $filterCategory);
} elseif ($searchKeyword) {
    $searchKeywordParam = "%" . $searchKeyword . "%";
    $stmt->bind_param("s", $searchKeywordParam);
}


$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Error executing query: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .form-create {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid #dee2e6;
        }

        .form-create h3 {
            color: #343a40;
            margin-bottom: 20px;
        }

        .form-create input[type="text"],
        .form-create input[type="number"],
        .form-create select {
            width: calc(33% - 10px);
            padding: 10px;
            margin: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-create button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .form-create button:hover {
            background-color: #0056b3;
        }

        h2 {
            color: #343a40;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
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
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .inline-form {
            display: inline;
            margin-right: 10px;
        }

        .inline-form input[type="text"],
        .inline-form input[type="number"],
        .inline-form select {
            width: 120px;
            padding: 8px;
            margin-right: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .archive-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .archive-btn:hover {
            background-color: #c82333;
        }

        .filter-search-form {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-search-form select,
        .filter-search-form input[type="text"],
        .filter-search-form button {
            margin: 5px;
        }

        @media (max-width: 768px) {
            .filter-search-form {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-search-form select,
            .filter-search-form input[type="text"],
            .filter-search-form button {
                width: 100%;
                margin: 5px 0;
            }
            .form-create input[type="text"],
            .form-create input[type="number"],
            .form-create select{
                width: 100%;
                margin: 5px 0;
            }
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
        <h1>Food Management</h1>

        <form method="POST" class="form-create">
            <h3>Add New Food Item</h3>
            <input type="text" name="name" placeholder="Food Name" required class="form-control">
            <input type="number" step="0.01" name="price" placeholder="Price" required class="form-control">
            <select name="category" required class="form-control">
                <option value="" disabled selected>Select Category</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="create" class="btn btn-primary">Create</button>
        </form>

        <form method="GET" class="filter-search-form">
            <select name="filter_category" class="form-control">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo htmlspecialchars($category); ?>" <?php if (isset($_GET['filter_category']) && $_GET['filter_category'] == $category) echo "selected"; ?>>
                        <?php echo htmlspecialchars($category); ?>
                    </option>
                <?php } ?>
            </select>
            <input type="text" name="search_keyword" placeholder="Search by Name" value="<?php if (isset($_GET['search_keyword'])) echo htmlspecialchars($_GET['search_keyword']); ?>" class="form-control">
            <button type="submit" class="btn btn-primary">Filter/Search</button>
        </form>

        <h2>Food List</h2>
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
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required class="form-control form-control-sm">
                                <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required class="form-control form-control-sm">
                                <select name="category" required class="form-control form-control-sm">
                                    <?php foreach ($categories as $category) { ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>" <?php if ($row['category'] == $category) echo "selected"; ?>>
                                            <?php echo htmlspecialchars($category); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <button type="submit" name="update" class="btn btn-sm btn-success">Update</button>
                            </form>
                            <form method="POST" class="inline-form">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="archive" class="btn btn-sm btn-danger archive-btn">Archive</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php
$result->free_result();
$stmt->close();
$mysqli->close();
?>
</body>
</html>
