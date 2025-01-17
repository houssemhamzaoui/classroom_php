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
$quiz_id =$_GET['id']; // Replace with dynamic quiz ID if needed
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h1, h3 {
            text-align: center;
            color: #343a40;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .stats {
            text-align: center;
            margin-bottom: 30px;
        }
        .stats p {
            font-size: 1.2rem;
            margin: 0;
        }
        .stats span {
            font-weight: bold;
            color: #4CAF50;
        }
        .chart-container {
            margin: 20px auto;
            width: 100%;
            max-width: 600px;
        }
        table {
            margin-top: 30px;
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: center;
            padding: 12px;
        }
        th {
            background-color: #343a40;
            color: #ffffff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Student Progress for Quiz</h1>
    <h3>Course: <span><?php echo htmlspecialchars($course_name); ?></span></h3>
    
    <div class="stats">
        <p>Total Students in Course: <span><?php echo $total_students; ?></span></p>
        <p>Students Who Took the Quiz: <span><?php echo $students_took_quiz; ?></span></p>
        <p>Average Score: <span><?php echo $average_score; ?></span></p>
    </div>

    <div class="chart-container">
        <canvas id="progressChart"></canvas>
    </div>

    <!-- Optional Summary Table -->
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Students</td>
                <td><?php echo $total_students; ?></td>
            </tr>
            <tr>
                <td>Students Who Took Quiz</td>
                <td><?php echo $students_took_quiz; ?></td>
            </tr>
            <tr>
                <td>Average Score</td>
                <td><?php echo $average_score; ?></td>
            </tr>
        </tbody>
    </table>
</div>

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
                backgroundColor: ['rgba(75, 192, 192, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 99, 132, 0.7)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
