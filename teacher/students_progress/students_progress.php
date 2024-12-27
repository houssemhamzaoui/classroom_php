<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo "Unauthorized access. Please log in as a teacher.";
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', 'hosshoss', 'projet');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the quiz ID and teacher ID from session or URL
$quiz_id = 1; // Replace with dynamic quiz ID if needed
$teacher_id = $_SESSION['user_id']; // Use session variable for the teacher's ID

// Query to fetch student progress data
$query = "
    SELECT 
        c.name AS course_name,
        COUNT(DISTINCT cs.id_student) AS total_students, 
        COUNT(DISTINCT sa.student_id) AS students_took_quiz,
        ROUND(AVG(CASE WHEN ca.is_correct = 1 THEN 1 ELSE 0 END), 2) AS average_score
    FROM 
        courses c
    JOIN 
        courses_student cs ON c.id = cs.id_course
    LEFT JOIN 
        student_answers sa ON sa.quiz_id = ? AND sa.student_id = cs.id_student
    LEFT JOIN 
        choices ca ON sa.choice_id = ca.id
    WHERE 
        c.id_teacher = ? AND sa.quiz_id = ?
    GROUP BY 
        c.id
";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $quiz_id, $teacher_id, $quiz_id); // Bind quiz ID and teacher ID
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $total_students = $row['total_students'];
    $students_took_quiz = $row['students_took_quiz'];
    $average_score = $row['average_score'];
    $course_name = $row['course_name'];
} else {
    // Handle case where no data is found
    echo "No data found for the given quiz.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Progress</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1, h3 {
            color: #4CAF50;
            text-align: center;
        }
        h3 {
            margin-bottom: 10px;
        }
        #progressChart {
            margin: 30px auto;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f9;
        }
    </style>
</head>
<body>

    <h1>Student Progress for Quiz</h1>
    <h3>Course: <?php echo htmlspecialchars($course_name); ?></h3>
    <p>Total Students in Course: <?php echo $total_students; ?></p>
    <p>Students Who Took the Quiz: <?php echo $students_took_quiz; ?></p>
    <p>Average Score: <?php echo $average_score; ?></p>

    <canvas id="progressChart" width="400" height="200"></canvas>

    <script>
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Students', 'Students Took Quiz', 'Average Score'],
                datasets: [{
                    label: 'Quiz Progress',
                    data: [
                        <?php echo $total_students; ?>, 
                        <?php echo $students_took_quiz; ?>, 
                        <?php echo $average_score; ?>
                    ],
                    backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
