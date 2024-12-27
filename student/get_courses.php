<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Database connection
$conn = new mysqli("localhost", "root", "hosshoss", "projet");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the student ID from the session
$studentId = $_SESSION['user_id'];

// Query to get courses for the student
$sql = "SELECT c.id, c.name, c.code_cours 
        FROM courses c
        INNER JOIN courses_student cs ON c.id = cs.id_course
        WHERE cs.id_student = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

// Fetch courses
$courses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$stmt->close();
$conn->close();
?>