<?php
// Start the session if not already active
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

// Get the teacher ID from the session
$teacherId = $_SESSION['user_id'];

// Query to get all courses assigned to the teacher
$sqlCourses = "SELECT id, name FROM courses WHERE id_teacher = ?";
$stmtCourses = $conn->prepare($sqlCourses);
$stmtCourses->bind_param("i", $teacherId);
$stmtCourses->execute();
$resultCourses = $stmtCourses->get_result();

$coursesList = [];
if ($resultCourses->num_rows > 0) {
    while ($row = $resultCourses->fetch_assoc()) {
        $coursesList[] = $row;
    }
}
$stmtCourses->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Courses</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for AJAX -->
</head>
<body>
    <div class="container">
        <h1>Welcome, Teacher!</h1>
        <h2>Your Assigned Courses</h2>

        <!-- Dropdown for course selection -->
        <label for="course_id">Select a course:</label>
        <select name="course_id" id="course_id">
            <option value="">All Courses</option>
            <?php foreach ($coursesList as $course): ?>
                <option value="<?php echo htmlspecialchars($course['id']); ?>">
                    <?php echo htmlspecialchars($course['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Table for displaying courses and students -->
        <div id="course-table">
            <p>Please select a course to see the details.</p>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#course_id').change(function () {
                const courseId = $(this).val();

                // Send AJAX request to fetch the course data
                $.ajax({
                    url: 'fetch_course_data.php',
                    method: 'GET',
                    data: { course_id: courseId },
                    success: function (response) {
                        $('#course-table').html(response); // Update the table with fetched data
                    },
                    error: function () {
                        alert('Failed to fetch course data. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
