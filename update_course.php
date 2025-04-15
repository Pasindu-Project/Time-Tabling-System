<?php
include 'db.php'; // or require 'db.php';

// Database connection
/*$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'test';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/

// Determine which year we are dealing with
$year = $_GET['year'] ?? 'first_year'; // Default to first_year

/**
 * 1) Fetch all existing (row, col) pairs for this $year from saved_courses
 */
$existingPairs = []; // We'll keep them as an array of ['row' => '...', 'col' => '...']
$sqlExisting = "SELECT `row`, `col` FROM `saved_courses` WHERE `year` = ?";
$stmtExisting = $conn->prepare($sqlExisting);
$stmtExisting->bind_param("s", $year);
$stmtExisting->execute();
$resultExisting = $stmtExisting->get_result();
while ($r = $resultExisting->fetch_assoc()) {
    $existingPairs[] = ['row' => $r['row'], 'col' => $r['col']];
}
$stmtExisting->close();

/**
 * 2) Build a "saved_courses" map so we can easily check if a (row, col) is already in the DB
 *    This is used for displaying checkboxes as "checked" below.
 */
$saved_courses = [];
foreach ($existingPairs as $pair) {
    $saved_courses[$pair['row']][$pair['col']] = true;
}

// 3) Fetch Distinct Courses for Semester 1
$sql_sem1 = "SELECT DISTINCT course_id, course_name, registered_students, lecturer_name 
             FROM $year 
             WHERE semester = 'semester 1'";
$result_sem1 = $conn->query($sql_sem1);
$sem1_courses = [];
while ($row = $result_sem1->fetch_assoc()) {
    $sem1_courses[] = $row;
}

// 4) Fetch Distinct Courses for Semester 2
$sql_sem2 = "SELECT DISTINCT course_id, course_name, registered_students, lecturer_name 
             FROM $year 
             WHERE semester = 'semester 2'";
$result_sem2 = $conn->query($sql_sem2);
$sem2_courses = [];
while ($row = $result_sem2->fetch_assoc()) {
    $sem2_courses[] = $row;
}

// 5) Process POST (Update) Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courses'])) {
    // Build an array of (row, col) pairs that the user has now checked
    // in the form:  $_POST['courses'][$rowCourseId][] = $colCourseId
    $submittedPairs = [];  // new list from user
    foreach ($_POST['courses'] as $rowCourseId => $colCourseIds) {
        foreach ($colCourseIds as $colCourseId) {
            $submittedPairs[] = [
                'row' => $rowCourseId,
                'col' => $colCourseId,
            ];
        }
    }

    /**
     * 6) Compare existingPairs with submittedPairs
     *    a) Insert new pairs
     *    b) Delete removed pairs
     */

    // (a) Insert any new pairs that are NOT yet in $existingPairs
    foreach ($submittedPairs as $p) {
        if (!in_array($p, $existingPairs)) {
            // Before inserting, optionally fetch course_name / registered_students / lecturer_name if you want
            // for this row or col. For example, let's do it for the row course:
            $rowCourseId  = $p['row'];
            $colCourseId  = $p['col'];
            
            // We can figure out the semester from the DB for the col or row. Let's do it for the col:
            $sqlCourseDetails = "
                SELECT course_name, registered_students, lecturer_name, semester
                FROM $year
                WHERE course_id = ?
                LIMIT 1
            ";
            $stmtCourse = $conn->prepare($sqlCourseDetails);
            $stmtCourse->bind_param("s", $colCourseId);
            $stmtCourse->execute();
            $resCourse = $stmtCourse->get_result();
            
            // Default values
            $cName = '';
            $cRegistered = 0;
            $cLecturer = '';
            $cSemester = 'semester 1';
            
            if ($resCourse->num_rows > 0) {
                $courseRow = $resCourse->fetch_assoc();
                $cName        = $courseRow['course_name'] ?: '';
                $cRegistered  = (int)$courseRow['registered_students'];
                $cLecturer    = $courseRow['lecturer_name'] ?: '';
                $cSemester    = $courseRow['semester'] ?: 'semester 1';
            }
            $stmtCourse->close();

            // Now insert into saved_courses
            $sqlInsert = "
                INSERT INTO `saved_courses` 
                    (`row`, `col`, `year`, `semester`, `course_name`, `registered_students`, `lecturer_name`) 
                VALUES 
                    (?, ?, ?, ?, ?, ?, ?)
            ";
            $stmtInsert = $conn->prepare($sqlInsert);
            // s = string, s= string, s= string, s= string, s= string, i= int, s= string
            $stmtInsert->bind_param(
                "sssssis",
                $rowCourseId, 
                $colCourseId, 
                $year, 
                $cSemester,
                $cName,
                $cRegistered,
                $cLecturer
            );
            $stmtInsert->execute();
            $stmtInsert->close();
        }
    }

    // (b) Delete any pairs that exist in DB but not in user-submitted pairs
    foreach ($existingPairs as $p) {
        if (!in_array($p, $submittedPairs)) {
            // The user has unchecked this box, so we remove the row from DB
            $sqlDelete = "DELETE FROM `saved_courses` WHERE `row` = ? AND `col` = ? AND `year` = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bind_param("sss", $p['row'], $p['col'], $year);
            $stmtDelete->execute();
            $stmtDelete->close();
        }
    }

    // Redirect to avoid form re-submission
    header("Location: update_course.php?year=" . urlencode($year));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Courses</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Scroll to Top and Bottom Buttons */
        .scroll-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            z-index: 99;
            font-size: 24px;
            border: none;
            outline: none;
            background-color: #0d6efd;
            color: white;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .scroll-btn:hover {
            background-color: #0b5ed7;
        }
        #scrollTopBtn, #scrollBottomBtn {
            display: none;
        }

        /* Sticky Headers & Row Names */
        /* Make sure the table is set to position sticky in the thead and the row's first column (th[scope="row"]) */
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #fff;
        }
        /* Make the first column sticky */
        .table tbody th[scope="row"] {
            position: sticky;
            left: 0;
            z-index: 9;
            background: #fff;
        }
        /* Adjust table overflow for smaller screens */
        .table-responsive {
            overflow: auto; /* Allows both horizontal and vertical scrolling if needed */
            max-height: 80vh; /* You can adjust this so the table has a maximum visible height */
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Course Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <a href="main_table.php" class="btn btn-outline-light">
                <i class="bi bi-house-door-fill"></i> Back to Home
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="mb-4">Update Courses</h1>
    <!-- Year Selection Form -->
    <form method="GET" action="update_course.php" class="mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <label for="year" class="col-form-label">Select Year:</label>
            </div>
            <div class="col-auto">
                <select id="year" name="year" onchange="this.form.submit()" class="form-select">
                    <option value="first_year"  <?php if ($year == 'first_year')  echo 'selected'; ?>>First Year</option>
                    <option value="second_year" <?php if ($year == 'second_year') echo 'selected'; ?>>Second Year</option>
                    <option value="third_year"  <?php if ($year == 'third_year')  echo 'selected'; ?>>Third Year</option>
                    <option value="fourth_year" <?php if ($year == 'fourth_year') echo 'selected'; ?>>Fourth Year</option>
                </select>
            </div>
        </div>
    </form>

    <form method="POST" action="">
        <!-- Semester 1 Courses Table -->
        <h2>Semester 1 Courses</h2>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Registered Students</th>
                        <th>Lecturer Name</th>
                        <!-- Column headers for each col_course in sem1 -->
                        <?php foreach ($sem1_courses as $col_course): ?>
                            <th><?php echo htmlspecialchars($col_course['course_id']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sem1_courses as $row_course): ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($row_course['course_id']); ?></th>
                        <td><?php echo htmlspecialchars($row_course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row_course['registered_students']); ?></td>
                        <td><?php echo htmlspecialchars($row_course['lecturer_name']); ?></td>
                        <!-- Now the matrix of checkboxes -->
                        <?php foreach ($sem1_courses as $col_course): ?>
                            <td class="text-center">
                                <input type="checkbox"
                                       name="courses[<?php echo htmlspecialchars($row_course['course_id']); ?>][]"
                                       value="<?php echo htmlspecialchars($col_course['course_id']); ?>"
                                       class="form-check-input"
                                       <?php 
                                         if (isset($saved_courses[$row_course['course_id']][$col_course['course_id']])) {
                                             echo 'checked';
                                         }
                                       ?>
                                >
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Semester 2 Courses Table -->
        <h2>Semester 2 Courses</h2>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Registered Students</th>
                        <th>Lecturer Name</th>
                        <!-- Column headers for each col_course in sem2 -->
                        <?php foreach ($sem2_courses as $col_course): ?>
                            <th><?php echo htmlspecialchars($col_course['course_id']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sem2_courses as $row_course): ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($row_course['course_id']); ?></th>
                        <td><?php echo htmlspecialchars($row_course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row_course['registered_students']); ?></td>
                        <td><?php echo htmlspecialchars($row_course['lecturer_name']); ?></td>
                        <!-- Matrix of checkboxes -->
                        <?php foreach ($sem2_courses as $col_course): ?>
                            <td class="text-center">
                                <input type="checkbox"
                                       name="courses[<?php echo htmlspecialchars($row_course['course_id']); ?>][]"
                                       value="<?php echo htmlspecialchars($col_course['course_id']); ?>"
                                       class="form-check-input"
                                       <?php 
                                         if (isset($saved_courses[$row_course['course_id']][$col_course['course_id']])) {
                                             echo 'checked';
                                         }
                                       ?>
                                >
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success me-2">
                <i class="bi bi-pencil-square"></i> Update
            </button>
            <a href="main_table.php" class="btn btn-secondary">
                <i class="bi bi-house-door-fill"></i> Back to Home
            </a>
        </div>
    </form>
</div>

<!-- Scroll to Top Button -->
<button onclick="scrollToTop()" id="scrollTopBtn" class="scroll-btn">
    <i class="bi bi-arrow-up-circle-fill"></i>
</button>

<!-- Scroll to Bottom Button -->
<button onclick="scrollToBottom()" id="scrollBottomBtn" class="scroll-btn">
    <i class="bi bi-arrow-down-circle-fill"></i>
</button>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show or hide the scroll buttons based on scroll position
    window.onscroll = function() {toggleScrollButtons()};

    function toggleScrollButtons() {
        const scrollTopBtn = document.getElementById("scrollTopBtn");
        const scrollBottomBtn = document.getElementById("scrollBottomBtn");
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            scrollTopBtn.style.display = "block";
        } else {
            scrollTopBtn.style.display = "none";
        }

        // Show scroll to bottom if not at bottom
        if ((window.innerHeight + window.pageYOffset) < document.body.offsetHeight - 300) {
            scrollBottomBtn.style.display = "block";
        } else {
            scrollBottomBtn.style.display = "none";
        }
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function scrollToBottom() {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }
</script>
</body>
</html>
