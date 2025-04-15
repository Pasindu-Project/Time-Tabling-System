<?php
// Start the session and check login
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: science.php");
    exit();
}

include 'db.php'; // or require 'db.php';

// Database connection settings
/*$servername = "localhost";
$username   = "root";     // Replace with your DB username
$password   = "";         // Replace with your DB password
$dbname     = "test";     // Replace with your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
*/
// Fetch the logged-in user's name from all_students
$user_email = $_SESSION['user_email'];
$user_name  = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Student';
$sql = "SELECT name FROM all_students WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($name);
    if ($stmt->fetch()) {
        $user_name = htmlspecialchars($name);
    }
    $stmt->close();
}

// Fetch distinct semesters from saved_timetable
$semesters = $conn->query("SELECT DISTINCT semester FROM saved_timetable ORDER BY semester ASC");

// Define allowed categories
$categories = ["first_year", "second_year", "third_year", "fourth_year"];

// Get filter parameters from GET request
$selected_category   = isset($_GET['category']) ? $_GET['category'] : '';
$selected_semester   = isset($_GET['semester']) ? $_GET['semester'] : '';
$selected_department = isset($_GET['department']) ? $_GET['department'] : '';

// Build the timetable query based on selected category
if ($selected_category === 'third_year') {
    // For third_year, join with third_year table.
    // The department filter works if either the official department (from third_year)
    // or the offered_to value (from saved_timetable) matches.
    $sql_timetable = "SELECT st.category, st.semester, st.day, st.hour, st.course_id, st.hall_name
                      FROM saved_timetable st
                      LEFT JOIN third_year ty ON st.course_id = ty.course_id
                      WHERE st.category = 'third_year'
                        AND (st.semester = ? OR ? = '')
                        AND (? = '' OR (ty.department = ? OR st.offered_to = ?))";
    $param_types = "sssss";
    $params = [$selected_semester, $selected_semester, $selected_department, $selected_department, $selected_department];
} elseif ($selected_category === 'fourth_year') {
    // For fourth_year, similar logic applies.
    $sql_timetable = "SELECT st.category, st.semester, st.day, st.hour, st.course_id, st.hall_name
                      FROM saved_timetable st
                      LEFT JOIN fourth_year ty ON st.course_id = ty.course_id
                      WHERE st.category = 'fourth_year'
                        AND (st.semester = ? OR ? = '')
                        AND (? = '' OR (ty.department = ? OR st.offered_to = ?))";
    $param_types = "sssss";
    $params = [$selected_semester, $selected_semester, $selected_department, $selected_department, $selected_department];
} else {
    // For first_year, second_year, or when no category is chosen, no department filtering is needed.
    $sql_timetable = "SELECT category, semester, day, hour, course_id, hall_name
                      FROM saved_timetable
                      WHERE (category = ? OR ? = '')
                        AND (semester = ? OR ? = '')";
    $param_types = "ssss";
    $params = [$selected_category, $selected_category, $selected_semester, $selected_semester];
}

$sql_timetable .= " ORDER BY hour, day";

// Prepare and execute the statement
$stmt_timetable = $conn->prepare($sql_timetable);
if ($stmt_timetable === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt_timetable->bind_param($param_types, ...$params);
$stmt_timetable->execute();
$result_timetable = $stmt_timetable->get_result();

// Group timetable data by hour and day for display
$timetable = [];
if ($result_timetable->num_rows > 0) {
    while ($row = $result_timetable->fetch_assoc()) {
        $hour = $row['hour'];
        $day  = $row['day'];
        if (!isset($timetable[$hour][$day])) {
            $timetable[$hour][$day] = [];
        }
        $timetable[$hour][$day][] = [
            'course_id' => $row['course_id'],
            'hall_name' => $row['hall_name'],
        ];
    }
}
$stmt_timetable->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Time Table</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { font-family: 'Poppins', Arial, sans-serif; margin: 0; background: #f4f4f4; }
    .navbar { background: linear-gradient(135deg, #667eea, #764ba2); }
    .navbar-brand, .nav-link, .navbar-toggler-icon { color: white !important; }
    .nav-link:hover { background-color: rgba(255,255,255,0.3); border-radius: 5px; transform: scale(1.05); transition: all 0.3s; }
    .timetable-table th, .timetable-table td { vertical-align: middle; text-align: center; max-width: 150px; word-wrap: break-word; }
    .timetable-table th { background-color: #667eea; color: white; }
    .timetable-table tr:nth-child(even) { background-color: #f9f9f9; }
    .timetable-table tr:hover { background-color: #e0e0e0; }
    .timetable-table td { font-size: 14px; }
    .container { padding: 20px; }
    .filter-form { margin-bottom: 30px; }
    .dropdown-menu a.dropdown-item:hover { background-color: #f1f1f1; }
    .course-card { background-color: #e9ecef; border-radius: 5px; padding: 5px 10px; margin-bottom: 5px; }
    .course-id { font-weight: bold; color: #333; }
    .hall-name { font-size: 13px; color: #555; }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Student Time Table</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="home.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="student_about.php">About</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" 
               data-bs-toggle="dropdown" aria-expanded="false"><?php echo htmlspecialchars($user_name); ?></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li><a class="dropdown-item" href="profile.php">Profile Details</a></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Container -->
  <div class="container my-4">
    <!-- Header with Welcome text and Download CSV Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
      <button id="csv-download-btn" class="btn btn-secondary">
        <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Download CSV
      </button>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="" class="row g-3 align-items-center filter-form">
      <!-- Category Filter -->
      <div class="col-md-3">
        <label for="category" class="form-label">Category:</label>
        <select name="category" id="category" class="form-select" onchange="this.form.submit()">
          <option value="">All</option>
          <?php foreach ($categories as $category_option): ?>
            <option value="<?php echo htmlspecialchars($category_option); ?>" 
              <?php echo ($selected_category === $category_option) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $category_option))); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Department Filter (only for third_year and fourth_year) -->
      <?php if ($selected_category === 'third_year' || $selected_category === 'fourth_year'): ?>
      <div class="col-md-3">
        <label for="department" class="form-label">Department:</label>
        <select name="department" id="department" class="form-select">
          <option value="">All</option>
          <?php
          // Build a union query to fetch department names from both the year table and offered_to from saved_timetable
          if ($selected_category === 'third_year') {
              $dept_sql = "SELECT dept FROM (
                              (SELECT department AS dept FROM third_year)
                              UNION
                              (SELECT offered_to AS dept FROM saved_timetable WHERE category='third_year' AND offered_to IS NOT NULL)
                           ) AS union_dept
                           ORDER BY dept ASC";
          } elseif ($selected_category === 'fourth_year') {
              $dept_sql = "SELECT dept FROM (
                              (SELECT department AS dept FROM fourth_year)
                              UNION
                              (SELECT offered_to AS dept FROM saved_timetable WHERE category='fourth_year' AND offered_to IS NOT NULL)
                           ) AS union_dept
                           ORDER BY dept ASC";
          }
          $departments_result = $conn->query($dept_sql);
          if ($departments_result) {
              while ($dept = $departments_result->fetch_assoc()) {
                  $dept_name = $dept['dept'];
                  echo '<option value="' . htmlspecialchars($dept_name) . '" ' .
                       (($selected_department === $dept_name) ? 'selected' : '') . '>' .
                       htmlspecialchars($dept_name) . '</option>';
              }
          }
          ?>
        </select>
      </div>
      <?php endif; ?>

      <!-- Semester Filter -->
      <div class="col-md-3">
        <label for="semester" class="form-label">Semester:</label>
        <select name="semester" id="semester" class="form-select">
          <option value="">All</option>
          <?php while ($row = $semesters->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($row['semester']); ?>" 
              <?php echo ($selected_semester === $row['semester']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($row['semester']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Filter and Reset Buttons -->
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary ms-2">Reset</a>
      </div>
    </form>

    <!-- Timetable Display -->
    <?php 
    // Define time slots and days of the week
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
                      echo '<div class="course-id">' . htmlspecialchars($course['course_id']) . '</div>';
                      echo '<div class="hall-name">' . htmlspecialchars($course['hall_name']) . '</div>';
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

  <?php $conn->close(); ?>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!-- CSV Download Script -->
  <script>
  document.getElementById('csv-download-btn').addEventListener('click', () => {
      // Helper function: get Monday of the current week
      function getMonday(d) {
          d = new Date(d);
          const day = d.getDay();
          const diff = d.getDate() - day + (day === 0 ? -6 : 1);
          return new Date(d.setDate(diff));
      }

      // Helper: format date as MM/DD/YYYY
      function formatDate(d) {
          const mm = String(d.getMonth() + 1).padStart(2, '0');
          const dd = String(d.getDate()).padStart(2, '0');
          const yyyy = d.getFullYear();
          return `${mm}/${dd}/${yyyy}`;
      }

      // Escape CSV values
      function escapeCsv(value) {
          if (value == null) return '""';
          return `"${String(value).replace(/"/g, '""')}"`;
      }

      const headers = [
          "Course ID",
          "Hall Name",
          "Day",
          "Start Time",
          "End Time"
      ];

      const csvRows = [];
      csvRows.push(headers.map(escapeCsv).join(','));

      // Define mapping for day names based on cell index (1 to 7)
      const days = {
          1: "Monday",
          2: "Tuesday",
          3: "Wednesday",
          4: "Thursday",
          5: "Friday",
          6: "Saturday",
          7: "Sunday"
      };

      // Process each row in the timetable table
      const table = document.querySelector('.timetable-table');
      const tbodyRows = table.querySelectorAll('tbody tr');
      
      tbodyRows.forEach(tr => {
          // First cell is the time slot, e.g., "8:00 - 9:00"
          const timeRangeText = tr.cells[0].textContent.trim();
          const [startTime, endTime] = timeRangeText.split('-').map(s => s.trim());
          
          // Loop over each day cell (columns 1 to 7)
          for (let col = 1; col < tr.cells.length; col++) {
              const cell = tr.cells[col];
              const dayName = days[col];
              // If the cell contains course cards, create a CSV row for each card
              const courseCards = cell.querySelectorAll('.course-card');
              courseCards.forEach(card => {
                  const courseId = card.querySelector('.course-id') ? card.querySelector('.course-id').textContent.trim() : '';
                  const hallName = card.querySelector('.hall-name') ? card.querySelector('.hall-name').textContent.trim() : '';
                  
                  const row = [
                      courseId,
                      hallName,
                      dayName,
                      startTime,
                      endTime
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
      a.download = 'student-timetable.csv';
      a.click();
      URL.revokeObjectURL(url);
  });
  </script>
</body>
</html>
