<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }
        table {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th {
            background-color: #343a40;
            color: #ffffff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-view {
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .btn-view:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Database connection details
        $host = 'localhost';
        $dbname = 'projet';
        $username = 'root';
        $password = 'hosshoss';

        // Connect to the database using MySQLi
        $conn = new mysqli($host, $username, $password, $dbname);
        session_start();

        // Check connection
        if ($conn->connect_error) {
            die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
        }

        $id_teacher = $_SESSION['user_id'];

        // Query to fetch quizzes
        $sql = "SELECT q.id, q.title 
                FROM quiz q, courses c
                WHERE q.course_id = c.id AND c.id_teacher = $id_teacher";
        $result = $conn->query($sql);

        // Check if there are quizzes
        if ($result->num_rows > 0) {
            echo "<h1>Your Quizzes</h1>";
            echo "<table class='table table-hover table-bordered text-center'>";
            echo "<thead class='table-dark'><tr><th>ID</th><th>Title</th><th>Action</th></tr></thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                // Display each quiz as a row in the table
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td><a href='students_progress.php?id=" . htmlspecialchars($row['id']) . "' class='btn-view'>View</a></td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-warning text-center'>No quizzes available.</div>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
