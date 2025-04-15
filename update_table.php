<?php
session_start();
include 'db.php'; // or require 'db.php';

// Database connection
/*$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'test';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/

// Retrieve success or error messages from session (if any)
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
// Clear the messages from session so they don't persist on next load
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Allowed categories
$allowed_categories = ['first_year', 'second_year', 'third_year', 'fourth_year'];

// Handle AJAX request for fetching lecturers based on department
if (isset($_GET['action']) && $_GET['action'] === 'get_lecturers' && isset($_GET['department'])) {
    $department = $conn->real_escape_string($_GET['department']);
    $stmt = $conn->prepare("SELECT name FROM lecture WHERE department = ?");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();
    $lecturers = [];
    while ($row = $result->fetch_assoc()) {
        $lecturers[] = $row['name'];
    }
    echo json_encode($lecturers);
    exit;
}

// Handle AJAX request for fetching offer departments based on course_id
if (isset($_GET['action']) && $_GET['action'] === 'get_offer_departments' && isset($_GET['course_id'])) {
    $course_id = $conn->real_escape_string($_GET['course_id']);
    $stmt = $conn->prepare("SELECT department FROM offer_courses WHERE course_id = ?");
    $stmt->bind_param("s", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $offer_departments = [];
    while ($row = $result->fetch_assoc()) {
        $offer_departments[] = $row['department'];
    }
    echo json_encode($offer_departments);
    exit;
}

// Handle category selection
$category = $_POST['category'] ?? $_GET['category'] ?? 'first_year';

// Validate table name
if (!in_array($category, $allowed_categories)) {
    die("Invalid category selected.");
}

// Fetch distinct departments from the lecture table
$departments = [];
$dept_result = $conn->query("SELECT DISTINCT department FROM lecture");
if ($dept_result && $dept_result->num_rows > 0) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row['department'];
    }
}

// Handle adding a new record
if (isset($_POST['add'])) {
    $course_id = trim($_POST['course_id'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    $no_of_hours = trim($_POST['no_of_hours'] ?? '');
    $credits = trim($_POST['credits'] ?? '');
    $registered_students = trim($_POST['registered_students'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $lecturer_name = trim($_POST['lecturer_name'] ?? '');
    $offer_departments = $_POST['offer_departments'] ?? []; // Array of selected departments

    // Validate required fields
    if (
        !empty($course_id) && !empty($course_name) && !empty($semester) && 
        !empty($no_of_hours) && !empty($credits) && !empty($registered_students) && 
        !empty($department) && !empty($lecturer_name)
    ) {
        // Check for duplicate course_id in the selected category
        $dup_stmt = $conn->prepare("SELECT course_id FROM $category WHERE course_id = ?");
        $dup_stmt->bind_param("s", $course_id);
        $dup_stmt->execute();
        $dup_stmt->store_result();
        if ($dup_stmt->num_rows > 0) {
            $_SESSION['error_message'] = "Course ID already exists!";
            $dup_stmt->close();
            header("Location: " . $_SERVER['PHP_SELF'] . "?category=$category");
            exit;
        }
        $dup_stmt->close();

        // Prepare and bind to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO $category (course_id, course_name, semester, no_of_hours, credits, registered_students, department, lecturer_name) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiiss", $course_id, $course_name, $semester, $no_of_hours, $credits, $registered_students, $department, $lecturer_name);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "New record added successfully!";
            
            // If category is third_year or fourth_year, handle offer_courses (only if user checked any)
            if (in_array($category, ['third_year', 'fourth_year']) && !empty($offer_departments)) {
                $offer_stmt = $conn->prepare("INSERT INTO offer_courses (course_id, department) VALUES (?, ?)");
                foreach ($offer_departments as $dept) {
                    // Validate department exists in lecture table
                    if (in_array($dept, $departments)) {
                        $offer_stmt->bind_param("ss", $course_id, $dept);
                        $offer_stmt->execute();
                    }
                }
                $offer_stmt->close();
            }
        } else {
            $_SESSION['error_message'] = "Error adding record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "All fields are required!";
    }
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?category=$category");
    exit;
}

// Handle updating existing records
if (isset($_POST['update'])) {
    $original_course_id = trim($_POST['original_course_id'] ?? '');
    $course_id = trim($_POST['course_id'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $semester = trim($_POST['semester'] ?? '');
    $no_of_hours = trim($_POST['no_of_hours'] ?? '');
    $credits = trim($_POST['credits'] ?? '');
    $registered_students = trim($_POST['registered_students'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $lecturer_name = trim($_POST['lecturer_name'] ?? '');
    $offer_departments = $_POST['offer_departments'] ?? []; // Array of selected departments

    // Validate required fields
    if (
        !empty($original_course_id) && !empty($course_id) && !empty($course_name) &&
        !empty($semester) && !empty($no_of_hours) && !empty($credits) && !empty($registered_students) &&
        !empty($department) && !empty($lecturer_name)
    ) {
        // If course_id is changed, check for duplicates
        if ($course_id !== $original_course_id) {
            $dup_stmt = $conn->prepare("SELECT course_id FROM $category WHERE course_id = ?");
            $dup_stmt->bind_param("s", $course_id);
            $dup_stmt->execute();
            $dup_stmt->store_result();
            if ($dup_stmt->num_rows > 0) {
                $_SESSION['error_message'] = "New Course ID already exists!";
                $dup_stmt->close();
                header("Location: " . $_SERVER['PHP_SELF'] . "?category=$category");
                exit;
            }
            $dup_stmt->close();
        }

        $stmt = $conn->prepare("UPDATE $category 
                                SET course_id=?, course_name=?, semester=?, no_of_hours=?,credits=?, registered_students=?, department=?, lecturer_name=? 
                                WHERE course_id=?"); 
        $stmt->bind_param("sssiiisss", $course_id, $course_name, $semester, $no_of_hours, $credits, $registered_students, $department, $lecturer_name, $original_course_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Record updated successfully!";
            
            // Handle offer_courses updates (only if category is third_year or fourth_year)
            if (in_array($category, ['third_year', 'fourth_year'])) {
                // First, delete existing offer_courses entries for this course_id
                $delete_offer_stmt = $conn->prepare("DELETE FROM offer_courses WHERE course_id = ?");
                $delete_offer_stmt->bind_param("s", $original_course_id);
                $delete_offer_stmt->execute();
                $delete_offer_stmt->close();

                // Then, insert the new offer_courses entries (only if user checked any)
                if (!empty($offer_departments)) {
                    $offer_stmt = $conn->prepare("INSERT INTO offer_courses (course_id, department) VALUES (?, ?)");
                    foreach ($offer_departments as $dept) {
                        // Validate department exists in lecture table
                        if (in_array($dept, $departments)) {
                            $offer_stmt->bind_param("ss", $course_id, $dept);
                            $offer_stmt->execute();
                        }
                    }
                    $offer_stmt->close();
                }
            } else {
                // If category is first_year or second_year, ensure no offer_courses entries remain
                $delete_offer_stmt = $conn->prepare("DELETE FROM offer_courses WHERE course_id = ?");
                $delete_offer_stmt->bind_param("s", $original_course_id);
                $delete_offer_stmt->execute();
                $delete_offer_stmt->close();
            }
        } else {
            $_SESSION['error_message'] = "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "All fields are required!";
    }
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?category=$category");
    exit;
}

// Handle deletion of records
if (isset($_POST['delete'])) {
    $course_id = trim($_POST['course_id'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // Validate category
    if (!in_array($category, $allowed_categories)) {
        $_SESSION['error_message'] = "Invalid category selected.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?category=$category");
        exit;
    }

    if (!empty($course_id)) {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Prepare and execute delete from category table
            $stmt = $conn->prepare("DELETE FROM $category WHERE course_id = ?");
            $stmt->bind_param("s", $course_id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                // If category is third_year or fourth_year, delete from offer_courses as well
                if (in_array($category, ['third_year', 'fourth_year'])) {
                    $offer_stmt = $conn->prepare("DELETE FROM offer_courses WHERE course_id = ?");
                    $offer_stmt->bind_param("s", $course_id);
                    $offer_stmt->execute();
                    $offer_stmt->close();
                }
                $_SESSION['success_message'] = "Record deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Record not found.";
            }
            $stmt->close();

            // Commit transaction
            $conn->commit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $_SESSION['error_message'] = "Error deleting record: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Invalid course ID.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?category=$category");
    exit;
}

// Fetch records from the selected category
$sql = "SELECT course_id, course_name, semester, no_of_hours, credits, registered_students, department, lecturer_name FROM $category";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags and Title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Manager</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .modal-header {
            background-color: #0d6efd;
            color: white;
        }
        .scroll-btn {
            position: fixed;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            opacity: 0.7;
            animation: blink 2s infinite;
            display: none; /* Initially hidden */
        }

        /* Positioning */
        #scrollTopBtn {
            bottom: 80px;
        }

        #scrollBottomBtn {
            bottom: 20px;
        }

        /* Hover Effects */
        .scroll-btn:hover {
            opacity: 1;
        }

        /* Blinking Animation */
        @keyframes blink {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .scroll-btn {
                width: 40px;
                height: 40px;
                line-height: 40px;
                font-size: 20px;
            }

            #scrollTopBtn {
                bottom: 70px;
            }

            #scrollBottomBtn {
                bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <!-- Removed the brand name to declutter the navbar -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <a href="main_table.php" class="btn btn-light">
                    <i class="bi bi-house-fill"></i> Back to Home
                </a>
            </div>
        </div>
    </nav>
    <!-- End of Navigation Bar -->

    <div class="container">
        <!-- Page Title and Add Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Course Manager</h1>
            <!-- Button to Open Add Record Modal -->
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRecordModal">
                <i class="bi bi-plus-lg"></i> Add New Record
            </button>
        </div>

        <!-- Display Success and Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Select Category -->
        <div class="mb-4">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="category" class="col-form-label">Select Year:</label>
                </div>
                <div class="col-auto">
                    <select id="category" name="category" class="form-select" onchange="this.form.submit()">
                        <option value="first_year" <?php if ($category == 'first_year') echo 'selected'; ?>>First Year</option>
                        <option value="second_year" <?php if ($category == 'second_year') echo 'selected'; ?>>Second Year</option>
                        <option value="third_year" <?php if ($category == 'third_year') echo 'selected'; ?>>Third Year</option>
                        <option value="fourth_year" <?php if ($category == 'fourth_year') echo 'selected'; ?>>Fourth Year</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <table id="coursesTable" class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Course ID</th>
                    <th>Course Name</th>
                    <th>Semester</th>
                    <th>No. of Hours</th>
                    <th>Credits</th> 
                    <th>Registered Students</th>
                    <th>Department</th>
                    <th>Lecturer Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['semester']); ?></td>
                            <td><?php echo htmlspecialchars($row['no_of_hours']); ?></td>
                            <td><?php echo htmlspecialchars($row['credits']); ?></td>
                            <td><?php echo htmlspecialchars($row['registered_students']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-primary edit-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editRecordModal"
                                    data-course_id="<?php echo htmlspecialchars($row['course_id']); ?>"
                                    data-course_name="<?php echo htmlspecialchars($row['course_name']); ?>"
                                    data-semester="<?php echo htmlspecialchars($row['semester']); ?>"
                                    data-no_of_hours="<?php echo htmlspecialchars($row['no_of_hours']); ?>"
                                    data-credits="<?php echo htmlspecialchars($row['credits']); ?>"
                                    data-registered_students="<?php echo htmlspecialchars($row['registered_students']); ?>"
                                    data-department="<?php echo htmlspecialchars($row['department']); ?>"
                                    data-lecturer_name="<?php echo htmlspecialchars($row['lecturer_name']); ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <!-- Delete Button -->
                                <button class="btn btn-sm btn-danger delete-btn" 
                                    data-course_id="<?php echo htmlspecialchars($row['course_id']); ?>"
                                    data-course_name="<?php echo htmlspecialchars($row['course_name']); ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                <!-- DataTables will handle empty tables -->
            </tbody>
        </table>

        <!-- Scroll to Top and Bottom Buttons -->
        <button id="scrollTopBtn" class="scroll-btn" aria-label="Scroll to Top">
            <i class="bi bi-arrow-up"></i>
        </button>

        <button id="scrollBottomBtn" class="scroll-btn" aria-label="Scroll to Bottom">
            <i class="bi bi-arrow-down"></i>
        </button>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
        <input type="hidden" name="course_id" id="delete_course_id">
        <input type="hidden" name="delete" value="1">
    </form>

    <!-- Add Record Modal -->
    <div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRecordModalLabel">Add New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="add_course_id" class="form-label">Course ID</label>
                                <input type="text" id="add_course_id" name="course_id" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="add_course_name" class="form-label">Course Name</label>
                                <input type="text" id="add_course_name" name="course_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="add_semester" class="form-label">Semester</label>
                                <select id="add_semester" name="semester" class="form-select" required>
                                    <option value="">Select Semester</option>
                                    <option value="semester 1">1</option>
                                    <option value="semester 2">2</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="add_no_of_hours" class="form-label">No. of Hours</label>
                                <input type="number" id="add_no_of_hours" name="no_of_hours" class="form-control" required min="0">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="add_credits" class="form-label">Credits</label>
                                <input type="number" id="add_credits" name="credits" class="form-control" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="add_registered_students" class="form-label">Registered Students</label>
                                <input type="number" id="add_registered_students" name="registered_students" class="form-control" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="add_department" class="form-label">Department</label>
                                <select id="add_department" name="department" class="form-select department-select" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="add_lecturer_name" class="form-label">Lecturer Name</label>
                                <select id="add_lecturer_name" name="lecturer_name" class="form-select" required>
                                    <option value="">Select Lecturer</option>
                                    <!-- Options will be populated based on selected department -->
                                </select>
                            </div>

                            <!-- Offer To Departments (Only for third_year and fourth_year) -->
                            <?php if (in_array($category, ['third_year', 'fourth_year'])): ?>
                                <div class="col-12">
                                    <label class="form-label">Offer To Departments (Optional):</label>
                                    <!-- Select All Checkbox -->
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllDepartmentsAdd">
                                        <label class="form-check-label" for="selectAllDepartmentsAdd">
                                            Select All
                                        </label>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($departments as $dept): ?>
                                            <div class="form-check me-3">
                                                <input class="form-check-input offer-dept-checkbox" 
                                                       type="checkbox" 
                                                       value="<?php echo htmlspecialchars($dept); ?>" 
                                                       id="offer_<?php echo htmlspecialchars($dept); ?>" 
                                                       name="offer_departments[]">
                                                <label class="form-check-label" for="offer_<?php echo htmlspecialchars($dept); ?>">
                                                    <?php echo htmlspecialchars($dept); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- End of Offer To Departments -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add" class="btn btn-success">Add Record</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End of Add Record Modal -->

    <!-- Edit Record Modal -->
    <div class="modal fade" id="editRecordModal" tabindex="-1" aria-labelledby="editRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="editRecordForm">
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <input type="hidden" name="original_course_id" id="edit_original_course_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRecordModalLabel">Edit Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="edit_course_id" class="form-label">Course ID</label>
                                <input type="text" id="edit_course_id" name="course_id" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_course_name" class="form-label">Course Name</label>
                                <input type="text" id="edit_course_name" name="course_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_semester" class="form-label">Semester</label>
                                <select id="edit_semester" name="semester" class="form-select" required>
                                    <option value="">Select Semester</option>
                                    <option value="semester 1">1</option>
                                    <option value="semester 2">2</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="edit_no_of_hours" class="form-label">No. of Hours</label>
                                <input type="number" id="edit_no_of_hours" name="no_of_hours" class="form-control" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="edit_credits" class="form-label">Credits</label>
                                <input type="number" id="edit_credits" name="credits" class="form-control" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="edit_registered_students" class="form-label">Registered Students</label>
                                <input type="number" id="edit_registered_students" name="registered_students" class="form-control" required min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="edit_department" class="form-label">Department</label>
                                <select id="edit_department" name="department" class="form-select department-select" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="edit_lecturer_name" class="form-label">Lecturer Name</label>
                                <select id="edit_lecturer_name" name="lecturer_name" class="form-select" required>
                                    <option value="">Select Lecturer</option>
                                    <!-- Options will be populated based on selected department -->
                                </select>
                            </div>

                            <!-- Offer To Departments (Only for third_year and fourth_year) -->
                            <?php if (in_array($category, ['third_year', 'fourth_year'])): ?>
                                <div class="col-12">
                                    <label class="form-label">Offer To Departments (Optional):</label>
                                    <!-- Select All Checkbox -->
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllDepartmentsEdit">
                                        <label class="form-check-label" for="selectAllDepartmentsEdit">
                                            Select All
                                        </label>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        <?php foreach ($departments as $dept): ?>
                                            <div class="form-check me-3">
                                                <input class="form-check-input offer-dept-checkbox-edit" 
                                                       type="checkbox" 
                                                       value="<?php echo htmlspecialchars($dept); ?>" 
                                                       id="edit_offer_<?php echo htmlspecialchars($dept); ?>" 
                                                       name="offer_departments[]">
                                                <label class="form-check-label" for="edit_offer_<?php echo htmlspecialchars($dept); ?>">
                                                    <?php echo htmlspecialchars($dept); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- End of Offer To Departments -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Record</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End of Edit Record Modal -->

    <!-- SweetAlert2 for Enhanced Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Initialize DataTables with stateSave enabled to maintain pagination state
            var table = $('#coursesTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "stateSave": true,
                "language": {
                    "emptyTable": "No records available."
                }
            });

            // Function to fetch lecturers based on department
            function fetchLecturers(department, selectElementId, selectedLecturer = '') {
                if (department === "") {
                    $('#' + selectElementId).html('<option value="">Select Lecturer</option>');
                    return;
                }
                $.ajax({
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                    method: "GET",
                    data: { action: 'get_lecturers', department: department },
                    dataType: "json",
                    success: function(data) {
                        var options = '<option value="">Select Lecturer</option>';
                        $.each(data, function(index, lecturer) {
                            if (lecturer === selectedLecturer) {
                                options += `<option value="${lecturer}" selected>${lecturer}</option>`;
                            } else {
                                options += `<option value="${lecturer}">${lecturer}</option>`;
                            }
                        });
                        $('#' + selectElementId).html(options);
                    },
                    error: function() {
                        $('#' + selectElementId).html('<option value="">Error loading lecturers</option>');
                    }
                });
            }

            // Handle department change in Add Modal
            $('#add_department').on('change', function() {
                var department = $(this).val();
                fetchLecturers(department, 'add_lecturer_name');
            });

            // Handle department change in Edit Modal
            $('#edit_department').on('change', function() {
                var department = $(this).val();
                fetchLecturers(department, 'edit_lecturer_name');
            });

            // Populate Edit Modal with existing data
            $('#editRecordModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var course_id = button.data('course_id');
                var course_name = button.data('course_name');
                var semester = button.data('semester');
                var no_of_hours = button.data('no_of_hours');
                var credits = button.data('credits');
                var registered_students = button.data('registered_students');
                var department = button.data('department');
                var lecturer_name = button.data('lecturer_name');

                var modal = $(this);
                modal.find('#edit_original_course_id').val(course_id);
                modal.find('#edit_course_id').val(course_id);
                modal.find('#edit_course_name').val(course_name);
                modal.find('#edit_semester').val(semester);
                modal.find('#edit_no_of_hours').val(no_of_hours);
                modal.find('#edit_credits').val(credits);
                modal.find('#edit_registered_students').val(registered_students);
                modal.find('#edit_department').val(department);

                // Fetch lecturers and set the selected lecturer
                fetchLecturers(department, 'edit_lecturer_name', lecturer_name);

                // If category is third_year or fourth_year, fetch and check the offer_departments
                <?php if (in_array($category, ['third_year', 'fourth_year'])): ?>
                    $.ajax({
                        url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                        method: "GET",
                        data: { action: 'get_offer_departments', course_id: course_id },
                        dataType: "json",
                        success: function(data) {
                            // Uncheck all first
                            $('.offer-dept-checkbox-edit').prop('checked', false);
                            // Check the ones in data
                            $.each(data, function(index, dept) {
                                $('#edit_offer_' + dept).prop('checked', true);
                            });

                            // Update Select All checkbox based on selections
                            var total = $('.offer-dept-checkbox-edit').length;
                            var checked = $('.offer-dept-checkbox-edit:checked').length;
                            $('#selectAllDepartmentsEdit').prop('checked', total === checked);
                        },
                        error: function() {
                            // Handle error if needed
                        }
                    });
                <?php endif; ?>
            });

            // Handle Delete Button Click using Event Delegation
            $('#coursesTable tbody').on('click', '.delete-btn', function() {
                var course_id = $(this).data('course_id');
                var course_name = $(this).data('course_name');

                Swal.fire({
                    title: 'Confirm Deletion',
                    text: `Do you want to delete the course "${course_name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Red color for delete action
                    cancelButtonColor: '#3085d6', // Blue color for cancel action
                    confirmButtonText: 'Delete', 
                    cancelButtonText: 'No, keep it',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Set the course_id in the hidden form and submit
                        $('#delete_course_id').val(course_id);
                        $('#deleteForm').submit();
                    }
                });
            });

            // Scroll to Top and Bottom Buttons Functionality
            // Show or hide buttons based on scroll position
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('#scrollTopBtn, #scrollBottomBtn').fadeIn();
                } else {
                    $('#scrollTopBtn, #scrollBottomBtn').fadeOut();
                }
            });

            // Scroll to top smoothly
            $('#scrollTopBtn').on('click', function() {
                $('html, body').animate({scrollTop: 0}, 'slow');
                return false;
            });

            // Scroll to bottom smoothly
            $('#scrollBottomBtn').on('click', function() {
                $('html, body').animate({scrollTop: $(document).height()}, 'slow');
                return false;
            });

            // Handle Select All functionality in Add Modal
            <?php if (in_array($category, ['third_year', 'fourth_year'])): ?>
                $('#selectAllDepartmentsAdd').on('change', function() {
                    var isChecked = $(this).is(':checked');
                    $('.offer-dept-checkbox').prop('checked', isChecked);
                });

                // Update Select All checkbox based on individual selections
                $('.offer-dept-checkbox').on('change', function() {
                    var total = $('.offer-dept-checkbox').length;
                    var checked = $('.offer-dept-checkbox:checked').length;
                    $('#selectAllDepartmentsAdd').prop('checked', total === checked);
                });

                // Similarly for Edit Modal
                $('#selectAllDepartmentsEdit').on('change', function() {
                    var isChecked = $(this).is(':checked');
                    $('.offer-dept-checkbox-edit').prop('checked', isChecked);
                });

                // Update Select All checkbox based on individual selections
                $('.offer-dept-checkbox-edit').on('change', function() {
                    var total = $('.offer-dept-checkbox-edit').length;
                    var checked = $('.offer-dept-checkbox-edit:checked').length;
                    $('#selectAllDepartmentsEdit').prop('checked', total === checked);
                });
            <?php endif; ?>

            // SweetAlert2 for Success and Error Messages
            <?php if ($success_message): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo addslashes($success_message); ?>',
                    timer: 3000,
                    showConfirmButton: false
                });
            <?php endif; ?>

            <?php if ($error_message): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?php echo addslashes($error_message); ?>',
                    timer: 5000,
                    showConfirmButton: true
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
