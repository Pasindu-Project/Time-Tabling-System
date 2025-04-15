<?php
ob_start(); // Start output buffering

// Server configuration
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";

// Initialize messages
$successMsg = "";
$errorMsg = "";

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and trim the input values
    $new_db_name = trim($_POST["dbname"]);
    $dean_name   = trim($_POST["dean_name"]);
    $email       = trim($_POST["email"]);
    $password    = trim($_POST["password"]);

    // Validate inputs
    if (empty($new_db_name) || empty($dean_name) || empty($email) || empty($password)) {
        $errorMsg = "Please fill in all required fields.";
    } else {
        // Connect to MySQL server (without selecting a database)
        $conn = new mysqli($servername, $dbUsername, $dbPassword);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Create the new database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS `" . $conn->real_escape_string($new_db_name) . "`";
        if ($conn->query($sql) !== TRUE) {
            $errorMsg = "Error creating database: " . $conn->error;
        } else {
            // Select the newly created database
            $conn->select_db($new_db_name);

            // Array of table creation queries (all tables will be empty)
            $tableQueries = [
                // all_students table
                "CREATE TABLE IF NOT EXISTS `all_students` (
                  `registration_num` VARCHAR(50) NOT NULL,
                  `name` VARCHAR(100) DEFAULT NULL,
                  `combination` VARCHAR(100) DEFAULT NULL,
                  `year` VARCHAR(30) NOT NULL,
                  `email` VARCHAR(100) DEFAULT NULL,
                  `password` VARCHAR(255) DEFAULT NULL,
                  `otp` VARCHAR(255) DEFAULT NULL,
                  `expired_otp` DATETIME DEFAULT NULL,
                  `create_otp` DATETIME DEFAULT NULL,
                  PRIMARY KEY (`registration_num`),
                  UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // dean table
                "CREATE TABLE IF NOT EXISTS `dean` (
                  `dean_id` INT(11) NOT NULL AUTO_INCREMENT,
                  `dean_name` VARCHAR(100) NOT NULL,
                  `email` VARCHAR(100) NOT NULL,
                  `password` VARCHAR(100) NOT NULL,
                  `otp` VARCHAR(255) DEFAULT NULL,
                  `expired_otp` DATETIME DEFAULT NULL,
                  `create_otp` DATETIME DEFAULT NULL,
                  PRIMARY KEY (`dean_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // first_year table
                "CREATE TABLE IF NOT EXISTS `first_year` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `course_id` VARCHAR(255) NOT NULL,
                  `course_name` VARCHAR(50) NOT NULL,
                  `semester` ENUM('semester 1','semester 2') NOT NULL,
                  `no_of_hours` INT(1) NOT NULL,
                  `credits` INT(11) DEFAULT NULL,
                  `registered_students` INT(10) NOT NULL,
                  `department` VARCHAR(50) NOT NULL,
                  `Lecturer_name` VARCHAR(50) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // fourth_year table
                "CREATE TABLE IF NOT EXISTS `fourth_year` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `course_id` VARCHAR(255) NOT NULL,
                  `course_name` VARCHAR(50) NOT NULL,
                  `semester` ENUM('semester 1','semester 2') NOT NULL,
                  `no_of_hours` INT(1) NOT NULL,
                  `credits` INT(11) DEFAULT NULL,
                  `registered_students` INT(10) NOT NULL,
                  `department` VARCHAR(50) NOT NULL,
                  `Lecturer_name` VARCHAR(50) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // lecture table
                "CREATE TABLE IF NOT EXISTS `lecture` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(100) NOT NULL,
                  `department` ENUM('Physics','Chemistry','Mathematics','Statistics','Zoology and Environment Science','Plant Science','UCSC','ITU') DEFAULT NULL,
                  `email` VARCHAR(100) NOT NULL,
                  `password` VARCHAR(100) NOT NULL,
                  `otp` VARCHAR(255) DEFAULT NULL,
                  `expired_otp` DATETIME DEFAULT NULL,
                  `create_otp` DATETIME DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // lecture_halls table
                "CREATE TABLE IF NOT EXISTS `lecture_halls` (
                  `hall_id` INT(10) NOT NULL AUTO_INCREMENT,
                  `hall_name` VARCHAR(255) NOT NULL,
                  `capacity` INT(11) NOT NULL,
                  `category` VARCHAR(255) NOT NULL,
                  PRIMARY KEY (`hall_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci",

                // offer_courses table
                "CREATE TABLE IF NOT EXISTS `offer_courses` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `course_id` VARCHAR(20) NOT NULL,
                  `department` VARCHAR(30) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // saved_courses table
                "CREATE TABLE IF NOT EXISTS `saved_courses` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `row` VARCHAR(255) NOT NULL,
                  `col` VARCHAR(255) NOT NULL,
                  `year` VARCHAR(50) NOT NULL,
                  `semester` VARCHAR(50) NOT NULL,
                  `course_name` VARCHAR(255) DEFAULT NULL,
                  `registered_students` INT(11) DEFAULT NULL,
                  `lecturer_name` VARCHAR(255) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci",

                // saved_examtimetable table
                "CREATE TABLE IF NOT EXISTS `saved_examtimetable` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `user_id` INT(11) NOT NULL,
                  `course_id` VARCHAR(255) NOT NULL,
                  `hall_name` VARCHAR(255) NOT NULL,
                  `hour` INT(11) NOT NULL,
                  `day` INT(11) NOT NULL,
                  `semester` VARCHAR(50) NOT NULL,
                  `category` VARCHAR(50) NOT NULL,
                  `clash` TINYINT(1) NOT NULL DEFAULT 0,
                  `clash_reason` VARCHAR(255) DEFAULT NULL,
                  `offered_to` VARCHAR(255) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci",

                // saved_timetable table
                "CREATE TABLE IF NOT EXISTS `saved_timetable` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `user_id` INT(11) NOT NULL,
                  `course_id` VARCHAR(50) DEFAULT NULL,
                  `hall_name` VARCHAR(100) NOT NULL,
                  `hour` INT(11) DEFAULT NULL,
                  `day` INT(11) DEFAULT NULL,
                  `semester` VARCHAR(20) DEFAULT NULL,
                  `category` VARCHAR(50) DEFAULT NULL,
                  `clash` TINYINT(1) DEFAULT 0,
                  `offered_to` VARCHAR(255) DEFAULT NULL,
                  `clash_reason` VARCHAR(255) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci",

                // second_year table
                "CREATE TABLE IF NOT EXISTS `second_year` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `course_id` VARCHAR(255) NOT NULL,
                  `course_name` VARCHAR(50) NOT NULL,
                  `semester` ENUM('semester 1','semester 2') NOT NULL,
                  `no_of_hours` INT(1) NOT NULL,
                  `credits` INT(11) DEFAULT NULL,
                  `registered_students` INT(11) NOT NULL,
                  `department` VARCHAR(50) NOT NULL,
                  `Lecturer_name` VARCHAR(50) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

                // third_year table
                "CREATE TABLE IF NOT EXISTS `third_year` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `course_id` VARCHAR(255) NOT NULL,
                  `course_name` VARCHAR(50) NOT NULL,
                  `semester` ENUM('semester 1','semester 2') NOT NULL,
                  `no_of_hours` INT(1) NOT NULL,
                  `credits` INT(11) DEFAULT NULL,
                  `registered_students` INT(10) NOT NULL,
                  `department` VARCHAR(50) NOT NULL,
                  `Lecturer_name` VARCHAR(50) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
            ];

            // Execute each table creation query
            foreach ($tableQueries as $query) {
                if ($conn->query($query) !== TRUE) {
                    $errorMsg .= "Error creating table: " . $conn->error . "<br>";
                }
            }

            // If there were no errors, insert the dean record into the dean table
            if (empty($errorMsg)) {
                $stmt = $conn->prepare("INSERT INTO dean (dean_name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $dean_name, $email, $password);
                if ($stmt->execute()) {
                    // Successful insertion; redirect to index.php
                    header("Location: index.php");
                    exit;
                } else {
                    $errorMsg = "Error inserting dean record: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Database &amp; Import Empty Tables with Dean Entry</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #FF5F6D, #FFC371);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }
    .card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .card-header {
      background-color: #4CAF50;
      color: #fff;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card mx-auto" style="max-width: 500px;">
      <div class="card-header text-center">
        <h3>Create New Database &amp; Add New Dean</h3>
      </div>
      <div class="card-body">
        <?php if ($successMsg): ?>
          <div class="alert alert-success" role="alert">
            <?php echo $successMsg; ?>
          </div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $errorMsg; ?>
          </div>
        <?php endif; ?>
        <form method="post" action="">
          <div class="mb-3">
            <label for="dbname" class="form-label">New Database Name</label>
            <input type="text" name="dbname" id="dbname" class="form-control" placeholder="Enter new database name" required>
          </div>
          <hr>
          <div class="mb-3">
            <label for="dean_name" class="form-label">Dean Name</label>
            <input type="text" name="dean_name" id="dean_name" class="form-control" placeholder="Enter Dean Name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <!-- For production, use type="password" and implement proper security -->
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
          </div>
          <div class="d-flex justify-content-between">
            <button type="submit" name="add_dean" class="btn btn-success">Add</button>
            <button type="reset" class="btn btn-secondary">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Bootstrap Bundle with Popper JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
