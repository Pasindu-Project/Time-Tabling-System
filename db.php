<?php
// Database connection settings
$servername = "localhost";
$db_username = "root"; // Replace with your database username
$db_password = ""; // Replace with your database password
$dbname = "test"; // Replace with your database name

// Turn off MySQLi error reporting (we're handling errors manually)
mysqli_report(MYSQLI_REPORT_OFF);

try {
    // Attempt connection using mysqli with error handling
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        // Redirect to newdatabase.php if connection fails (database might not exist)
        header("Location: newdatabase.php?dbname=" . urlencode($dbname));
        echo json_encode(["clash" => false]);
        exit();
    }
} catch (mysqli_sql_exception $e) {
    // In case an exception is thrown, redirect to newdatabase.php
    header("Location: newdatabase.php?dbname=" . urlencode($dbname));
    exit();
}

?>
