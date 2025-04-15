<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

// Get the user's name
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Dean';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Dean Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 100px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #6c63ff;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 1.5rem;
        }
        .btn-back-home {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #6c63ff;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-back-home:hover {
            background-color: #574b90;
            text-decoration: none;
        }
        .btn-back-home i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <!-- Back to Home Button -->
    <a href="main_table.php" class="btn-back-home">
        <i class="bi bi-arrow-left-circle-fill"></i> Back to Home
    </a>

    <div class="container">
        <div class="card">
            <div class="card-header">
                About the Dean Dashboard
            </div>
            <div class="card-body">
                <h5 class="card-title">Welcome to the Dean Dashboard</h5>
                <p class="card-text">
                    The Dean Dashboard is an intuitive web-based platform designed to streamline course and timetable management for deans and academic administrators.
                    It offers powerful tools to efficiently manage course scheduling, lecture halls, and assignments.
                </p>
                <h6>Key Features:</h6>
                <ul>
                    <li><strong>Course Management:</strong> Add, update, and organize courses across academic years and departments.</li>
                    <li><strong>Timetable Scheduling:</strong> Drag and drop courses into a visual timetable grid.</li>
                    <li><strong>Hall Assignment:</strong> Allocate lecture halls based on course size and availability.</li>
                    <li><strong>Real-time Updates:</strong> Refresh course lists and update hall assignments dynamically.</li>
                    <li><strong>Session Management:</strong> Secure access with session handling and user authentication.</li>
                </ul>
                <h6>How to Use:</h6>
                <ol>
                    <li>Select the academic year and department to view relevant courses.</li>
                    <li>Assign courses to specific time slots and lecture halls using drag-and-drop.</li>
                    <li>Use the "Refresh List" to update course availability in real-time.</li>
                    <li>Click "Save Timetable" to store your assignments in the system.</li>
                </ol>
                <p>
                    For any technical issues or feature requests, please contact the IT support team.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
