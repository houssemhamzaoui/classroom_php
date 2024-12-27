<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

var_dump($_SESSION);
var_dump($_FILES);

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

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (
        !isset($_POST['course_id']) || 
        !is_numeric($_POST['course_id']) || 
        !isset($_FILES['file']) || 
        !isset($_POST['file_type']) || 
        !isset($_POST['description'])
    ) {
        echo "Invalid input.";
        exit();
    }

    $teacherId = $_SESSION['user_id'];
    $courseId = intval($_POST['course_id']);
    $fileType = $_POST['file_type'];
    $description = trim($_POST['description']);
    $file = $_FILES['file'];

    // Define allowed file types
    $allowedTypes = [
        'file' => ['application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'image' => ['image/jpeg', 'image/png', 'image/gif'],
        'video' => ['video/mp4', 'video/x-msvideo', 'video/mpeg']
    ];

    // Validate file type
    if (!array_key_exists($fileType, $allowedTypes) || !in_array($file['type'], $allowedTypes[$fileType])) {
        echo "Unsupported file type. Please upload a valid $fileType.";
        exit();
    }

    // Fetch course details
    $stmt = $conn->prepare("SELECT name FROM courses WHERE id = ? AND id_teacher = ?");
    $stmt->bind_param("ii", $courseId, $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Course not found or you are not authorized for this course.";
        exit();
    }

    $course = $result->fetch_assoc();
    $courseName = $course['name'];
    $teacherName = $_SESSION['username']; // Assuming username is stored in the session

    // Create directory for uploads
    $directory = "../../server/" . $courseName . "_" . $teacherId;
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $directory_access = "../server/" . $courseName . "_" . $teacherId;
    $file_access = $directory_access . "/" . basename($file['name']);
    
    // Save file
    $filePath = $directory . "/" . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Save file info to the database
        $stmt = $conn->prepare("INSERT INTO files (teacher_id, course_id, path, file_type, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisss", $teacherId, $courseId, $file_access, $fileType, $description);
        if ($stmt->execute()) {
            echo "File uploaded successfully!";
        } else {
            echo "Failed to save file details in the database.";
        }
        $stmt->close();
    } else {
        echo "Failed to upload file.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispose File</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS -->
</head>
<body>
    <div class="container2">
        <h1>Upload a File, Image, or Video</h1>
        <form action="dispose_file.php" method="post" enctype="multipart/form-data">
            <label for="course_id">Select Course:</label>
            <select name="course_id" id="course_id" required>
                <?php
                $conn = new mysqli("localhost", "root", "hosshoss", "projet");

                // Check for connection errors
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch courses for the teacher
                $stmt = $conn->prepare("SELECT id, name FROM courses WHERE id_teacher = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </select>
            <br><br>
            <label for="file_type">File Type:</label>
            <select name="file_type" id="file_type" required>
                <option value="file">Document</option>
                <option value="image">Image</option>
                <option value="video">Video</option>
            </select>
            <br><br>
            <label for="file">Choose File:</label>
            <input type="file" name="file" id="file" required>
            <br><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="4" cols="50" placeholder="Provide a brief description of the file"></textarea>
            <br><br>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
