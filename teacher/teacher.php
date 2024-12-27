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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Teacher Dashboard</title>
</head>
<body>
    <div class="container">
        <div class="container2">
            <div class="nav_bar">
                <h1>Teacher Dashboard</h1>
            </div>  
            <div class="courses">
                <h2>Your actions</h2>
                <a href="/projet_web/teacher/creer_course/creer.php">create a course</a></br>
                <a href="/projet_web/teacher/view_code/view.php">view courses and codes</a></br>
                <a href="/projet_web/teacher/handle_students/handling_students.php">manage students</a></br>
                <a href="/projet_web/teacher/quiz/quiz.php">create quiz</a></br>
                <a href="/projet_web/teacher/teacher_fichiers/dispose_file.php">dipose a file</a></br>
                <a href="">view progress of students</a>
            </div>
        </div>
    </div>
</body>
</html>
