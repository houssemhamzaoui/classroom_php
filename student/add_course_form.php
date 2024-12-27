<?php
// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_code'])) {
    $courseCode = trim($_POST['course_code']);
    
    // Validate course code
    if (!empty($courseCode)) {
        // Database connection
        $conn = new mysqli("localhost", "root", "hosshoss", "projet");

        // Check for connection errors
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to check if the course exists and is not already enrolled by the student
        $studentId = $_SESSION['user_id'];
        $sql = "SELECT id FROM courses WHERE code_cours = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $courseCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $course = $result->fetch_assoc();
            $courseId = $course['id'];

            // Check if the student is already enrolled
            $checkEnrollmentSql = "SELECT * FROM courses_student WHERE id_student = ? AND id_course = ?";
            $checkStmt = $conn->prepare($checkEnrollmentSql);
            $checkStmt->bind_param("ii", $studentId, $courseId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows === 0) {
                // Enroll the student in the course
                $enrollSql = "INSERT INTO courses_student (id_student, id_course) VALUES (?, ?)";
                $enrollStmt = $conn->prepare($enrollSql);
                $enrollStmt->bind_param("ii", $studentId, $courseId);
                if ($enrollStmt->execute()) {
                    $message = "You have been successfully enrolled in the course.";
                } else {
                    $message = "Error enrolling in the course. Please try again.";
                }
                $enrollStmt->close();
            } else {
                $message = "You are already enrolled in this course.";
            }

            $checkStmt->close();
        } else {
            $message = "Invalid course code.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $message = "Course code cannot be empty.";
    }
}
?>

<div class="add-course">
    <h3>Add a Course</h3>
    <form method="POST" action="">
        <input type="text" name="course_code" placeholder="Enter Course Code" required>
        <button type="submit">Add Course</button>
    </form>
    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</div>
