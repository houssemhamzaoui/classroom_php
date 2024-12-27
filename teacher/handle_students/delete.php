<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user is logged in and has the correct role
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

    // Get POST parameters (course_id and student_id)
    $courseId = isset($_POST['course_id']) ? $_POST['course_id'] : null;
    $studentId = isset($_POST['student_id']) ? $_POST['student_id'] : null;

    // Ensure course_id and student_id are provided
    if ($courseId === null || $studentId === null) {
        echo "Missing course or student ID.";
        exit();
    }

    // Delete the student from the course (removes the record from courses_student table)
    $sql = "DELETE FROM courses_student WHERE id_course = ? AND id_student = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $courseId, $studentId);

    if ($stmt->execute()) {
        // Deletion was successful
        echo "Student successfully removed from the course.";
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the teacher dashboard
    header("Location: handling_students.php");
    exit();
}
?>
