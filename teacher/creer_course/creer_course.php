<?php
// Start the session
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo "Unauthorized access. Please log in as a teacher.";
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "hosshoss", "projet");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacherId = $_SESSION['user_id'];
    $courseName = $_POST['course_name'];
    $courseCode = $_POST['course_code'];

    // Check if the course name already exists
    $nameCheckSql = "SELECT id FROM courses WHERE name = ?";
    $nameCheckStmt = $conn->prepare($nameCheckSql);
    $nameCheckStmt->bind_param("s", $courseName);
    $nameCheckStmt->execute();
    $nameCheckResult = $nameCheckStmt->get_result();

    if ($nameCheckResult->num_rows > 0) {
        echo "Course name already exists. Please choose a different name.";
        exit();
    }

    // If no course code is provided, generate a unique code
    if (empty($courseCode)) {
        // Generate a unique code (e.g., using a combination of teacher ID and current timestamp)
        $courseCode = 'C' . $teacherId . '-' . time();
    }

    // Check if the course code already exists
    $codeCheckSql = "SELECT id FROM courses WHERE code_cours = ?";
    $codeCheckStmt = $conn->prepare($codeCheckSql);
    $codeCheckStmt->bind_param("s", $courseCode);
    $codeCheckStmt->execute();
    $codeCheckResult = $codeCheckStmt->get_result();

    if ($codeCheckResult->num_rows > 0) {
        echo "Course code already exists. Please choose a different code.";
        exit();
    }

    // Insert the new course into the database
    $insertSql = "INSERT INTO courses (name, code_cours, id_teacher) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ssi", $courseName, $courseCode, $teacherId);
    
    if ($insertStmt->execute()) {
        echo "Course created successfully! and this your courseCode $courseCode";
    } else {
        echo "Error creating course: " . $conn->error;
    }

    // Close the prepared statements
    $nameCheckStmt->close();
    $codeCheckStmt->close();
    $insertStmt->close();
}

// Close the database connection
$conn->close();
?>
