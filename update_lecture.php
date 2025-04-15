<?php
// Start the session to store messages
session_start();

// Place `use` statements at the top **right after** your autoloader is required.
require __DIR__ . '/send_mail/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ... then all other PHP code and HTML below ...

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

// Function to retrieve ENUM values for a given column
function getEnumOptions($conn, $table, $column) {
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $type = $row['Type']; // e.g., enum('Physics','Chemistry',...)
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        if (isset($matches[1])) {
            $enum = explode("','", $matches[1]);
            return $enum;
        }
    }
    return [];
}

// Retrieve ENUM options for 'department'
$departments = getEnumOptions($conn, 'lecture', 'department');

// Initialize variables for form fields
$id = "";
$name = "";
$department = "";
$email = "";
$password_hash = "";
$update = false;

// Function to add a new department to the ENUM
function addNewDepartment($conn, $new_department, $table, $column) {
    // Sanitize the new department name
    $new_department = trim($new_department);
    if (empty($new_department)) {
        return "New department name cannot be empty.";
    }

    // Escape single quotes in the department name
    $new_department_escaped = $conn->real_escape_string($new_department);

    // Check if the department already exists (case-insensitive)
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $type = $row['Type'];
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        if (isset($matches[1])) {
            $enum = explode("','", $matches[1]);
            foreach ($enum as $dept) {
                if (strcasecmp($dept, $new_department) == 0) {
                    return "Department already exists.";
                }
            }
        }
    }

    // Prepare the ALTER TABLE statement to add the new department
    $alter_query = "ALTER TABLE `$table` MODIFY `$column` ENUM(";
    foreach (getEnumOptions($conn, $table, $column) as $dept) {
        $alter_query .= "'" . $conn->real_escape_string($dept) . "',";
    }
    $alter_query .= "'" . $new_department_escaped . "')";

    // Execute the ALTER TABLE statement
    if ($conn->query($alter_query) === TRUE) {
        return true;
    } else {
        return "Error adding new department: " . $conn->error;
    }
}

// -----------------------//
// Handle Add Lecturer
// -----------------------//
if (isset($_POST['add'])) {
    // Retrieve and sanitize form inputs
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']);
    $new_department = isset($_POST['new_department']) ? trim($_POST['new_department']) : '';

    // Check if 'Add New Department' was selected
    if ($department === 'add_new') {
        // Validate new department
        if (empty($new_department)) {
            $_SESSION['message'] = "Please enter the new department name.";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Add the new department to ENUM
        $add_dept_result = addNewDepartment($conn, $new_department, 'lecture', 'department');
        if ($add_dept_result !== true) {
            $_SESSION['message'] = $add_dept_result;
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Update the departments array after adding the new department
        $departments = getEnumOptions($conn, 'lecture', 'department');
        $department = $new_department;
    }

    // Validate required fields
    if (empty($name) || empty($department) || empty($email) || empty($password_input)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['msg_type'] = "danger";
    } elseif (!in_array($department, $departments)) {
        $_SESSION['message'] = "Invalid department selected.";
        $_SESSION['msg_type'] = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['msg_type'] = "danger";
    } else {
        // Proactive Duplicate Check for Email and Name
        $stmt = $conn->prepare("SELECT id FROM lecture WHERE email = ? OR name = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $name);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                // Duplicate found
                $_SESSION['message'] = "A lecturer with the same email or name already exists.";
                $_SESSION['msg_type'] = "danger";
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Error preparing statement: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        }

        // Hash the password for database storage
        $password_hash = password_hash($password_input, PASSWORD_DEFAULT);

        // Prepare and bind the INSERT statement
        $stmt = $conn->prepare("INSERT INTO lecture (name, department, email, password) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            $_SESSION['message'] = "Error preparing statement: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        } else {
            $stmt->bind_param("ssss", $name, $department, $email, $password_hash);

            // Execute the statement
            if ($stmt->execute()) {
                // ------- If successfully added, send the email now -------
                $_SESSION['message'] = "Lecturer added successfully.";
                $_SESSION['msg_type'] = "success";

                try {
                    $mail = new PHPMailer(true);
                    
                    // SMTP Configuration
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    // Replace with your Gmail account and App Password:
                    $mail->Username   = 'pasindu22222@gmail.com';  
                    $mail->Password   = 'zmrm hlvf nusb vsnf';       
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('pasindu22222@gmail.com', 'Student Time Tabling System');
                    $mail->addAddress($email); // send to the lecturer's email

                    // Email Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Student Time Tabling System';
                    $mail->Body    = "
                        <html>
                        <head>
                            <title>Welcome to Student Time Tabling System</title>
                        </head>
                        <body>
                            <p>Hi <strong>{$name}</strong>,</p>
                            <p>Welcome to the <strong>Student Time Tabling System</strong>!</p>
                            <p>Your login details are as follows:</p>
                            <ul>
                                <li><strong>Email:</strong> {$email}</li>
                                <li><strong>Password:</strong> {$password_input}</li>
                            </ul>
                            <p>Please keep this information secure.</p>
                            <p>Thank You.</p>
                        </body>
                        </html>
                    ";

                    // Plaintext version for older email clients
                    $mail->AltBody = "Hi {$name},\n\nWelcome to the Student Time Tabling System!\n\n".
                                     "Your login details:\nEmail: {$email}\nPassword: {$password_input}\n\nThank You.";

                    $mail->send();

                    // Append success info about email
                    $_SESSION['message'] .= " An email with login credentials was sent to {$email}.";
                } catch (Exception $e) {
                    // If email fails, show a warning
                    $_SESSION['message'] .= " However, email could not be sent. Mailer Error: ".$mail->ErrorInfo;
                    $_SESSION['msg_type'] = "warning";
                }
            } else {
                // Check for duplicate entries
                if ($conn->errno === 1062) {
                    $_SESSION['message'] = "Email or Name already exists.";
                } else {
                    $_SESSION['message'] = "Error adding lecturer: " . $stmt->error;
                }
                $_SESSION['msg_type'] = "danger";
            }
            // Close the statement
            $stmt->close();
        }
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// -----------------------//
// Handle Edit Lecturer
// -----------------------//
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $update = true;

    // Prepare and execute the SELECT statement to fetch existing data
    $stmt = $conn->prepare("SELECT * FROM lecture WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If a record is found, populate the form fields
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $name = $row['name'];
            $department = $row['department'];
            $email = $row['email'];
            // Password is not fetched for security reasons
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing statement: " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    }
}

// -----------------------//
// Handle Update Lecturer
// -----------------------//
if (isset($_POST['update'])) {
    // Retrieve and sanitize form inputs
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']);
    $new_department = isset($_POST['new_department']) ? trim($_POST['new_department']) : '';

    // Check if 'Add New Department' was selected
    if ($department === 'add_new') {
        // Validate new department
        if (empty($new_department)) {
            $_SESSION['message'] = "Please enter the new department name.";
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Add the new department to ENUM
        $add_dept_result = addNewDepartment($conn, $new_department, 'lecture', 'department');
        if ($add_dept_result !== true) {
            $_SESSION['message'] = $add_dept_result;
            $_SESSION['msg_type'] = "danger";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Update the departments array after adding the new department
        $departments = getEnumOptions($conn, 'lecture', 'department');
        $department = $new_department;
    }

    // Validate required fields
    if (empty($name) || empty($department) || empty($email)) {
        $_SESSION['message'] = "Name, Department, and Email are required.";
        $_SESSION['msg_type'] = "danger";
    } elseif (!in_array($department, $departments)) {
        $_SESSION['message'] = "Invalid department selected.";
        $_SESSION['msg_type'] = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['msg_type'] = "danger";
    } else {
        // Proactive Duplicate Check for Email and Name (excluding current lecturer)
        $stmt = $conn->prepare("SELECT id FROM lecture WHERE (email = ? OR name = ?) AND id != ?");
        if ($stmt) {
            $stmt->bind_param("ssi", $email, $name, $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                // Duplicate found
                $_SESSION['message'] = "Another lecturer with the same email or name already exists.";
                $_SESSION['msg_type'] = "danger";
                $stmt->close();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Error preparing statement: " . $conn->error;
            $_SESSION['msg_type'] = "danger";
        }

        // If user provided a new password, update it
        if (!empty($password_input)) {
            // Hash the new password
            $password_hash = password_hash($password_input, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE lecture SET name=?, department=?, email=?, password=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("ssssi", $name, $department, $email, $password_hash, $id);
            } else {
                $_SESSION['message'] = "Error preparing statement: " . $conn->error;
                $_SESSION['msg_type'] = "danger";
            }
        } else {
            // Update without changing the password
            $stmt = $conn->prepare("UPDATE lecture SET name=?, department=?, email=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("sssi", $name, $department, $email, $id);
            } else {
                $_SESSION['message'] = "Error preparing statement: " . $conn->error;
                $_SESSION['msg_type'] = "danger";
            }
        }

        if (isset($stmt) && $stmt) {
            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['message'] = "Lecturer updated successfully.";
                $_SESSION['msg_type'] = "success";
            } else {
                // Check for duplicate entries
                if ($conn->errno === 1062) {
                    $_SESSION['message'] = "Email or Name already exists.";
                } else {
                    $_SESSION['message'] = "Error updating lecturer: " . $stmt->error;
                }
                $_SESSION['msg_type'] = "danger";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// -----------------------//
// Handle Delete Lecturer
// -----------------------//
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Prepare and bind the DELETE statement
    $stmt = $conn->prepare("DELETE FROM lecture WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['message'] = "Lecturer deleted successfully.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting lecturer: " . $stmt->error;
            $_SESSION['msg_type'] = "danger";
        }

        // Close the statement
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing statement: " . $conn->error;
        $_SESSION['msg_type'] = "danger";
    }

    // Redirect to avoid URL manipulation issues
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lecturers Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .card-header h5 {
            margin: 0;
        }
        /* Style for new department input */
        #new_department_field {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Lecturers Management</a>
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
                    echo htmlspecialchars($_SESSION['message']); 
                    // Check if the message is about duplicate email or name to trigger popup
                    $duplicate_message = (strpos($_SESSION['message'], 'already exists') !== false);
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php if ($duplicate_message): ?>
                <script>
                    // Trigger a JavaScript alert for duplicate email or name
                    alert("A lecturer with the same email or name already exists.");
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Lecturers Table -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">List of Lecturers</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch all lecturers from the database
                            $result = $conn->query("SELECT * FROM lecture ORDER BY id ASC");
                            if ($result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this lecturer?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                                echo "<tr><td colspan='5' class='text-center'>No lecturers found.</td></tr>";
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Lecturer Form -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><?php echo $update ? "Edit Lecturer" : "Add New Lecturer"; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($update): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department" required>
                            <option value="">--Select Department--</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept); ?>" <?php if ($dept == $department) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($dept); ?>
                                </option>
                            <?php endforeach; ?>
                            <!-- Add New Department Option -->
                            <option value="add_new" <?php if ($department === 'add_new') echo "selected"; ?>>Add New Department</option>
                        </select>
                    </div>
                    <!-- New Department Input Field -->
                    <div class="mb-3" id="new_department_field">
                        <label for="new_department" class="form-label">New Department Name</label>
                        <input type="text" class="form-control" id="new_department" name="new_department" 
                               value="<?php echo htmlspecialchars(isset($_POST['new_department']) ? $_POST['new_department'] : ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <?php echo $update ? "New Password (leave blank to keep current password)" : "Password"; ?>
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               <?php echo $update ? "" : "required"; ?>>
                    </div>
                    <button type="submit" name="<?php echo $update ? 'update' : 'add'; ?>" 
                            class="btn <?php echo $update ? 'btn-warning' : 'btn-primary'; ?>">
                        <?php echo $update ? '<i class="bi bi-pencil-square"></i> Update Lecturer' : '<i class="bi bi-plus-circle"></i> Add Lecturer'; ?>
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
    <script>
        // Function to toggle the new department input field
        function toggleNewDepartmentField() {
            const departmentSelect = document.getElementById('department');
            const newDeptField = document.getElementById('new_department_field');
            if (departmentSelect.value === 'add_new') {
                newDeptField.style.display = 'block';
            } else {
                newDeptField.style.display = 'none';
            }
        }

        // Initialize the new department field based on the current selection
        document.addEventListener('DOMContentLoaded', function() {
            toggleNewDepartmentField();
            document.getElementById('department').addEventListener('change', toggleNewDepartmentField);
        });
    </script>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
