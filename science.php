<?php
// Start the session
session_start();

include 'db.php'; // or require 'db.php';


// Database connection settings
/*$servername = "localhost";
$db_username = "root"; // Replace with your database username
$db_password = ""; // Replace with your database password
$dbname = "test"; // Replace with your database name

// Create connection using mysqli with error handling
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}*/

// Initialize an error message variable
$error_message = "";

// Function to sanitize output to prevent XSS
function sanitize_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Function to detect if a password is hashed
function is_password_hashed($password) {
    // Assuming bcrypt hashes; adjust the pattern if using a different algorithm
    return (strlen($password) === 60) && (preg_match('/^\$2y\$/', $password));
}

// Function to hash a password
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify password and handle hashing if necessary
function verify_and_update_password($conn, $email, $entered_password, $stored_password, $table_name) {
    if (is_password_hashed($stored_password)) {
        // Password is hashed; verify using password_verify
        if (password_verify($entered_password, $stored_password)) {
            return ['status' => true, 'new_hash' => null];
        } else {
            return ['status' => false, 'new_hash' => null];
        }
    } else {
        // Password is plain-text; verify by direct comparison
        if ($entered_password === $stored_password) {
            // Password is correct; hash it and update the database
            $new_hashed_password = hash_password($entered_password);
            $update_stmt = $conn->prepare("UPDATE $table_name SET password = ? WHERE email = ?");
            if ($update_stmt) {
                $update_stmt->bind_param("ss", $new_hashed_password, $email);
                $update_stmt->execute();
                $update_stmt->close();
                return ['status' => true, 'new_hash' => $new_hashed_password];
            } else {
                // Failed to prepare update statement
                return ['status' => false, 'new_hash' => null];
            }
        } else {
            return ['status' => false, 'new_hash' => null];
        }
    }
}

// Process the form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize email and password from POST data
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $entered_password = trim($_POST["password"]);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (empty($entered_password)) {
        $error_message = "Password cannot be empty.";
    } else {
        // Define SQL queries for different user roles
        $sql_student = "SELECT password FROM all_students WHERE email = ?";
        $sql_dean = "SELECT password FROM dean WHERE email = ?";
        $sql_lecture = "SELECT password, department FROM lecture WHERE email = ?";

        // Array to hold user data and redirect paths
        $user_roles = [
            [
                'table' => 'all_students',
                'sql' => $sql_student,
                'redirect' => 'home.php',
                'additional' => null
            ],
            [
                'table' => 'dean',
                'sql' => $sql_dean,
                'redirect' => 'main_table.php',
                'additional' => null
            ],
            [
                'table' => 'lecture',
                'sql' => $sql_lecture,
                'redirect' => null, // Redirect based on department
                'additional' => 'department'
            ]
        ];

        $authenticated = false;

        foreach ($user_roles as $role) {
            $stmt = $conn->prepare($role['sql']);
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    if ($role['table'] === 'lecture') {
                        // Lecture table has an additional department field
                        $stmt->bind_result($db_password, $department);
                        $stmt->fetch();
                        $verification = verify_and_update_password($conn, $email, $entered_password, $db_password, $role['table']);
                        if ($verification['status']) {
                            $_SESSION['user_email'] = $email;
                            // Optionally regenerate session ID to prevent session fixation
                            session_regenerate_id(true);
                    
                            // Redirect to a common department page and pass the department as a GET parameter
                            header("Location: department.php?dept=" . urlencode(strtolower($department)));
                            $authenticated = true;
                            $stmt->close();
                            exit();
                        } else {
                            $error_message = "Invalid password. Please try again.";
                            break;
                        }
                    }
                    else {
                        // All other tables have only password
                        $stmt->bind_result($db_password);
                        $stmt->fetch();
                        $verification = verify_and_update_password($conn, $email, $entered_password, $db_password, $role['table']);
                        if ($verification['status']) {
                            $_SESSION['user_email'] = $email;
                            session_regenerate_id(true);
                            header("Location: " . $role['redirect']);
                            $authenticated = true;
                            $stmt->close();
                            exit();
                        } else {
                            $error_message = "Invalid password. Please try again.";
                            break;
                        }
                    }
                }
                $stmt->close();
            } else {
                $error_message = "Database error: Unable to prepare statement.";
                break;
            }
        }

        if (!$authenticated && empty($error_message)) {
            $error_message = "No account found with that email.";
        }
    }
}

// Google API credentials
require_once 'vendor/autoload.php'; // Ensure you have installed the Google API PHP Client via Composer

// Initialize Google Client
$google_client = new Google_Client();
$google_client->setClientId('24328922924-hdn0mkqmsdajveavibo61i6pf4oj24fs.apps.googleusercontent.com');
$google_client->setClientSecret('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');
$google_client->setRedirectUri('http://localhost/faculty/callback.php'); // Update with your callback URL
$google_client->addScope('email');
$google_client->addScope('profile');

// Handle Google login callback
if (isset($_GET['code'])) {
    try {
        $google_client->authenticate($_GET['code']);
        $_SESSION['access_token'] = $google_client->getAccessToken();
        header('Location: callback.php'); // Redirect to callback for further handling
        exit();
    } catch (Exception $e) {
        $error_message = "Google authentication failed: " . sanitize_output($e->getMessage());
    }
}

// If Google login is successful, retrieve user info
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $google_client->setAccessToken($_SESSION['access_token']);
    $google_service = new Google_Service_Oauth2($google_client);
    
    try {
        $google_account_info = $google_service->userinfo->get();
        $email = filter_var($google_account_info->email, FILTER_SANITIZE_EMAIL);

        // Define SQL queries to check user roles
        $sql_student = "SELECT email FROM all_students WHERE email = ?";
        $sql_dean = "SELECT email FROM dean WHERE email = ?";

        // Function to check existence in a table
        function check_user_exists($conn, $sql, $email) {
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();
                $exists = $stmt->num_rows > 0;
                $stmt->close();
                return $exists;
            }
            return false;
        }

        if (check_user_exists($conn, $sql_student, $email)) {
            // Redirect to student home
            $_SESSION['user_email'] = $email;
            session_regenerate_id(true);
            header('Location: home.php');
            exit();
        } elseif (check_user_exists($conn, $sql_dean, $email)) {
            // Redirect to dean main table
            $_SESSION['user_email'] = $email;
            session_regenerate_id(true);
            header('Location: main_table.php');
            exit();
        } else {
            $error_message = "No account associated with this Google account.";
        }
    } catch (Exception $e) {
        $error_message = "Failed to retrieve Google user information: " . sanitize_output($e->getMessage());
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Time Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="science.css">
    <!-- Font Awesome for icons (Optional, if used in your original code) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pVnI8D3bkN6s7Eul7tG4lPbaB6VYb7zwg+q74K+3aQbTYc1lq1R4FpZ5HEM+v1X8Hq3qZesxz7vOw1eZ7VV2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container">
    <div class="left-panel">
        <img src="logo.png" alt="Welcome Image" id="welcome-image" class="img-fluid">
        <p>University Student Time Table System</p>
    </div>
    <div class="form-panel">
        <h1>Login</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo sanitize_output($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Email
                </label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required value="<?php echo isset($email) ? sanitize_output($email) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        <a href="<?php echo sanitize_output($google_client->createAuthUrl()); ?>" class="btn btn-danger mt-3"> <i class="fab fa-google"></i>Sign in with Google</a>
        <p class="mt-3">Lost your password? <a href="./reset/forgot_password.php">Click here</a></p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
