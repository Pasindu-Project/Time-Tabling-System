<?php
// lecturer_about.php - About Page for Lecturer Timetabling System
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Lecturer Timetabling System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #6c6acd;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">University of Colombo</a>
            <div class="d-flex">
                <a href="department.php" class="btn btn-outline-light me-2">
                    <i class="bi bi-house-door"></i> Back to Home
                </a>
                <!-- Removed Contact Us button -->
            </div>
        </div>
    </nav>

    <!-- About Section -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>About the Lecturer Timetabling System</h3>
                    </div>
                    <div class="card-body">
                        <p>
                            The <strong>Lecturer Timetabling System</strong> at <strong>Colombo University</strong> is designed to streamline the process of creating and managing class schedules for lecturers. This system provides an efficient, user-friendly interface for faculty members to view and manage their timetables.
                        </p>
                        <h5>Key Features:</h5>
                        <ul>
                            <li>Automated timetable generation for lectures and meetings.</li>
                            <li>Real-time updates and notifications for schedule changes.</li>
                            <li>Conflict detection to prevent overlapping classes.</li>
                            <li>User-friendly dashboard tailored for lecturers.</li>
                            <li>Secure login system for authenticated access.</li>
                        </ul>
                        <p>
                            Our goal is to enhance academic planning and ensure seamless scheduling for all lecturers at the university.
                        </p>
                    </div>
                    <div class="card-footer text-muted">
                        &copy; <?php echo date("Y"); ?> Colombo University. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
