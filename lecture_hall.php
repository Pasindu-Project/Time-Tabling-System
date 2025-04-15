<?php
// Start the session to store messages
session_start();
include 'db.php'; // or require 'db.php';

// Database configuration
/*$servername = "localhost"; // Replace with your server name if different
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "test";          // Database name

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and handle any connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}*/

// Set character set to UTF-8 for proper encoding
$conn->set_charset("utf8");

// Initialize variables for form fields
$hall_id = "";
$hall_name = "";
$capacity = "";
$category = "";
$update = false;

// Handle Add Lecture Hall
if (isset($_POST['add'])) {
    // Retrieve and sanitize form inputs
    $hall_name = trim($_POST['hall_name']);
    $capacity = intval($_POST['capacity']);
    $category = trim($_POST['category']);

    // Check for duplicate lecture hall (based on hall_name)
    $check_stmt = $conn->prepare("SELECT hall_id FROM lecture_halls WHERE hall_name = ?");
    $check_stmt->bind_param("s", $hall_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Duplicate found
        $_SESSION['message'] = "Lecture Hall with the name '<strong>" . htmlspecialchars($hall_name) . "</strong>' already exists.";
        $_SESSION['msg_type'] = "danger";
    } else {
        // No duplicate found, proceed to insert
        // Prepare and bind the INSERT statement
        $stmt = $conn->prepare("INSERT INTO lecture_halls (hall_name, capacity, category) VALUES (?, ?, ?)");
        if ($stmt === false) {
            $_SESSION['message'] = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        } else {
            $stmt->bind_param("sis", $hall_name, $capacity, $category);

            // Execute the statement and set session messages based on success or failure
            if ($stmt->execute()) {
                $_SESSION['message'] = "Lecture Hall '<strong>" . htmlspecialchars($hall_name) . "</strong>' added successfully.";
                $_SESSION['msg_type'] = "success";
            } else {
                // Handle potential duplicate entry if unique constraint is set at DB level
                if ($conn->errno == 1062) { // Duplicate entry error code
                    $_SESSION['message'] = "Lecture Hall with the name '<strong>" . htmlspecialchars($hall_name) . "</strong>' already exists.";
                    $_SESSION['msg_type'] = "danger";
                } else {
                    $_SESSION['message'] = "Error adding lecture hall: " . htmlspecialchars($stmt->error);
                    $_SESSION['msg_type'] = "danger";
                }
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close the duplicate check statement
    $check_stmt->close();

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit Lecture Hall - Show Edit Form
if (isset($_GET['edit'])) {
    $hall_id = intval($_GET['edit']);
    $update = true;

    // Prepare and execute the SELECT statement to fetch existing data
    $stmt = $conn->prepare("SELECT * FROM lecture_halls WHERE hall_id=?");
    if ($stmt === false) {
        $_SESSION['message'] = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    } else {
        $stmt->bind_param("i", $hall_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If a record is found, populate the form fields
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hall_name = $row['hall_name'];
            $capacity = $row['capacity'];
            $category = $row['category'];
        } else {
            $_SESSION['message'] = "Lecture Hall not found.";
            $_SESSION['msg_type'] = "danger";
            $update = false;
        }
        $stmt->close();
    }
}

// Handle Update Lecture Hall
if (isset($_POST['update'])) {
    // Retrieve and sanitize form inputs
    $hall_id = intval($_POST['hall_id']);
    $hall_name = trim($_POST['hall_name']);
    $capacity = intval($_POST['capacity']);
    $category = trim($_POST['category']);

    // Check for duplicate lecture hall (based on hall_name), excluding current hall_id
    $check_stmt = $conn->prepare("SELECT hall_id FROM lecture_halls WHERE hall_name = ? AND hall_id != ?");
    $check_stmt->bind_param("si", $hall_name, $hall_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Duplicate found
        $_SESSION['message'] = "Another Lecture Hall with the name '<strong>" . htmlspecialchars($hall_name) . "</strong>' already exists.";
        $_SESSION['msg_type'] = "danger";
    } else {
        // No duplicate found, proceed to update
        // Prepare and bind the UPDATE statement
        $stmt = $conn->prepare("UPDATE lecture_halls SET hall_name=?, capacity=?, category=? WHERE hall_id=?");
        if ($stmt === false) {
            $_SESSION['message'] = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        } else {
            $stmt->bind_param("sisi", $hall_name, $capacity, $category, $hall_id);

            // Execute the statement and set session messages based on success or failure
            if ($stmt->execute()) {
                $_SESSION['message'] = "Lecture Hall '<strong>" . htmlspecialchars($hall_name) . "</strong>' updated successfully.";
                $_SESSION['msg_type'] = "success";
            } else {
                // Handle potential duplicate entry if unique constraint is set at DB level
                if ($conn->errno == 1062) { // Duplicate entry error code
                    $_SESSION['message'] = "Lecture Hall with the name '<strong>" . htmlspecialchars($hall_name) . "</strong>' already exists.";
                    $_SESSION['msg_type'] = "danger";
                } else {
                    $_SESSION['message'] = "Error updating lecture hall: " . htmlspecialchars($stmt->error);
                    $_SESSION['msg_type'] = "danger";
                }
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close the duplicate check statement
    $check_stmt->close();

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Delete Lecture Hall
if (isset($_GET['delete'])) {
    $hall_id = intval($_GET['delete']);

    // Prepare and bind the DELETE statement
    $stmt = $conn->prepare("DELETE FROM lecture_halls WHERE hall_id=?");
    if ($stmt === false) {
        $_SESSION['message'] = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    } else {
        $stmt->bind_param("i", $hall_id);

        // Execute the statement and set session messages based on success or failure
        if ($stmt->execute()) {
            $_SESSION['message'] = "Lecture Hall deleted successfully.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting lecture hall: " . htmlspecialchars($stmt->error);
            $_SESSION['msg_type'] = "danger";
        }

        // Close the statement
        $stmt->close();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lecture Halls Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Lecture Halls Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- "Back to Home" Button -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="main_table.php" class="btn btn-light text-primary">
                            <i class="bi bi-house-door-fill"></i> Back to Home
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">

        <!-- Display Session Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['msg_type']); ?> alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Lecture Halls Table -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">List of Lecture Halls</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Hall Name</th>
                                <th>Capacity</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch all lecture halls from the database
                            $result = $conn->query("SELECT * FROM lecture_halls ORDER BY hall_id ASC");
                            if ($result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['hall_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['hall_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $row['hall_id']; ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $row['hall_id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this lecture hall?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                                echo "<tr><td colspan='5' class='text-center'>No lecture halls found.</td></tr>";
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Lecture Hall Form -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><?php echo $update ? "Edit Lecture Hall" : "Add New Lecture Hall"; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($update): ?>
                        <input type="hidden" name="hall_id" value="<?php echo htmlspecialchars($hall_id); ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="hall_name" class="form-label">Hall Name</label>
                        <input type="text" class="form-control" id="hall_name" name="hall_name" 
                               value="<?php echo htmlspecialchars($hall_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" 
                               value="<?php echo htmlspecialchars($capacity); ?>" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">--Select Category--</option>
                            <option value="Lecturehall" <?php if ($category == "Lecturehall") echo "selected"; ?>>Lecturehall</option>
                            <option value="Lab" <?php if ($category == "Lab") echo "selected"; ?>>Lab</option>
                            <option value="Seminar" <?php if ($category == "Seminar") echo "selected"; ?>>Seminar</option>
                            <!-- Add more categories as needed -->
                        </select>
                    </div>
                    <button type="submit" name="<?php echo $update ? 'update' : 'add'; ?>" 
                            class="btn <?php echo $update ? 'btn-warning' : 'btn-primary'; ?>">
                        <?php echo $update ? '<i class="bi bi-pencil-square"></i> Update Lecture Hall' : '<i class="bi bi-plus-circle"></i> Add Lecture Hall'; ?>
                    </button>
                    <?php if ($update): ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary ms-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optional: Initialize Bootstrap components (e.g., tooltips) -->
    <script>
        // Example: Initialize tooltips if needed in future
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
