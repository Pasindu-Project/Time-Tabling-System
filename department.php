<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: science.php");
    exit();
}

include 'db.php'; // or require 'db.php';


// Database connection settings
/*$servername  = "localhost";
$db_username = "root";    // Replace with your database username
$db_password = "";        // Replace with your database password
$dbname      = "test";     // Replace with your database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}
*/
$user_email = $_SESSION['user_email'];

// Initialize default values
$user_name  = "User";
$department = ""; // This will be filled automatically from the database

// 1) Attempt to fetch the lecturer's name and department from the lecture table
$sql = "SELECT name, department FROM lecture WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($lecturer_name, $dept_from_lecture);
    if ($stmt->fetch()) {
        $user_name  = htmlspecialchars($lecturer_name);
        $department = htmlspecialchars($dept_from_lecture);
    }
    $stmt->close();
} else {
    die("Error preparing lecture SQL statement: " . htmlspecialchars($conn->error));
}

// 2) If no lecturer record was found, attempt to fetch the user's name and department from the all_students table
if (empty($department)) {
    $sql = "SELECT name, department FROM all_students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($student_name, $dept_from_student);
        if ($stmt->fetch()) {
            $user_name  = htmlspecialchars($student_name);
            $department = htmlspecialchars($dept_from_student);
        }
        $stmt->close();
    } else {
        die("Error preparing student SQL statement: " . htmlspecialchars($conn->error));
    }
}

// If department is still not found, you can either set a default or exit with an error
if (empty($department)) {
    die("Department not found for the current user.");
}

// Define explicit categories (i.e. year tables)
$categories = ["first_year", "second_year", "third_year", "fourth_year"];

// Get filter values for Year and Semester from GET parameters (if any)
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';

// Retrieve distinct semesters from the saved_timetable table
$semesters = $conn->query("SELECT DISTINCT semester FROM saved_timetable ORDER BY semester ASC");
if (!$semesters) {
    die("Error fetching semesters: " . htmlspecialchars($conn->error));
}

// IMPORTANT: Force the lecturer to be the CURRENT user by using the name fetched above
$current_lecturer = $user_name;

// Because the department is coming directly from the database—and after validating your data—you can safely inject it into the UNION query.
$dept_safe = $conn->real_escape_string($department);

// Prepare the SQL statement for the timetable data.
$sql_timetable = "
    SELECT st.category, st.semester, st.day, st.hour, st.course_id, st.hall_name, c.Lecturer_name
    FROM saved_timetable st
    LEFT JOIN (
        SELECT course_id, Lecturer_name FROM first_year WHERE department = '$dept_safe'
        UNION ALL
        SELECT course_id, Lecturer_name FROM second_year WHERE department = '$dept_safe'
        UNION ALL
        SELECT course_id, Lecturer_name FROM third_year WHERE department = '$dept_safe'
        UNION ALL
        SELECT course_id, Lecturer_name FROM fourth_year WHERE department = '$dept_safe'
    ) c ON st.course_id = c.course_id
    WHERE (st.category = ? OR ? = '')
      AND (st.semester = ? OR ? = '')
      AND c.Lecturer_name = ?
    ORDER BY st.hour, st.day
";

$stmt_timetable = $conn->prepare($sql_timetable);
if (!$stmt_timetable) {
    die("Preparation failed: (" . htmlspecialchars($conn->errno) . ") " . htmlspecialchars($conn->error));
}

$stmt_timetable->bind_param("sssss",
    $selected_category,
    $selected_category,
    $selected_semester,
    $selected_semester,
    $current_lecturer
);

if (!$stmt_timetable->execute()) {
    die("Execution failed: (" . htmlspecialchars($stmt_timetable->errno) . ") " . htmlspecialchars($stmt_timetable->error));
}

$result_timetable = $stmt_timetable->get_result();
if (!$result_timetable) {
    die("Getting result failed: (" . htmlspecialchars($stmt_timetable->errno) . ") " . htmlspecialchars($stmt_timetable->error));
}

// Initialize an array to group timetable data by hour and day
$timetable = [];
if ($result_timetable->num_rows > 0) {
    while ($row = $result_timetable->fetch_assoc()) {
        $hour = $row['hour'];
        $day  = $row['day'];
        if (!isset($timetable[$hour][$day])) {
            $timetable[$hour][$day] = [];
        }
        $timetable[$hour][$day][] = [
            'course_id'     => htmlspecialchars($row['course_id']),
            'hall_name'     => htmlspecialchars($row['hall_name']),
            'lecturer_name' => htmlspecialchars($row['Lecturer_name'])
        ];
    }
}
$stmt_timetable->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords($department); ?> Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }
        /* Navigation Bar */
        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .navbar-brand, .nav-link, .navbar-toggler-icon {
            color: white !important;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            transform: scale(1.05);
            transition: all 0.3s;
        }
        /* Table Styling */
        .timetable-table th, .timetable-table td {
            vertical-align: middle;
            text-align: center;
            max-width: 150px;
            word-wrap: break-word;
        }
        .timetable-table th {
            background-color: #667eea;
            color: white;
        }
        .timetable-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .timetable-table tr:hover {
            background-color: #e0e0e0;
        }
        .timetable-table td {
            font-size: 14px;
        }
        .container {
            padding: 20px;
        }
        .filter-form {
            margin-bottom: 30px;
        }
        .course-card {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 5px 10px;
            margin-bottom: 5px;
        }
        .course-id {
            font-weight: bold;
            color: #333;
        }
        .hall-name, .lecturer-name {
            font-size: 13px;
            color: #555;
        }
        /* Loader Styles */
        .loader-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .loader {
            width: 70px;
            height: 70px;
            position: relative;
        }
        .loader:before {
            content: "";
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 6px solid #007bff;
            position: absolute;
            top: 0;
            left: 0;
            animation: pulse 1s ease-in-out infinite;
        }
        .loader:after {
            content: "";
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 6px solid transparent;
            border-top-color: #007bff;
            position: absolute;
            top: 0;
            left: 0;
            animation: spin 2s linear infinite;
        }
        .loader-text {
            font-size: 24px;
            margin-top: 20px;
            color: #007bff;
            font-family: Arial, sans-serif;
            text-align: center;
            text-transform: uppercase;
        }
        @keyframes pulse {
            0% {
                transform: scale(0.6);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0;
            }
            100% {
                transform: scale(0.6);
                opacity: 1;
            }
        }
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        /* Hide the main content until page load */
        .content {
            display: none;
        }
        /* Once the page is loaded, hide the loader and show the content */
        .loaded .loader-container {
            display: none;
        }
        .loaded .content {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Loader Container -->
    <div class="loader-container">
        <div class="loader"></div>
        <div class="loader-text">Loading...</div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><?php echo ucwords($department); ?> Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#navbarNav" aria-controls="navbarNav" 
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="department.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="department_about.php">About</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" 
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($user_name); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Container -->
        <div class="container my-4">
            <!-- Header Row: Welcome and CSV Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
                <button id="csv-download-btn" class="btn btn-secondary">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Download CSV
                </button>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="" class="row g-3 align-items-center filter-form">
                <div class="col-md-3">
                    <label for="category" class="form-label">Year:</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">All Years</option>
                        <?php foreach ($categories as $category_option): ?>
                            <option value="<?php echo htmlspecialchars($category_option); ?>"
                                <?php echo ($selected_category === $category_option) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $category_option))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="semester" class="form-label">Semester:</label>
                    <select name="semester" id="semester" class="form-select">
                        <option value="">All Semesters</option>
                        <?php
                        if ($semesters->num_rows > 0):
                            $semesters->data_seek(0);
                            while ($row = $semesters->fetch_assoc()):
                        ?>
                            <option value="<?php echo htmlspecialchars($row['semester']); ?>"
                                <?php echo ($selected_semester === $row['semester']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['semester']); ?>
                            </option>
                        <?php 
                            endwhile;
                        endif;
                        ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
                       class="btn btn-secondary ms-2">Reset</a>
                </div>
            </form>

            <!-- Timetable Display -->
            <?php
            $time_slots = [
                8  => "8:00 - 9:00",
                9  => "9:00 - 10:00",
                10 => "10:00 - 11:00",
                11 => "11:00 - 12:00",
                12 => "12:00 - 1:00",
                13 => "1:00 - 2:00",
                14 => "2:00 - 3:00",
                15 => "3:00 - 4:00",
                16 => "4:00 - 5:00"
            ];

            $days = [
                0 => "Monday",
                1 => "Tuesday",
                2 => "Wednesday",
                3 => "Thursday",
                4 => "Friday",
                5 => "Saturday",
                6 => "Sunday"
            ];
            ?>

            <div class="table-responsive">
                <table class="table table-bordered timetable-table mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>Time Slot</th>
                            <?php foreach ($days as $day): ?>
                                <th><?php echo htmlspecialchars($day); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($time_slots as $hour => $time_range): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($time_range); ?></td>
                                <?php foreach (array_keys($days) as $day): ?>
                                    <td>
                                        <?php 
                                        if (isset($timetable[$hour][$day])) {
                                            foreach ($timetable[$hour][$day] as $course) {
                                                echo '<div class="course-card">';
                                                echo '<div class="course-id">' . $course['course_id'] . '</div>';
                                                echo '<div class="hall-name">Hall: ' . $course['hall_name'] . '</div>';
                                                if (!empty($course['lecturer_name'])) {
                                                    echo '<div class="lecturer-name">Lecturer: ' . $course['lecturer_name'] . '</div>';
                                                }
                                                echo '</div>';
                                            }
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JavaScript: Loader and CSV Download Logic -->
    <script>
        window.addEventListener('load', function() {
            // Short delay before showing content
            setTimeout(function() {
                document.body.classList.add('loaded');
            }, 500);
        });
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
    <!-- CSV Download Logic -->
    <script>
        document.getElementById('csv-download-btn').addEventListener('click', () => {
            // Helper function to get Monday of the current week
            function getMonday(d) {
                d = new Date(d);
                const day = d.getDay();
                const diff = d.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is Sunday
                return new Date(d.setDate(diff));
            }
        
            // Helper to format date as MM/DD/YYYY
            function formatDate(d) {
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                const yyyy = d.getFullYear();
                return `${mm}/${dd}/${yyyy}`;
            }
        
            // Always wrap CSV fields in quotes and escape internal quotes
            function escapeCsv(value) {
                if (value == null) return '""';
                return `"${String(value).replace(/"/g, '""')}"`;
            }
        
            const headers = [
                "Subject",
                "Start Date",
                "Start Time",
                "End Date",
                "End Time",
                "Description",
                "Location",
                "All Day Event"
            ];
        
            const csvRows = [];
            csvRows.push(headers.map(escapeCsv).join(','));
        
            // Calculate Monday of this week as reference date
            const monday = getMonday(new Date());
        
            // Map table column index to day offset (col index 1 => Monday, 2 => Tuesday, etc.)
            const dayOffsets = {
                1: 0, // Monday
                2: 1,
                3: 2,
                4: 3,
                5: 4,
                6: 5,
                7: 6
            };
        
            const table = document.querySelector('.timetable-table');
            const tbodyRows = table.querySelectorAll('tbody tr');
        
            tbodyRows.forEach(tr => {
                // First cell holds time range e.g., "08:00 - 09:00"
                const timeText = tr.cells[0].textContent.trim();
                const [startTime, endTime] = timeText.split('-').map(s => s.trim());
        
                // Loop over day cells (columns 1 to 7)
                for (let col = 1; col < tr.cells.length; col++) {
                    const cell = tr.cells[col];
                    // Calculate event date based on Monday + offset
                    const eventDate = new Date(monday);
                    eventDate.setDate(monday.getDate() + (dayOffsets[col] || 0));
                    const formattedDate = formatDate(eventDate);
        
                    // In each cell, if there are course cards, create an event row for each
                    const courseCards = cell.querySelectorAll('.course-card');
                    courseCards.forEach(card => {
                        // Extract the course details from the card
                        const courseId = card.querySelector('.course-id') ? card.querySelector('.course-id').textContent.trim() : '';
                        const hallName = card.querySelector('.hall-name') ? card.querySelector('.hall-name').textContent.replace('Hall:', '').trim() : '';
                        let lecturer = "";
                        if (card.querySelector('.lecturer-name')) {
                            lecturer = card.querySelector('.lecturer-name').textContent.replace('Lecturer:', '').trim();
                        }
        
                        // Construct Subject and Description fields
                        const subject = `${courseId}`;
                        let description = `Lecturer: ${lecturer}`;
        
                        const row = [
                            subject,
                            formattedDate,
                            startTime,
                            formattedDate,
                            endTime,
                            description,
                            hallName,
                            "False"
                        ];
        
                        csvRows.push(row.map(escapeCsv).join(','));
                    });
                }
            });
        
            const csvString = csvRows.join("\n");
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'assigned-courses.csv';
            a.click();
            URL.revokeObjectURL(url);
        });
    </script>
</body>
</html>
