<?php
// Start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo "Unauthorized access. Please log in as a student.";
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "hosshoss", "projet");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and retrieve the course ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid course ID.";
    exit();
}

$courseId = intval($_GET['id']);
$studentId = $_SESSION['user_id'];

// Query to fetch course details along with teacher's name
$sql = "SELECT c.id, c.name, c.code_cours, c.id_teacher, l.username AS teacher_name
        FROM courses c
        INNER JOIN courses_student cs ON c.id = cs.id_course
        INNER JOIN login l ON c.id_teacher = l.id
        WHERE cs.id_student = ? AND c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $studentId, $courseId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the course exists and the student is enrolled
if ($result->num_rows === 0) {
    echo "Course not found or you are not enrolled in this course.";
    exit();
}

$course = $result->fetch_assoc();
$stmt->close();

// Query to fetch files related to the course and teacher
$fileSql = "SELECT f.id, f.path, f.file_type, f.description, f.created_at 
            FROM files f
            WHERE f.teacher_id = ? AND f.course_id = ?";
$fileStmt = $conn->prepare($fileSql);
$fileStmt->bind_param("ii", $course['id_teacher'], $courseId);
$fileStmt->execute();
$fileResult = $fileStmt->get_result();

// Display course details
echo "<h1>Course Details</h1>";
echo "<p>Course Name: " . htmlspecialchars($course['name']) . "</p>";
echo "<p>Teacher: " . htmlspecialchars($course['teacher_name']) . "</p>";

// Display files
echo "<h2>Course Files</h2>";
if ($fileResult->num_rows > 0) {
    echo "<div style='display: flex; flex-direction: column; gap: 15px;'>";
while ($file = $fileResult->fetch_assoc()) {
    $filePath = htmlspecialchars($file['path']);
    $fileName = basename($filePath);
    $fileType = htmlspecialchars($file['file_type']);
    $description = htmlspecialchars($file['description']);
    $createdAt = htmlspecialchars($file['created_at']);
    
    echo "<div style='border: 1px solid #ccc; padding: 20px; border-radius: 10px; background: #f9f9f9; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); margin-bottom: 20px;'>";
    
    // Display image
    if (strpos($fileType, 'image') === 0) {
        echo "<div style='text-align: center; max-width: 100%;'>
                <img src='$filePath' alt='$fileName' style='width: 100%; height: auto; max-width: 500px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);'></br>
                <a href='$filePath' download='$fileName' style='text-decoration: none; color: #0073e6; font-weight: bold;'>Download Image</a>
              </div>";
    } 
    // Display video
    elseif (strpos($fileType, 'video') === 0) {
        echo "<div style='text-align: center; max-width: 100%;'>
                <video controls style='width: 100%; max-width: 500px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);'>
                    <source src='$filePath' >
                    Your browser does not support the video tag.
                </video>
              </div>";
    } 
    // Display document link
    else {
        echo "<div style='text-align: center;'>
                <a href='$filePath' target='_blank' style='font-size: 16px; color: #0073e6; font-weight: bold; text-decoration: none;'>
                    $fileName
                </a>
              </div>";
    }
    
    echo "<p style='font-size: 14px; color: #555; margin-top: 10px;'>Description: $description</p>";
    echo "<p style='font-size: 14px; color: #555;'>Uploaded on: $createdAt</p>";
    echo "</div>";
}
echo "</div>";

} else {
    echo "<p>No files available for this course.</p>";
}

$fileStmt->close();
$conn->close();
?>
