<?php
// Start the session
session_start();

// Check if the teacher is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo "Unauthorized access. Please log in as a teacher.";
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <title>Create Course</title>
</head>
<body>
    <h1>Create a New Course</h1>
    <form action="creer_course.php" method="POST">
        <label for="course_name">Course Name:</label><br>
        <input type="text" id="course_name" name="course_name" required><br><br>
        
        <label for="course_code">Course Code (Optional, Leave Empty for Auto-generated):</label><br>
        <input type="text" id="course_code" name="course_code"><br><br>
        
        <input type="submit" value="Create Course">
    </form>
</body>
</html>
