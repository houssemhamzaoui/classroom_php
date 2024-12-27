<?php
// Start session if not already active
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
$course_id=$_GET['id'];
// Prepare SQL query to fetch all quizzes that are still active (deadline not reached)
$sql = "
    SELECT id, title, deadline 
    FROM quiz
    WHERE deadline > NOW() and course_id=$course_id
";

$result = $conn->query($sql);

// Check if there are any active quizzes
if ($result->num_rows === 0) {
    echo "No active quizzes found.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quizzes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .quiz-list-container {
            max-width: 800px;
            margin: auto;
        }
        .quiz-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .quiz-card h2 {
            margin: 0 0 10px;
        }
        .quiz-card p {
            margin: 5px 0;
        }
        .quiz-card button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .quiz-card button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="quiz-list-container">
        <h1>Available Quizzes</h1>

        <?php while ($quiz = $result->fetch_assoc()): ?>
            <div class="quiz-card">
                <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($quiz['deadline']); ?></p>
                <form action="../../student/quiz/take_quiz.php" method="GET">
                    <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz['id']); ?>">
                    <button type="submit">Take Quiz</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
