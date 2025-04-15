<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    // Redirect to the login page if not logged in
    header("Location: science.php");
    exit();
}

$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Student';
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'user@gmail.com';


include 'db.php'; // or require 'db.php';

// Database connection settings
/*$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "test"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}*/

// Fetch user details from the database
$sql = "SELECT registration_num, name, combination, email, year FROM all_students WHERE email = ?";
$stmt = $conn->prepare($sql);
$user_details = [
    'registration_num' => 'Not Available',
    'name' => $user_name,
    'combination' => 'Not Available',
    'email' => $user_email,
    'year' => 'Not Available' // Initialize with a default value
];

if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($registration_num, $name, $combination, $email, $year);

    if ($stmt->fetch()) {
        $user_details = [
            'registration_num' => htmlspecialchars($registration_num),
            'name' => htmlspecialchars($name),
            'combination' => htmlspecialchars($combination),
            'email' => htmlspecialchars($email),
            'year' => htmlspecialchars($year) // Assign the fetched year
        ];
    }
    $stmt->close();
} else {
    die("Database query failed.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .profile-card {
            width: 100%;
            max-width: 500px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .profile-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .profile-body {
            padding: 20px;
        }
        .profile-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .profile-item:last-child {
            border-bottom: none;
        }
        .profile-item label {
            font-weight: bold;
            color: #333;
        }
        .profile-item span {
            color: #555;
        }
        .button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }
        .button:hover {
            background: #5643cc;
        }
    </style>
</head>
<body>
    <div class="profile-card">
        <div class="profile-header">
            <h1><?php echo $user_details['name']; ?></h1>
            <p>Welcome to Your Profile</p>
        </div>
        <div class="profile-body">
            <div class="profile-item">
                <label>Registration Number:</label>
                <span><?php echo $user_details['registration_num']; ?></span>
            </div>
            <div class="profile-item">
                <label>Combination:</label>
                <span><?php echo $user_details['combination']; ?></span>
            </div>
            <div class="profile-item">
                <label>Email:</label>
                <span><?php echo $user_details['email']; ?></span>
            </div>
            <div class="profile-item">
                <label>Year:</label>
                <span><?php echo $user_details['year']; ?></span>
            </div>
        </div>
        <a href="home.php" class="button">Back to Home</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
