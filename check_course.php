<?php
include 'db.php'; // or require 'db.php';

// Database connection
/*$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'test';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/

// Fetch courses based on year (for AJAX)
if (isset($_POST['year'])) {
    $year = $_POST['year'];
    $result = $conn->query("SELECT * FROM $year");
    $semester1 = [];
    $semester2 = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Build an array (object in JS) that carries all needed info
            $courseData = [
                'course_id'           => $row['course_id'],
                'course_name'         => $row['course_name'],
                'registered_students' => $row['registered_students'],
                'lecturer_name'       => $row['Lecturer_name'],
            ];

            if ($row['semester'] === 'semester 1') {
                $semester1[] = $courseData;
            } elseif ($row['semester'] === 'semester 2') {
                $semester2[] = $courseData;
            }
        }
    }

    // Return JSON of semester1 and semester2
    echo json_encode(['semester1' => $semester1, 'semester2' => $semester2]);
    exit;
}

// Save selected courses to the database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_courses'])) {
    $selected_courses = json_decode($_POST['selected_courses'], true);
    $year = $_POST['selected_year'];

    foreach ($selected_courses as $course) {
        list($rowCourseId, $colCourseId, $semester) = explode(',', $course);

        // 1) Get additional data for the *row* course from the year table
        $queryRow = "SELECT course_name, registered_students, Lecturer_name
                     FROM $year
                     WHERE course_id = ?";
        $stmtRow = $conn->prepare($queryRow);
        $stmtRow->bind_param("s", $rowCourseId);
        $stmtRow->execute();
        $resultRow = $stmtRow->get_result();
        
        // Default values if not found
        $row_course_name = '';
        $row_registered_students = 0;
        $row_lecturer_name = '';

        if ($resultRow->num_rows > 0) {
            $r = $resultRow->fetch_assoc();
            $row_course_name = $r['course_name'];
            $row_registered_students = $r['registered_students'];
            $row_lecturer_name = $r['Lecturer_name'];
        }
        $stmtRow->close();

        // 2) Insert into saved_courses (including extra columns)
        $insertSql = "
            INSERT INTO saved_courses 
                (row, col, year, semester, course_name, registered_students, lecturer_name)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmtInsert = $conn->prepare($insertSql);
        // s = string, s = string, s = string, s = string, s = string, i = integer, s = string
        $stmtInsert->bind_param(
            "sssssis",
            $rowCourseId,
            $colCourseId,
            $year,
            $semester,
            $row_course_name,
            $row_registered_students,
            $row_lecturer_name
        );
        $stmtInsert->execute();
        $stmtInsert->close();
    }

    echo "<script>alert('Courses saved successfully!'); window.location.href='main_table.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Select Courses</h1>
    <div class="mb-4">
        <a href="main_table.php" class="btn btn-secondary">Back to Home</a>
    </div>
    <form method="GET" action="update_course.php">
        <button type="submit" class="btn btn-info">Show</button>
    </form>
    <form method="POST" action="check_course.php" id="course-form">
        <div class="mb-3">
            <label for="year" class="form-label">Select Year:</label>
            <select id="year" name="selected_year" class="form-select" required>
                <option value="first_year">First Year</option>
                <option value="second_year">Second Year</option>
                <option value="third_year">Third Year</option>
                <option value="fourth_year">Fourth Year</option>
            </select>
        </div>
        <div id="courses-container">
            <!-- Semester 1 and Semester 2 tables will be loaded here dynamically -->
        </div>
        <input type="hidden" name="selected_courses" id="selected-courses">
        <button type="submit" class="btn btn-success mt-3">Save</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    /**
     * Generates an HTML table for a given semester, including:
     *  - A header row with columns: "Course ID", "Course Name", "Registered Students", "Lecturer Name",
     *    followed by a column for each course's ID (to build the checkbox matrix).
     *  - A row for each course, displaying the above info, plus checkboxes for the cross of row x col.
     *
     * @param {Array} courses - Array of course objects { course_id, course_name, registered_students, lecturer_name }
     * @param {Number} semester - 1 or 2
     * @returns {String} HTML table string
     */
    function generateTable(courses, semester) {
        if (!courses || !courses.length) {
            return `<h3>Semester ${semester} Courses</h3><p>No courses found.</p>`;
        }

        let table = `<h3>Semester ${semester} Courses</h3>`;
        table += `<table class="table table-bordered">`;

        // Table header
        table += `<thead><tr>`;
        table += `<th>Course ID</th>`;
        table += `<th>Course Name</th>`;
        table += `<th>Registered Students</th>`;
        table += `<th>Lecturer Name</th>`;

        // For the "matrix" part, each course's ID is a separate column
        courses.forEach((colCourse) => {
            table += `<th>${colCourse.course_id}</th>`;
        });
        table += `</tr></thead>`;

        // Table body
        table += `<tbody>`;
        courses.forEach((rowCourse) => {
            table += `<tr>`;
            table += `<td>${rowCourse.course_id}</td>`;
            table += `<td>${rowCourse.course_name}</td>`;
            table += `<td>${rowCourse.registered_students}</td>`;
            table += `<td>${rowCourse.lecturer_name}</td>`;

            // For each column course, add a checkbox (row vs col)
            courses.forEach((colCourse) => {
                table += `
                    <td>
                        <input 
                            type="checkbox" 
                            class="course-checkbox" 
                            data-row="${rowCourse.course_id}" 
                            data-col="${colCourse.course_id}" 
                            data-semester="${semester}"
                        >
                    </td>`;
            });
            table += `</tr>`;
        });
        table += `</tbody></table>`;

        return table;
    }

    /**
     * Loads courses for a given year via AJAX (POST), then builds the tables
     */
    function loadCourses(year) {
        $.post('check_course.php', { year: year }, function (data) {
            const response = JSON.parse(data);
            const semester1Table = generateTable(response.semester1, 1);
            const semester2Table = generateTable(response.semester2, 2);

            $('#courses-container').html(semester1Table + semester2Table);
        });
    }

    $(document).ready(function () {
        // Load courses whenever the year selection changes
        $('#year').on('change', function () {
            const year = $(this).val();
            loadCourses(year);
        });

        // Initially load the courses for the default selected year
        loadCourses($('#year').val());

        // Before submitting, gather selected checkboxes
        $('#course-form').on('submit', function () {
            const selectedCourses = [];
            $('.course-checkbox:checked').each(function () {
                const row = $(this).data('row');
                const col = $(this).data('col');
                const semester = $(this).data('semester');
                // We'll store "row, col, semester" in the hidden input
                selectedCourses.push(`${row},${col},${semester}`);
            });
            $('#selected-courses').val(JSON.stringify(selectedCourses));
        });
    });
</script>
</body>
</html>
