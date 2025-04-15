<?php
// main_table.php

// Start session
session_start();

/**
 * 1) Check if the user is logged in
 */
if (!isset($_SESSION['user_email'])) {
    header("Location: science.php");
    exit();
}
include 'db.php'; // or require 'db.php';

/**
 * 2) Database connection settings
 */
/*$servername  = "localhost";
$db_username = "root";
$db_password = "";
$dbname      = "test";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}*/

/**
 * 3) Fetch the logged-in dean's name (example logic)
 */
$user_email = $_SESSION['user_email'];
$user_name  = "Dean";  // default fallback

$sql  = "SELECT dean_name FROM dean WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($db_dean_name);
    if ($stmt->fetch()) {
        $user_name = htmlspecialchars($db_dean_name);
    }
    $stmt->close();
}

/**
 * 4) Initialize session arrays if not set
 */
if (!isset($_SESSION['dropped_items'])) {
    $_SESSION['dropped_items'] = [];
}
if (!isset($_SESSION['item_no_of_hours'])) {
    $_SESSION['item_no_of_hours'] = [];
}

/**
 * 5) Handle "Refresh List" functionality
 */
if (isset($_GET['refresh']) && $_GET['refresh'] === 'true') {
    // Recompute item_no_of_hours for the current filters
    $category   = $_SESSION['selected_category']  ?? 'first_year';
    $semester   = $_SESSION['selected_semester']  ?? '';
    $department = $_SESSION['selected_department'] ?? '';
    $searchTerm = $_SESSION['search_term']         ?? '';

    // Re-fetch from `$category` table with filters
    $sql_refresh = "SELECT course_id, no_of_hours FROM `$category` WHERE 1 ";
    $params = [];
    $types  = "";

    if ($semester) {
        $sql_refresh .= " AND semester = ?";
        $params[] = $semester;
        $types   .= "s";
    }
    if (($category === 'third_year' || $category === 'fourth_year') && $department) {
        $sql_refresh .= " AND department = ?";
        $params[] = $department;
        $types   .= "s";
    }
    if ($searchTerm) {
        $sql_refresh .= " AND course_id LIKE ?";
        $like = "%" . $searchTerm . "%";
        $params[] = $like;
        $types   .= "s";
    }

    $stmt_r = $conn->prepare($sql_refresh);
    if ($stmt_r) {
        if (!empty($params)) {
            $stmt_r->bind_param($types, ...$params);
        }
        $stmt_r->execute();
        $res_r = $stmt_r->get_result();

        // Reset the item_no_of_hours for this category
        $_SESSION['item_no_of_hours'][$category] = [];

        while ($row_r = $res_r->fetch_assoc()) {
            $cid   = $row_r['course_id'];
            $dbH   = (int)$row_r['no_of_hours'];
            // Count how many times this course has been assigned in the timetable
            $assigned = 0;
            foreach ($_SESSION['dropped_items'] as $cell) {
                foreach ($cell as $itm) {
                    if ($itm['item'] === $cid && $itm['category'] === $category) {
                        $assigned++;
                    }
                }
            }
            $rem = $dbH - $assigned;
            if ($rem < 0) $rem = 0;

            $_SESSION['item_no_of_hours'][$category][$cid] = $rem;
        }
        $stmt_r->close();
    }

    // Save to DB (for demonstration)
    $user_id = 1;
    saveTimetableToDB($conn, $user_id, $_SESSION['dropped_items']);

    // Redirect to remove "refresh=true" from URL
    $redirect = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $redirect");
    exit;
}

/**
 * 6) Persist category, dept, semester, search in session
 *    whenever user changes them in the form (via GET).
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['refresh'])) {
    if (isset($_GET['category'])) {
        $_SESSION['selected_category'] = $_GET['category'];
        // If user changes year, we also reset department selection
        unset($_SESSION['selected_department']);
    }
    if (isset($_GET['department'])) {
        $_SESSION['selected_department'] = $_GET['department'];
    }
    if (isset($_GET['semester'])) {
        $_SESSION['selected_semester'] = $_GET['semester'];
    }
    if (isset($_GET['search'])) {
        $_SESSION['search_term'] = trim($_GET['search']);
    }
}

// Read the final values from session (or defaults)
$category   = $_SESSION['selected_category']  ?? 'first_year';
$semester   = $_SESSION['selected_semester']  ?? '';
$department = $_SESSION['selected_department'] ?? '';
$searchTerm = $_SESSION['search_term']         ?? '';

/**
 * 7) If session is new/empty, LOAD Timetable from DB (saved state)
 */
$user_id = 1;
if (empty($_SESSION['dropped_items'])) {
    loadTimetableFromDB($conn, $user_id);
}

// Ensure the selected category has an array for item_no_of_hours
if (!isset($_SESSION['item_no_of_hours'][$category])) {
    $_SESSION['item_no_of_hours'][$category] = [];
}

/**
 * 7a) If the item_no_of_hours for this category is empty, initialize it
 *     from the database for the current filters.
 */
if (empty($_SESSION['item_no_of_hours'][$category])) {
    $sql_init = "SELECT course_id, no_of_hours FROM `$category` WHERE 1 ";
    $params = [];
    $types  = "";

    if ($semester) {
        $sql_init .= " AND semester = ?";
        $params[] = $semester;
        $types   .= "s";
    }
    if (($category === 'third_year' || $category === 'fourth_year') && $department) {
        $sql_init .= " AND department = ?";
        $params[] = $department;
        $types   .= "s";
    }
    if ($searchTerm) {
        $sql_init .= " AND course_id LIKE ?";
        $like = "%" . $searchTerm . "%";
        $params[] = $like;
        $types   .= "s";
    }

    $stI = $conn->prepare($sql_init);
    if ($stI) {
        if (!empty($params)) {
            $stI->bind_param($types, ...$params);
        }
        $stI->execute();
        $rI = $stI->get_result();

        while ($rowI = $rI->fetch_assoc()) {
            $cid  = $rowI['course_id'];
            $dbH  = (int)$rowI['no_of_hours'];
            // Count how many times it's assigned
            $assigned = 0;
            foreach ($_SESSION['dropped_items'] as $cell) {
                foreach ($cell as $itm) {
                    if ($itm['item'] === $cid && $itm['category'] === $category) {
                        $assigned++;
                    }
                }
            }
            $rem = $dbH - $assigned;
            if ($rem < 0) $rem = 0;

            $_SESSION['item_no_of_hours'][$category][$cid] = $rem;
        }
        $stI->close();
    }
}

/**
 * 8) Handle Reset timetable
 */
if (isset($_GET['reset']) && $_GET['reset'] === 'true') {
    $_SESSION['dropped_items']    = [];
    $_SESSION['item_no_of_hours'] = [];
    unset($_SESSION['selected_category']);
    unset($_SESSION['selected_department']);
    unset($_SESSION['selected_semester']);
    unset($_SESSION['search_term']);

    // Clear from DB
    $user_id = 1;
    saveTimetableToDB($conn, $user_id, $_SESSION['dropped_items']);

    header("Location: " . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

/**
 * 9) Dropping an item from the side-list => timetable cell
 */
if (
    isset($_GET['item'], $_GET['hour'], $_GET['day'], $_GET['category'], $_GET['item_semester'], $_GET['hall'], $_GET['clash'], $_GET['registered_students'], $_GET['course_name'], $_GET['lecturer_name'])
    && !empty($_GET['item'])
    && !isset($_GET['move']) // not a move
) {
    $unique_id     = uniqid('item_', true);
    $item          = $_GET['item'];
    $hour          = $_GET['hour'];
    $day           = $_GET['day'];
    $cat           = $_GET['category'];
    $semester      = $_GET['item_semester'];
    $hall          = $_GET['hall'];
    $isClash       = filter_var($_GET['clash'], FILTER_VALIDATE_BOOLEAN);
    $regStudents   = (int)$_GET['registered_students'];
    $course_name   = $_GET['course_name'];
    $lecturer_name = $_GET['lecturer_name'];
    $clash_reason  = $_GET['clashReason'] ?? '';

    if (empty($hall)) {
        echo json_encode(['error' => 'Hall not selected']);
        exit;
    }

    // fetch department from $cat table
    $dept = '';
    $qDept = $conn->prepare("SELECT department FROM `$cat` WHERE course_id = ? LIMIT 1");
    if ($qDept) {
        $qDept->bind_param("s", $item);
        $qDept->execute();
        $rDept = $qDept->get_result();
        if ($rDept->num_rows > 0) {
            $deptRow = $rDept->fetch_assoc();
            $dept = htmlspecialchars($deptRow['department']);
        }
        $qDept->close();
    }

    // fetch offered_to from offer_courses
    $offered_to = null;
    $qOff = $conn->prepare("SELECT department FROM offer_courses WHERE course_id = ?");
    if ($qOff) {
        $qOff->bind_param("s", $item);
        $qOff->execute();
        $rOff = $qOff->get_result();
        $arrDepts = [];
        while ($d = $rOff->fetch_assoc()) {
            $arrDepts[] = htmlspecialchars($d['department']);
        }
        if (!empty($arrDepts)) {
            $offered_to = implode(", ", $arrDepts);
        }
        $qOff->close();
    }

    // decrement hours
    if (!isset($_SESSION['item_no_of_hours'][$cat])) {
        $_SESSION['item_no_of_hours'][$cat] = [];
    }
    if (isset($_SESSION['item_no_of_hours'][$cat][$item]) && $_SESSION['item_no_of_hours'][$cat][$item] > 0) {
        $_SESSION['item_no_of_hours'][$cat][$item]--;
    }

    $cellKey = "{$hour}_{$day}";
    if (!isset($_SESSION['dropped_items'][$cellKey])) {
        $_SESSION['dropped_items'][$cellKey] = [];
    }

    $_SESSION['dropped_items'][$cellKey][] = [
        'id'                  => $unique_id,
        'item'                => $item,
        'course_name'         => $course_name,
        'lecturer_name'       => $lecturer_name,
        'category'            => $cat,
        'semester'            => $semester,
        'hall'                => $hall,
        'clash'               => $isClash,
        'clash_reason'        => $clash_reason,
        'registered_students' => $regStudents,
        'department'          => $dept,
        'offered_to'          => $offered_to
    ];

    // Re-check that entire cell for new or existing clashes
    recheckCellClashesInSession($cellKey, $conn);

    // persist to DB
    $user_id = 1;
    saveTimetableToDB($conn, $user_id, $_SESSION['dropped_items']);

    echo json_encode([
        'success'     => true,
        'id'          => $unique_id,
        'hour'        => $hour,
        'day'         => $day,
        'clash'       => $isClash,
        'clashReason' => $clash_reason,
        'department'  => $dept
    ]);
    exit;
}

/**
 * 9a) Moving an item (timetable→timetable)
 */
if (
    isset($_GET['move'], $_GET['id'], $_GET['hour'], $_GET['day'], $_GET['src_hour'], $_GET['src_day'], $_GET['category'], $_GET['semester'], $_GET['hall'], $_GET['clash'], $_GET['course_name'], $_GET['lecturer_name'])
    && $_GET['move'] === 'true'
) {
    $id            = $_GET['id'];
    $hour          = $_GET['hour'];
    $day           = $_GET['day'];
    $src_hour      = $_GET['src_hour'];
    $src_day       = $_GET['src_day'];
    $cat           = $_GET['category'];
    $semester      = $_GET['semester'];
    $hall          = $_GET['hall'];
    $isClash       = filter_var($_GET['clash'], FILTER_VALIDATE_BOOLEAN);
    $course_name   = $_GET['course_name'] ?? '';
    $lecturer_name = $_GET['lecturer_name'] ?? '';
    $clash_reason  = $_GET['clashReason']   ?? '';

    // For fetching the department again (if needed):
    $itemForDept = $_GET['item'] ?? '';

    $dept = '';
    $qDept = $conn->prepare("SELECT department FROM `$cat` WHERE course_id = ? LIMIT 1");
    if ($qDept) {
        $qDept->bind_param("s", $itemForDept);
        $qDept->execute();
        $rd = $qDept->get_result();
        if ($rd->num_rows > 0) {
            $deptRow = $rd->fetch_assoc();
            $dept = htmlspecialchars($deptRow['department']);
        }
        $qDept->close();
    }

    // fetch offered_to
    $offered_to = null;
    $qOff = $conn->prepare("SELECT department FROM offer_courses WHERE course_id = ?");
    if ($qOff) {
        $qOff->bind_param("s", $itemForDept);
        $qOff->execute();
        $rO = $qOff->get_result();
        $arrD = [];
        while ($od = $rO->fetch_assoc()) {
            $arrD[] = htmlspecialchars($od['department']);
        }
        if (!empty($arrD)) {
            $offered_to = implode(", ", $arrD);
        }
        $qOff->close();
    }

    // find the item in the source cell
    $oldCellKey = "{$src_hour}_{$src_day}";
    $itemDataToMove = null;

    if (isset($_SESSION['dropped_items'][$oldCellKey])) {
        foreach ($_SESSION['dropped_items'][$oldCellKey] as $idx => $data) {
            if ($data['id'] === $id && $data['category'] === $cat) {
                $itemDataToMove = $data;
                unset($_SESSION['dropped_items'][$oldCellKey][$idx]);
                $_SESSION['dropped_items'][$oldCellKey] = array_values($_SESSION['dropped_items'][$oldCellKey]);
                break;
            }
        }
    }

    if ($itemDataToMove) {
        // re-check the old cell after removal
        recheckCellClashesInSession($oldCellKey, $conn);

        // add the item to the new cell
        $newCellKey = "{$hour}_{$day}";
        if (!isset($_SESSION['dropped_items'][$newCellKey])) {
            $_SESSION['dropped_items'][$newCellKey] = [];
        }
        $_SESSION['dropped_items'][$newCellKey][] = [
            'id'                  => $itemDataToMove['id'],
            'item'                => $itemDataToMove['item'],
            'course_name'         => $itemDataToMove['course_name'],
            'lecturer_name'       => $itemDataToMove['lecturer_name'],
            'category'            => $cat,
            'semester'            => $semester,
            'hall'                => $hall,
            'clash'               => $isClash,
            'clash_reason'        => $clash_reason,
            'registered_students' => $itemDataToMove['registered_students'],
            'department'          => $dept,
            'offered_to'          => $offered_to
        ];

        // re-check the new cell for potential new clashes
        recheckCellClashesInSession($newCellKey, $conn);

        // save to DB
        $user_id = 1;
        saveTimetableToDB($conn, $user_id, $_SESSION['dropped_items']);

        echo json_encode([
            'success'     => true,
            'hour'        => $hour,
            'day'         => $day,
            'clash'       => $isClash,
            'clashReason' => $clash_reason,
            'department'  => $dept
        ]);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
    exit;
}

/**
 * 10) Handle Undo (removing item from timetable cell)
 */
if (isset($_GET['undo'], $_GET['id'])) {
    $idToUndo = $_GET['id'];
    $found    = false;
    $itemData = null;
    $cellKey  = null;

    foreach ($_SESSION['dropped_items'] as $key => &$cell) {
        foreach ($cell as $idx => $data) {
            if ($data['id'] === $idToUndo) {
                $itemData = $data;
                $cellKey  = $key;
                unset($cell[$idx]);
                $_SESSION['dropped_items'][$key] = array_values($_SESSION['dropped_items'][$key]);
                $found = true;
                break 2;
            }
        }
    }

    if ($found && $itemData) {
        // Example constraint: can't undo if the item is "offered to" the current dept
        $current_dept = $_SESSION['selected_department'] ?? '';
        $is_offered_to_current = false;
        if (!empty($itemData['offered_to'])) {
            $arr = array_map('trim', explode(',', $itemData['offered_to']));
            if (in_array($current_dept, $arr)) {
                $is_offered_to_current = true;
            }
        }
        if ($is_offered_to_current) {
            echo json_encode(['error' => 'Cannot undo assignment for courses offered to the current department.']);
            exit;
        }

        // restore hours
        $undoCat = $itemData['category'];
        if (!isset($_SESSION['item_no_of_hours'][$undoCat])) {
            $_SESSION['item_no_of_hours'][$undoCat] = [];
        }
        if (isset($_SESSION['item_no_of_hours'][$undoCat][$itemData['item']])) {
            $_SESSION['item_no_of_hours'][$undoCat][$itemData['item']]++;
        } else {
            $_SESSION['item_no_of_hours'][$undoCat][$itemData['item']] = 1;
        }

        // re-check cell for remaining items
        recheckCellClashesInSession($cellKey, $conn);

        // fetch more info to return in JSON (if needed)
        $reg_students  = 0;
        $course_name   = '';
        $lecturer_name = '';
        $dept          = '';
        $sem           = '';

        $stU = $conn->prepare("SELECT department, registered_students, course_name, lecturer_name, semester
                               FROM `$undoCat` WHERE course_id = ? LIMIT 1");
        if ($stU) {
            $stU->bind_param("s", $itemData['item']);
            $stU->execute();
            $rsU = $stU->get_result();
            if ($rsU->num_rows > 0) {
                $rr = $rsU->fetch_assoc();
                $dept          = htmlspecialchars($rr['department']);
                $reg_students  = (int)$rr['registered_students'];
                $course_name   = htmlspecialchars($rr['course_name']);
                $lecturer_name = htmlspecialchars($rr['lecturer_name']);
                $sem           = htmlspecialchars($rr['semester']);
            }
            $stU->close();
        }

        // fetch offered_to
        $offered_to = null;
        $stO = $conn->prepare("SELECT department FROM offer_courses WHERE course_id = ?");
        if ($stO) {
            $stO->bind_param("s", $itemData['item']);
            $stO->execute();
            $rO = $stO->get_result();
            $arrO = [];
            while ($oo = $rO->fetch_assoc()) {
                $arrO[] = htmlspecialchars($oo['department']);
            }
            if (!empty($arrO)) {
                $offered_to = implode(", ", $arrO);
            }
            $stO->close();
        }

        // save to DB
        $user_id = 1;
        saveTimetableToDB($conn, $user_id, $_SESSION['dropped_items']);

        // success response
        echo json_encode([
            'success' => true,
            'id'      => $idToUndo,
            'item'    => $itemData['item'],
            'category' => $itemData['category'],
            'no_of_hours' => $_SESSION['item_no_of_hours'][$undoCat][$itemData['item']],
            'registered_students' => $reg_students,
            'course_name'         => $course_name,
            'lecturer_name'       => $lecturer_name,
            'semester'            => $sem,
            'clash'               => $itemData['clash'],
            'department'          => $dept,
            'offered_to'          => $offered_to
        ]);
        exit;
    }

    // If not found
    echo json_encode([
        'id' => null,
        'item' => null
    ]);
    exit;
}

/**
 * 11) Recheck a cell via AJAX (optional)
 */
if (isset($_GET['recheckCellClashes'])) {
    $hour = (int)($_GET['hour'] ?? 0);
    $day  = (int)($_GET['day'] ?? 0);
    $ck   = "{$hour}_{$day}";
    $upd  = recheckCellClashesInSession($ck, $conn);
    echo json_encode(['success' => true, 'items' => $upd]);
    exit;
}

// Finally close main DB connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dean's Dashboard</title>
    <link rel="icon" href="timetable.png" type="image/x-icon">
    <link rel="stylesheet" href="main_table.css">
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <!-- Include Bootstrap Icons -->
    <link 
        rel="stylesheet" 
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    >
    <!-- Include jsPDF and its autoTable plugin from CDNs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif; 
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        /* NAVBAR */
        .navbar {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }

        /* MAIN LAYOUT */
        #main {
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
        }
        #content {
            display: flex; 
            flex: 1;
        }

        /* LEFT PANEL */
        #section_one {
            width: 30%; 
            background: #fff; 
            padding: 20px; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            overflow-y: auto; 
            max-height: 100vh;
        }
        #section_one .form-select, #section_one .btn {
            margin-bottom: 8px;
        }

        /* RIGHT PANEL */
        #section_two {
            width: 70%; 
            padding: 20px; 
            /* Remove the overflow-x here so the entire panel doesn't get the scrollbar */
            /* overflow-x: auto; */
        }

        /* TABLE SCROLL CONTAINER */
        .table-scroll-container {
            /* Decide your preferred max-height */
            max-height: 600px; 
            overflow-y: auto; 
            overflow-x: auto; 
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        /* TABLE */
        table {
            width: 100%; 
            border-collapse: collapse; 
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        th, td {
            border: 1px solid #ccc; 
            width: 12.5%; 
            height: 60px; 
            vertical-align: top; 
            position: relative; 
            padding: 5px;
        }
        th {
            background-color: #667eea; 
            color: white;
        }
        /* table striping & hover highlight */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e0e0e0;
        }

        .dropzone {
            min-height: 50px; 
            overflow: auto; 
            background: #f9f9f9;
        }

        /* DRAGGABLE FROM SIDE LIST */
        .draggable-item {
            list-style: none; 
            padding: 8px; 
            margin-bottom: 8px; 
            background: #e2e6ea;
            border: 1px solid #dae0e5; 
            border-radius: 4px; 
            cursor: grab;
            display: flex; 
            flex-wrap: wrap; 
            gap: 8px; 
            align-items: center; 
            justify-content: space-between;
        }

        /* ASSIGNED ITEMS IN TABLE */
        .draggable-assigned {
            margin-bottom: 5px; 
            cursor: grab;
            display: flex; 
            flex-wrap: wrap; 
            gap: 5px; 
            align-items: center; 
            justify-content: space-between;
        }

        /* CLASH INDICATOR */
        .clash-indicator {
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            border-radius: 4px;
            padding: 2px 4px; 
            display: inline-block; 
            margin-top: 5px; 
            cursor: pointer;
        }
        .clash-indicator::after {
            content: " ⚠️";
        }

        /* UNDO BUTTON */
        .undo-button {
            border: none; 
            padding: 2px 5px; 
            cursor: pointer; 
            font-size: 0.8em; 
            margin-top: 5px; 
            border-radius: 3px;
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .undo-success {
            background-color: #28a745; 
            color: white;
        }
        .undo-error {
            background-color: #dc3545; 
            color: white;
        }

        .min-max-checkboxes {
            display: flex; 
            gap: 5px; 
            width: 100%;
        }

        .clash {
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
            border-radius: 4px;
            padding: 2px 4px; 
            display: inline-block; 
            margin-top: 5px;
        }

        .pdf-button-container {
            text-align: right;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Dean's Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_about.php">About</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <?php echo htmlspecialchars($user_name); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div id="main">
  <div id="content">
    <!-- SECTION ONE (Left Panel) -->
    <div id="section_one">
      <!-- Selection form -->
      <form method="GET" id="selection-form" class="mb-3">
        <!-- Year -->
        <div class="mb-3">
          <label for="category" class="form-label">Select Year:</label>
          <select id="category" name="category" class="form-select" 
                  onchange="document.getElementById('selection-form').submit()">
            <option value="first_year"  <?php if($category==='first_year') echo 'selected';?>>First Year</option>
            <option value="second_year" <?php if($category==='second_year') echo 'selected';?>>Second Year</option>
            <option value="third_year"  <?php if($category==='third_year') echo 'selected';?>>Third Year</option>
            <option value="fourth_year" <?php if($category==='fourth_year') echo 'selected';?>>Fourth Year</option>
          </select>
        </div>

        <!-- Department (only show if 3rd/4th year) -->
        <?php
        $connDept = new mysqli($servername, $db_username, $db_password, $dbname);
        if (!$connDept->connect_error) {
            if ($category === 'third_year' || $category === 'fourth_year') {
                $sqlD = "SELECT DISTINCT department FROM `$category`";
                $rdpt= $connDept->query($sqlD);
                $deptList = [];
                if ($rdpt && $rdpt->num_rows > 0) {
                    while($rw = $rdpt->fetch_assoc()) {
                        $deptList[] = $rw['department'];
                    }
                }
                ?>
                <div class="mb-3">
                  <label for="department" class="form-label">Select Department:</label>
                  <select id="department" name="department" class="form-select" 
                          onchange="document.getElementById('selection-form').submit()">
                    <option value="" <?php if($department === '') echo 'selected';?>>All Departments</option>
                    <?php
                    foreach($deptList as $dpt) {
                        $sel = ($department === $dpt) ? 'selected' : '';
                        echo "<option value='".htmlspecialchars($dpt)."' $sel>".htmlspecialchars($dpt)."</option>";
                    }
                    ?>
                  </select>
                </div>
                <?php
            }
        }
        $connDept->close();
        ?>

        <!-- Semester -->
        <div class="mb-3">
          <label for="semester" class="form-label">Select Semester:</label>
          <select id="semester" name="semester" class="form-select"
                  onchange="document.getElementById('selection-form').submit()">
            <option value="" <?php if($semester === '') echo 'selected';?>>All</option>
            <option value="semester 1" <?php if($semester === 'semester 1') echo 'selected';?>>Semester 1</option>
            <option value="semester 2" <?php if($semester === 'semester 2') echo 'selected';?>>Semester 2</option>
          </select>
        </div>

        <div class="mb-3 d-flex flex-wrap gap-2">
          <button type="submit" name="reset" value="true" class="btn btn-danger">Reset All</button>
          <a href="lecture_hall.php" class="btn btn-warning">Update Lecture Hall</a>
          <a href="update_table.php" class="btn btn-primary">Update Courses</a>
          <a href="update_lecture.php" class="btn btn-primary">Update Lecturer</a>
          <a href="update_course.php" class="btn btn-primary">Check Courses</a>
        </div>
      </form>

      <!-- "Refresh List" -->
      <button id="refresh-list-btn" class="btn btn-success mb-3">Refresh List</button>

      <!-- Search box -->
      <div class="mb-3">
        <label for="search-input" class="form-label">Search Course ID:</label>
        <input type="text" id="search-input" class="form-control" placeholder="Enter Course ID">
      </div>

      <!-- The list of courses to drag -->
      <?php
      // Show items from DB with the selected filters
      $connCourses = new mysqli($servername, $db_username, $db_password, $dbname);
      $sqlC = "SELECT course_id, course_name, lecturer_name, no_of_hours, semester, registered_students
               FROM `$category` WHERE 1 ";
      $p    = [];
      $t    = "";
      if ($semester) {
          $sqlC .= " AND semester = ?";
          $p[] = $semester; 
          $t  .= "s";
      }
      if (($category === 'third_year' || $category === 'fourth_year') && $department) {
          $sqlC .= " AND department = ?";
          $p[] = $department;
          $t  .= "s";
      }
      if ($searchTerm) {
          $sqlC .= " AND course_id LIKE ?";
          $likeS = "%" . $searchTerm . "%";
          $p[] = $likeS;
          $t  .= "s";
      }
      $stC = $connCourses->prepare($sqlC);
      if ($stC) {
          if (!empty($p)) {
              $stC->bind_param($t, ...$p);
          }
          $stC->execute();
          $rC = $stC->get_result();
      } else {
          $rC = false;
      }

      // Fetch all halls
      $allHalls = [];
      $hq = "SELECT hall_name, capacity FROM lecture_halls";
      $rH = $connCourses->query($hq);
      if ($rH && $rH->num_rows > 0) {
          while($rh = $rH->fetch_assoc()) {
              $allHalls[] = $rh;
          }
      }
      $allHalls_js = json_encode($allHalls, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
      echo "<script>const allHalls = $allHalls_js;</script>";

      if ($rC && $rC->num_rows > 0) {
          echo "<ul id='item-list' data-category='".htmlspecialchars($category)."' class='list-unstyled'>";
          while($row = $rC->fetch_assoc()) {
              $cid       = htmlspecialchars($row['course_id']);
              $cname     = htmlspecialchars($row['course_name']);
              $lect      = htmlspecialchars($row['lecturer_name']);
              $hrs_db    = (int)$row['no_of_hours'];
              $sem_row   = htmlspecialchars($row['semester']);
              $reg_num   = (int)$row['registered_students'];

              // Remaining hours = DB hours - how many times used
              $remH = $_SESSION['item_no_of_hours'][$category][$cid] ?? $hrs_db;
              if ($remH > 0) {
                  echo "<li draggable='true' class='draggable-item'
                           data-item='{$cid}'
                           data-course_name='{$cname}'
                           data-lecturer_name='{$lect}'
                           data-no_of_hours='{$remH}'
                           data-semester='{$sem_row}'
                           data-registered_students='{$reg_num}'>
                        <span>{$cid} ({$remH})</span>
                        <div class='min-max-checkboxes'>
                          <label><input type='checkbox' class='min-checkbox' checked> Min</label>
                          <label><input type='checkbox' class='max-checkbox' checked> Max</label>
                        </div>
                        <select class='form-select form-select-sm hall-select'>
                          <option value='' selected>Select Hall</option>
                          <option value='Online' data-capacity='0'>Online</option>";
                  foreach ($allHalls as $hh) {
                      $hn = htmlspecialchars($hh['hall_name']);
                      $cp = (int)$hh['capacity'];
                      echo "<option value='$hn' data-capacity='$cp'>$hn</option>";
                  }
                  echo "</select>";
                  echo "</li>";
              }
          }
          echo "</ul>";
      } else {
          echo "<p>No data found.</p>";
      }
      if ($stC) $stC->close();
      $connCourses->close();
      ?>
    </div>

    <!-- SECTION TWO (Right Panel) -->
    <div id="section_two">
      <!-- PDF and CSV download buttons placed top-right with icons -->
      <div class="pdf-button-container">
        <button class="btn btn-primary" id="pdf-download-btn">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF
        </button>
        <button class="btn btn-secondary" id="csv-download-btn">
            <i class="bi bi-file-earmark-spreadsheet-fill me-2"></i>Download CSV
        </button>
        <a href="exam_timetable.php" class="btn btn-secondary" id="exam-timetable-btn">
            <i class="bi bi-calendar-check me-2"></i>Exam Time Table
        </a>
      </div>

      <h3>Assigned Courses and Halls</h3>
      
      <!-- NEW SCROLLABLE WRAPPER FOR THE TABLE -->
      <div class="table-scroll-container">
        <table id="assigned-table">
          <thead>
            <tr>
              <th>Time</th>
              <th>Monday</th>
              <th>Tuesday</th>
              <th>Wednesday</th>
              <th>Thursday</th>
              <th>Friday</th>
              <th>Saturday</th>
              <th>Sunday</th>
            </tr>
          </thead>
          <tbody>
            <?php for($h = 8; $h <= 17; $h++): ?>
              <tr>
                <td><?php echo sprintf("%02d:00 - %02d:00", $h, $h + 1); ?></td>
                <?php for($d = 0; $d < 7; $d++):
                  $cellKey = "{$h}_{$d}";
                ?>
                  <td class="dropzone" data-hour="<?php echo $h; ?>" data-day="<?php echo $d; ?>">
                    <?php
                    // Render each course item in this cell
                    if (isset($_SESSION['dropped_items'][$cellKey])) {
                        foreach ($_SESSION['dropped_items'][$cellKey] as $itm) {
                            // Show only if it matches current category
                            if ($itm['category'] !== $category) {
                                continue;
                            }

                            // If a specific semester is chosen and the item doesn't match, skip
                            if (!empty($semester) && $itm['semester'] !== $semester) {
                                continue;
                            }

                            // If 3rd/4th year & user selected a dept, check if the item matches or is offered
                            if (($category === 'third_year' || $category === 'fourth_year') && $department !== '') {
                                $off2 = $itm['offered_to'] ?? '';
                                // skip if it's neither from that department nor offered to it
                                if ($itm['department'] !== $department && strpos($off2, $department) === false) {
                                    continue;
                                }
                            }
                            $uid        = htmlspecialchars($itm['id']);
                            $cId        = htmlspecialchars($itm['item']);
                            $cName      = htmlspecialchars($itm['course_name']);
                            $lectName   = htmlspecialchars($itm['lecturer_name']);
                            $hl         = htmlspecialchars($itm['hall']);
                            $hasClash   = !empty($itm['clash']);
                            $clsClass   = $hasClash ? 'clash-indicator' : ''; 
                            $regCount   = (int)($itm['registered_students'] ?? 0);
                            $semest     = htmlspecialchars($itm['semester']);
                            $offerd     = htmlspecialchars($itm['offered_to'] ?? '');
                            $reason     = $itm['clash_reason'] ?? '';

                            // Tooltip info
                            $tt = "Course Name: {$cName}\nLecturer: {$lectName}\nRegistered: {$regCount}\nSemester: {$semest}\n";
                            if ($offerd) {
                                $tt .= "Offered To: {$offerd}\n";
                            }
                            if (($category === 'third_year' || $category === 'fourth_year') && !empty($itm['department'])) {
                                $tt .= "Offered By: " . $itm['department'] . "\n";
                            }
                            if ($hasClash && $reason) {
                                $tt .= "Clash Reason: {$reason}\n";
                            }
                            $tt = nl2br(htmlspecialchars($tt));

                            // Show or block Undo button
                            $showUndo   = true;
                            $draggable  = "true"; 
                            if (!empty($offerd)) {
                                $arr = array_map('trim', explode(',', $offerd));
                                if (in_array($department, $arr)) {
                                    // If the current department *is* the offered_to, we block undo
                                    $showUndo = false;
                                    $draggable = "false";
                                }
                            }
                            echo "<div class='draggable-assigned course-item'
                                       draggable='{$draggable}'
                                       data-id='{$uid}'
                                       data-item='{$cId}'
                                       data-course_name='{$cName}'
                                       data-lecturer_name='{$lectName}'
                                       data-category='{$itm['category']}'
                                       data-semester='{$semest}'
                                       data-hall='{$hl}'
                                       data-clash='".($hasClash ? 'true' : 'false')."'
                                       data-clash_reason='".htmlspecialchars($reason, ENT_QUOTES)."'
                                       data-registered_students='{$regCount}'
                                       data-offered_to='{$offerd}'
                                       data-bs-toggle='tooltip'
                                       data-bs-html='true'
                                       title='{$tt}'>
                                    <span class='{$clsClass}'>{$cId} - {$hl}</span>";
                            
                            // Undo button (only if allowed)
                            if ($showUndo) {
                                echo "<button class='undo-button ".($hasClash ? "undo-error" : "undo-success")."'
                                             data-id='{$uid}'>
                                      <span class='icon'>".($hasClash ? "✗" : "✓")."</span> Undo
                                      </button>";
                            }
                            echo "</div>";
                        }
                    }
                    ?>
                  </td>
                <?php endfor; ?>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
      <!-- end .table-scroll-container -->
    </div>
  </div>
</div>

<script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>
<script>
/**
 * DRAG & DROP LOGIC
 */

// DRAG from side-list
document.querySelectorAll('.draggable-item').forEach(item => {
  item.addEventListener('dragstart', e => {
    const hallSel = item.querySelector('.hall-select');
    if (!hallSel || !hallSel.value) {
      alert("Please select a hall first.");
      e.preventDefault();
      return;
    }
    const payload = {
      from: "side-list",
      item: item.dataset.item,
      course_name: item.dataset.course_name,
      lecturer_name: item.dataset.lecturer_name,
      no_of_hours: item.dataset.no_of_hours,
      category: document.querySelector('#item-list').dataset.category,
      semester: item.dataset.semester,
      hall: hallSel.value,
      registered_students: item.dataset.registered_students
    };
    e.dataTransfer.setData('text/plain', JSON.stringify(payload));
  });
});

// DRAG from timetable
function bindDragStartToAssigned() {
  document.querySelectorAll('.draggable-assigned').forEach(el => {
    if (el.getAttribute('draggable') === 'true') {
      el.addEventListener('dragstart', e => {
        const payload = {
          from:    "timetable",
          id:      el.dataset.id,
          item:    el.dataset.item,
          course_name:   el.dataset.course_name,
          lecturer_name: el.dataset.lecturer_name,
          category: el.dataset.category,
          semester: el.dataset.semester,
          hall:     el.dataset.hall,
          clash:    (el.dataset.clash === 'true'),
          clashReason: el.dataset.clash_reason || '',
          srcHour:  el.parentElement.dataset.hour,
          srcDay:   el.parentElement.dataset.day,
          registered_students: el.dataset.registered_students,
          offered_to: el.dataset.offered_to || ''
        };
        e.dataTransfer.setData('text/plain', JSON.stringify(payload));
      });
    }
  });
}
bindDragStartToAssigned();

// DROP on table cells
document.querySelectorAll('.dropzone').forEach(zone => {
  zone.addEventListener('dragover', e => e.preventDefault());
  zone.addEventListener('drop', e => {
    e.preventDefault();
    const d = e.dataTransfer.getData('text/plain');
    if (!d) return;
    const data = JSON.parse(d);
    const hour = zone.dataset.hour;
    const day  = zone.dataset.day;

    if (data.from === "side-list") {
      dropFromSideList(data, hour, day);
    } else {
      dropFromTimetable(data, hour, day);
    }
  });
});

function dropFromSideList(data, hour, day) {
  const { item, course_name, lecturer_name, category, semester, hall, registered_students } = data;
  // optional "check_clash.php" if you have such logic:
  const urlCheck = `check_clash.php?item=${encodeURIComponent(item)}&hour=${hour}&day=${day}&category=${encodeURIComponent(category)}&hall=${encodeURIComponent(hall)}`;
  fetch(urlCheck)
    .then(r => r.json())
    .then(result => {
      let isClash = false;
      let reason = '';
      if (result.clash) {
        isClash = true;
        reason  = result.reason || 'Clash detected';
      }
      const urlStore = `?item=${encodeURIComponent(item)}`
                     + `&hour=${hour}`
                     + `&day=${day}`
                     + `&category=${encodeURIComponent(category)}`
                     + `&item_semester=${encodeURIComponent(semester)}`
                     + `&hall=${encodeURIComponent(hall)}`
                     + `&clash=${isClash}`
                     + `&clashReason=${encodeURIComponent(reason)}`
                     + `&registered_students=${registered_students}`
                     + `&course_name=${encodeURIComponent(course_name)}`
                     + `&lecturer_name=${encodeURIComponent(lecturer_name)}`;
      fetch(urlStore)
        .then(rr => rr.json())
        .then(s => {
          if(s.success) {
            window.location.reload();
          } else {
            console.error("Failed to add the course");
          }
        });
    })
    .catch(err => console.error("Check clash error:", err));
}

function dropFromTimetable(data, hour, day) {
  const { id, item, course_name, lecturer_name, category, semester, hall, clash, clashReason, srcHour, srcDay } = data;
  if (srcHour === hour && srcDay === day) return; // same cell, do nothing

  // optional "check_clash.php"
  const urlCheck = `check_clash.php?item=${encodeURIComponent(item)}&hour=${hour}&day=${day}&category=${encodeURIComponent(category)}&hall=${encodeURIComponent(hall)}`;
  fetch(urlCheck)
    .then(r => r.json())
    .then(result => {
      let newClash = false;
      let newReason = '';
      if (result.clash) {
        newClash = true;
        newReason = result.reason || 'Clash detected';
      }
      const urlMove = `?move=true`
                    + `&id=${encodeURIComponent(id)}`
                    + `&hour=${hour}`
                    + `&day=${day}`
                    + `&src_hour=${srcHour}`
                    + `&src_day=${srcDay}`
                    + `&category=${encodeURIComponent(category)}`
                    + `&semester=${encodeURIComponent(semester)}`
                    + `&hall=${encodeURIComponent(hall)}`
                    + `&clash=${newClash}`
                    + `&clashReason=${encodeURIComponent(newReason)}`
                    + `&item=${encodeURIComponent(item)}`
                    + `&course_name=${encodeURIComponent(course_name)}`
                    + `&lecturer_name=${encodeURIComponent(lecturer_name)}`;
      fetch(urlMove)
        .then(rr => rr.json())
        .then(mm => {
          if (mm.success) {
            window.location.reload();
          } else {
            console.error("Failed to move course");
          }
        });
    })
    .catch(err => console.error("Check clash for move error:", err));
}

// UNDO
document.addEventListener('click', e => {
  if (
    e.target.classList.contains('undo-button') ||
    (e.target.parentElement && e.target.parentElement.classList.contains('undo-button'))
  ) {
    let btn = e.target;
    if (!btn.classList.contains('undo-button')) {
      btn = e.target.parentElement;
    }
    const id = btn.dataset.id;
    fetch(`?undo=true&id=${encodeURIComponent(id)}`)
      .then(r => r.json())
      .then(d => {
        if (d.error) {
          alert(d.error);
          return;
        }
        window.location.reload();
      })
      .catch(err => console.error("Undo error:", err));
  }
});

// "Refresh List"
document.getElementById('refresh-list-btn').addEventListener('click', () => {
  const url = new URL(window.location.href);
  url.searchParams.set('refresh','true');
  window.location.href = url.toString();
});

// Live search (client-side filter)
document.getElementById('search-input').addEventListener('input', function(){
  const val = this.value.toLowerCase();
  document.querySelectorAll('#item-list .draggable-item').forEach(i => {
    const cid = (i.dataset.item || '').toLowerCase();
    if (cid.includes(val)) {
      i.style.display = "";
    } else {
      i.style.display = "none";
    }
  });
});

// MIN/MAX capacity check for halls
function filterHalls(item) {
  const reg = parseInt(item.dataset.registered_students, 10) || 0;
  const minCb = item.querySelector('.min-checkbox');
  const maxCb = item.querySelector('.max-checkbox');
  const hallSel = item.querySelector('.hall-select');

  const minChecked = minCb.checked;
  const maxChecked = maxCb.checked;

  // Keep current selection if possible
  const currentVal = hallSel.value;
  hallSel.innerHTML = `
    <option value='' selected>Select Hall</option>
    <option value='Online' data-capacity='0'>Online</option>
  `;
  allHalls.forEach(h => {
    if (h.hall_name === 'Online') return;
    let include = true;
    // If "min" is checked => only show halls where capacity >= reg
    if (minChecked) {
      include = include && (h.capacity >= reg);
    }
    // If "max" is checked => only show halls where capacity <= reg
    if (maxChecked) {
      include = include && (h.capacity <= reg);
    }
    if (include) {
      hallSel.innerHTML += `<option value='${h.hall_name}' data-capacity='${h.capacity}'>${h.hall_name}</option>`;
    }
  });
  // If the current selection is still in the new list, keep it
  if ([...hallSel.options].some(o => o.value === currentVal)) {
    hallSel.value = currentVal;
  } else {
    hallSel.value = '';
  }
}
document.querySelectorAll('.draggable-item').forEach(i => {
  const minCb = i.querySelector('.min-checkbox');
  const maxCb = i.querySelector('.max-checkbox');
  minCb.addEventListener('change', () => filterHalls(i));
  maxCb.addEventListener('change', () => filterHalls(i));
  filterHalls(i);
});

// Initialize tooltips
function initTooltips() {
  // remove any leftover bootstrap tooltips
  document.querySelectorAll('.tooltip.show').forEach(t => t.remove());
  const triggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  triggers.map(el => new bootstrap.Tooltip(el));
}
initTooltips();

/**
 * PDF Download Logic (using jsPDF + autoTable)
 */
document.getElementById('pdf-download-btn').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('l', 'pt', 'a4');  // landscape A4

  let title = "Assigned Courses and Halls";
  const selectedYear = document.getElementById('category').selectedOptions[0].textContent;
  title += " - " + selectedYear;
  doc.setFontSize(14);
  doc.text(title, 40, 40);

  // Prepare table data
  const tableData = [];
  const table = document.getElementById('assigned-table');
  const headers = [];
  table.querySelectorAll('thead th').forEach(th => {
    headers.push(th.textContent.trim());
  });
  tableData.push(headers);

  // Iterate through rows
  table.querySelectorAll('tbody tr').forEach(tr => {
    const rowData = [];
    tr.querySelectorAll('td').forEach((td, index) => {
      if (index === 0) {
        // Time column
        rowData.push(td.textContent.trim());
      } else {
        // Days columns
        let cellContent = '';
        td.querySelectorAll('.draggable-assigned').forEach(item => {
          const text = item.querySelector('span').textContent; 
          cellContent += text + "\n";
        });
        cellContent = cellContent.trim();
        rowData.push(cellContent);
      }
    });
    tableData.push(rowData);
  });

  doc.autoTable({
    head: [headers],
    body: tableData.slice(1),
    startY: 60,
    styles: { halign: 'left' },
    headStyles: { fillColor: [102, 126, 234] },
    theme: 'grid'
  });

  doc.save('assigned-courses.pdf');
});

/**
 * CSV Download Logic (for Google Calendar import)
 *
 * The CSV columns are:
 * Subject, Start Date, Start Time, End Date, End Time, Description, Location, All Day Event
 *
 * We assume each timetable cell corresponds to an event.
 * For dates, we calculate the current week's Monday and add an offset.
 */
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

  const table = document.getElementById('assigned-table');
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

      // Find each assigned event in this cell
      const events = cell.querySelectorAll('.draggable-assigned');
      events.forEach(eventEl => {
        // Get data attributes from the event element
        const courseId = eventEl.dataset.item || '';
        const courseName = eventEl.dataset.course_name || '';
        const lecturer = eventEl.dataset.lecturer_name || '';
        const hall = eventEl.dataset.hall || '';
        const clash = eventEl.dataset.clash === 'true' ? 'Clash: ' + (eventEl.dataset.clash_reason || '') : '';
        const regCount = eventEl.dataset.registered_students || '';

        // Construct the Subject and Description fields
        const subject = `${courseId} - ${courseName}`;
        let description = `Lecturer: ${lecturer}; Registered: ${regCount}`;
        if (clash) {
          description += `; ${clash}`;
        }

        const row = [
          subject,
          formattedDate,
          startTime,
          formattedDate,
          endTime,
          description,
          hall,
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

<?php
/** 
 * ======================================================================
 * SAVE Timetable to DB (now includes clash_reason)
 * ======================================================================
 */
function saveTimetableToDB($conn, $user_id, $dropped_items) {
    $conn->begin_transaction();
    try {
        // delete old entries for this user
        $del = $conn->prepare("DELETE FROM saved_timetable WHERE user_id = ?");
        $del->bind_param("i", $user_id);
        $del->execute();
        $del->close();

        // insert new (note the extra clash_reason field)
        $sql = "INSERT INTO saved_timetable 
                (user_id, course_id, hall_name, hour, day, semester, category, clash, clash_reason, offered_to)
                VALUES (?,?,?,?,?,?,?,?,?,?)";
        $st = $conn->prepare($sql);
        // i s s i i s s i s s
        // user_id, course_id, hall_name, hour, day, semester, category, clash, clash_reason, offered_to
        $st->bind_param("issiississ",
            $uid, $cid, $hall, $hr, $dy, $sem, $cat, $cl, $clash_reason, $off
        );

        foreach($dropped_items as $cellKey => $arr) {
            list($hr, $dy) = explode('_', $cellKey);
            $hr = (int)$hr; 
            $dy = (int)$dy;
            foreach ($arr as $itm) {
                $uid          = $user_id;
                $cid          = $itm['item'];
                $hall         = $itm['hall'];
                $sem          = $itm['semester'];
                $cat          = $itm['category'];
                $cl           = !empty($itm['clash']) ? 1 : 0;
                $clash_reason = isset($itm['clash_reason']) ? $itm['clash_reason'] : '';
                $off          = $itm['offered_to'] ?? null;

                if (empty($hall)) {
                    continue;  // skip if no hall
                }
                $st->execute();
            }
        }
        $st->close();
        $conn->commit();
    } catch(\Exception $ex) {
        $conn->rollback();
        error_log("Error saving timetable: " . $ex->getMessage());
    }
}

/**
 * ======================================================================
 * LOAD Timetable from DB into Session (now loads clash_reason)
 * ======================================================================
 */
function loadTimetableFromDB($conn, $user_id) {
    $sql = "SELECT course_id, hall_name, hour, day, semester, category, clash, clash_reason, offered_to
            FROM saved_timetable
            WHERE user_id = ?";
    $st = $conn->prepare($sql);
    $st->bind_param("i", $user_id);
    $st->execute();
    $r = $st->get_result();
    $_SESSION['dropped_items'] = [];

    while($row = $r->fetch_assoc()) {
        $hr = $row['hour'];
        $dy = $row['day'];
        $ck = "{$hr}_{$dy}";
        if (!isset($_SESSION['dropped_items'][$ck])) {
            $_SESSION['dropped_items'][$ck] = [];
        }
        $course_id    = $row['course_id'];
        $hall_name    = $row['hall_name'];
        $sem          = $row['semester'];
        $cat          = $row['category'];
        $clash        = $row['clash'] ? true : false;
        $clash_reason = $row['clash_reason'] ?? '';
        $offered_to   = $row['offered_to'] ?? null;

        // fetch department, course name, lecturer, reg
        $dept = '';
        $cname = '';
        $lname = '';
        $rnum = 0;
        $q = $conn->prepare("SELECT department, course_name, lecturer_name, registered_students
                             FROM `$cat` WHERE course_id=? LIMIT 1");
        if ($q) {
            $q->bind_param("s", $course_id);
            $q->execute();
            $rX = $q->get_result();
            if ($rX->num_rows > 0) {
                $rwX = $rX->fetch_assoc();
                $dept  = htmlspecialchars($rwX['department']);
                $cname = htmlspecialchars($rwX['course_name']);
                $lname = htmlspecialchars($rwX['lecturer_name']);
                $rnum  = (int)$rwX['registered_students'];
            }
            $q->close();
        }

        $unique_id = uniqid('item_', true);
        $_SESSION['dropped_items'][$ck][] = [
            'id'                  => $unique_id,
            'item'                => $course_id,
            'course_name'         => $cname,
            'lecturer_name'       => $lname,
            'category'            => $cat,
            'semester'            => $sem,
            'hall'                => $hall_name,
            'clash'               => $clash,
            'clash_reason'        => $clash_reason,
            'registered_students' => $rnum,
            'department'          => $dept,
            'offered_to'          => $offered_to
        ];
    }
    $st->close();

    // Optionally re-check for updated reasons:
    foreach ($_SESSION['dropped_items'] as $cellKey => $arr) {
        recheckCellClashesInSession($cellKey, $conn);
    }
}

/**
 * ======================================================================
 * RECHECK Cell Clashes In Session
 * 
 * NOTE: We clear old reasons first, then recalculate.
 *       If you prefer to keep the DB-stored reason unless
 *       something changes, adjust accordingly.
 * ======================================================================
 */
function recheckCellClashesInSession($cellKey, $conn) {
    if (!isset($_SESSION['dropped_items'][$cellKey]) || empty($_SESSION['dropped_items'][$cellKey])) {
        return [];
    }

    $items = $_SESSION['dropped_items'][$cellKey];
    // We'll do pairwise comparisons to detect conflicts.

    // Clear old clash flags/reasons
    foreach($items as $idx => $it) {
        $items[$idx]['clash'] = false;
        $items[$idx]['clash_reason'] = '';
    }

    for($i = 0; $i < count($items); $i++) {
        for($j = $i + 1; $j < count($items); $j++) {
            $itemI = $items[$i];
            $itemJ = $items[$j];

            // 0) Skip checking if they are in different semesters
            if ($itemI['semester'] !== $itemJ['semester']) {
                continue;
            }

            // 1) same lecturer?
            if (!empty($itemI['lecturer_name']) && $itemI['lecturer_name'] === $itemJ['lecturer_name']) {
                $reason = "Same lecturer: " . $itemI['lecturer_name']
                        . " (" . $itemI['course_name'] . " vs " . $itemJ['course_name'] . ")";
                $items[$i]['clash'] = true;
                $items[$j]['clash'] = true;
                if (empty($items[$i]['clash_reason'])) {
                    $items[$i]['clash_reason'] = $reason;
                }
                if (empty($items[$j]['clash_reason'])) {
                    $items[$j]['clash_reason'] = $reason;
                }
                continue; 
            }

            // 2) known clash pair in "saved_courses" table?
            $cI = $itemI['item'];
            $cJ = $itemJ['item'];
            $sqlC = "SELECT 1 FROM saved_courses
                     WHERE (row=? AND col=?) OR (row=? AND col=?)
                     LIMIT 1";
            $st = $conn->prepare($sqlC);
            $st->bind_param("ssss", $cI, $cJ, $cJ, $cI);
            $st->execute();
            $rr = $st->get_result();
            if ($rr->num_rows > 0) {
                $reason = "Known clash pair: " . $itemI['course_name'] . " vs " . $itemJ['course_name'];
                $items[$i]['clash'] = true;
                $items[$j]['clash'] = true;
                if (empty($items[$i]['clash_reason'])) {
                    $items[$i]['clash_reason'] = $reason;
                }
                if (empty($items[$j]['clash_reason'])) {
                    $items[$j]['clash_reason'] = $reason;
                }
                $st->close();
                continue;
            }
            $st->close();

            // 3) same lecture hall?
            if (!empty($itemI['hall']) && $itemI['hall'] === $itemJ['hall']  && $itemI['hall'] !== 'Online') {
                if ($itemI['category'] === $itemJ['category']) {
                    $reason = "Same lecture hall (Online) & same year";
                    $items[$i]['clash'] = true;
                    $items[$j]['clash'] = true;
                    if (empty($items[$i]['clash_reason'])) {
                        $items[$i]['clash_reason'] = $reason;
                    }
                    if (empty($items[$j]['clash_reason'])) {
                        $items[$j]['clash_reason'] = $reason;
                    }
                } else {
                    $reason = "Same lecture hall: " . $itemI['hall'];
                    $items[$i]['clash'] = true;
                    $items[$j]['clash'] = true;
                    if (empty($items[$i]['clash_reason'])) {
                        $items[$i]['clash_reason'] = $reason;
                    }
                    if (empty($items[$j]['clash_reason'])) {
                        $items[$j]['clash_reason'] = $reason;
                    }
                }
            }
        }
    }
    // Save back into the session
    $_SESSION['dropped_items'][$cellKey] = $items;

    return $items;
}
?>
