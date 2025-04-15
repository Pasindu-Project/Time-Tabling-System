<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // or require 'db.php';

/**
 * check_clash.php?item=PH1007&hour=8&day=0&category=first_year&hall=KGH
 *
 * Param details:
 *  - item:      the course_id being dragged (e.g. "PH1007")
 *  - hour, day: the cell slot
 *  - category:  the DB table or "year" we check (e.g. "first_year")
 *  - hall:      the lecture hall selected for the course (e.g. "KGH")
 *
 * We look in $_SESSION['dropped_items'][$cellKey] for existing items in that cell
 * and compare them to the NEW course being dropped in. The checks are:
 *   1) Same lecturer
 *   2) Known clash pair (from `saved_courses` table)
 *   3) Same lecture hall
 *
 * NOTE: This version DOES NOT SKIP if they are different semesters.
 * It will check for a clash no matter what.
 */

// 1) Check required GET params
if (!isset($_GET['item'], $_GET['hour'], $_GET['day'], $_GET['category'], $_GET['hall'])) {
    // If any param is missing, we'll just return "no clash"
    echo json_encode(["clash" => false]);
    exit;
}

$item     = $_GET['item'];      // e.g. "PH1007"
$hour     = $_GET['hour'];      // e.g. "8"
$day      = $_GET['day'];       // e.g. "0"
$category = $_GET['category'];  // e.g. "first_year"
$hall     = $_GET['hall'];      // e.g. "KGH"

// 2) Build cell key
$cellKey = "{$hour}_{$day}";

// 3) If there's nothing in that cell yet, there's obviously no clash
if (!isset($_SESSION['dropped_items'][$cellKey]) || empty($_SESSION['dropped_items'][$cellKey])) {
    echo json_encode(["clash" => false]);
    exit;
}

// 4) Connect to DB
/*$host     = 'localhost';
$username = 'root';
$password = '';
$dbname   = 'test';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    // If DB fails, fallback to "no clash"
    echo json_encode(["clash" => false]);
    exit;
}*/

// 5) Lookup full info for the *new* course, including lecturer & semester
$sqlNew = "SELECT lecturer_name, course_name, semester 
           FROM {$category} 
           WHERE course_id = ? 
           LIMIT 1";
$stmtNew = $conn->prepare($sqlNew);
$stmtNew->bind_param("s", $item);
$stmtNew->execute();
$resNew = $stmtNew->get_result();
if ($resNew->num_rows <= 0) {
    // If not found in DB => we can't do a thorough check; default to no clash
    echo json_encode(["clash" => false]);
    exit;
}
$newRow        = $resNew->fetch_assoc();
$newLecturer   = $newRow['lecturer_name'] ?? '';
$newCourseName = $newRow['course_name']   ?? $item;
$newSemester   = $newRow['semester']      ?? '';
$stmtNew->close();

// 6) For each existing item in the cell, do the checks
$existingItems = $_SESSION['dropped_items'][$cellKey];

foreach ($existingItems as $exist) {
    $existCourseId = $exist['item'];
    $existCat      = $exist['category'];
    $existHall     = $exist['hall'];
    
    // Optionally retrieve the existing course's semester if needed
    // (We don't skip if different; we STILL do all checks.)
    $sqlExist = "SELECT lecturer_name, course_name, semester
                 FROM {$existCat}
                 WHERE course_id = ?
                 LIMIT 1";
    $stmtE = $conn->prepare($sqlExist);
    $stmtE->bind_param("s", $existCourseId);
    $stmtE->execute();
    $resE = $stmtE->get_result();
    if ($resE->num_rows > 0) {
        $rowE          = $resE->fetch_assoc();
        $existLect     = $rowE['lecturer_name'] ?? '';
        $existName     = $rowE['course_name']   ?? $existCourseId;
        $existSemester = $rowE['semester']      ?? '';

        // -------------------------------------
        // 1) Check if they have the same lecturer
        // -------------------------------------
        if (!empty($newLecturer) && $newLecturer === $existLect) {
            $reason = "Same lecturer: {$newLecturer} "
                    . "({$newCourseName} vs {$existName})";
            echo json_encode(["clash" => true, "reason" => $reason]);
            $stmtE->close();
            $conn->close();
            exit;
        }

        // -------------------------------------
        // 2) Check if known clash pair from `saved_courses`
        //    (like a table where you store specific "X vs Y" conflicts)
        // -------------------------------------
        $sqlClash = "
            SELECT 1
            FROM saved_courses
            WHERE (row = ? AND col = ?)
               OR (row = ? AND col = ?)
            LIMIT 1
        ";
        $stmtC = $conn->prepare($sqlClash);
        $stmtC->bind_param("ssss", $item, $existCourseId, $existCourseId, $item);
        $stmtC->execute();
        $resC = $stmtC->get_result();
        if ($resC->num_rows > 0) {
            // We found a row that says (PH1005, PH1007) or (PH1007, PH1005) is a clash
            $reason = "{$newCourseName} vs {$existName} => known clash pair (saved_courses)";
            echo json_encode(["clash" => true, "reason" => $reason]);
            $stmtC->close();
            $stmtE->close();
            $conn->close();
            exit;
        }
        $stmtC->close();

        // -------------------------------------
        // 3) Same lecture hall?
        // -------------------------------------
        if (!empty($hall) && $hall === $existHall) {
            // Some logic treat "Online" differently, but here we treat
            // ANY identical hall as a potential clash
            $reason = "Same lecture hall: {$hall} for "
                    . "{$newCourseName} and {$existName}";
            echo json_encode(["clash" => true, "reason" => $reason]);
            $stmtE->close();
            $conn->close();
            exit;
        }

        // -------------------------------------------------
        // OPTIONAL: If you want to do something about
        // semesters (for instance, treat only same-semester
        // items as a clash or the opposite), you would place
        // that logic here. But for now, we do NOT skip if
        // they're different. We check all items.
        // -------------------------------------------------
    }
    $stmtE->close();
}

// 7) If we get here, none of the checks triggered => no clash
$conn->close();
echo json_encode(["clash" => false]);
exit;
