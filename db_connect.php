<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "it9_project";

// Create mysqli connection
$mysqli = new mysqli($servername, $username, $password, $dbname); // Changed $conn to $mysqli

// Check mysqli connection
if ($mysqli->connect_error) { // Changed $conn to $mysqli
    die("Connection failed: " . $mysqli->connect_error);
}
//set the connection character to utf8.
$mysqli->set_charset("utf8");

// Create PDO connection
try {
    // Use $servername for PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
