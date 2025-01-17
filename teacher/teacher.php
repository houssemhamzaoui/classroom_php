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

if (isset($_POST['sign_out'])) {
    // Clear session data
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page
    header("Location: /projet_web/login/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ3QpWghf2aBzXo1wFzNSKrJYBksrLq+Fs59MI0bGFyIkDoXaMQ7oK0kFphB" crossorigin="anonymous">
    <title>Teacher Dashboard</title>
    <style>
        body {
            background-color: #f4f5f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .navbar {
            text-align:center;
            color: black;
            border-radius: 10px;
            padding: 20px 30px;
        }
        .navbar h1 {
            font-size: 38px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn {
            background-color: #4e73df;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            transition: transform 0.3s, background-color 0.3s;
        }
        .btn:hover {
            background-color: #2e59d9;
            transform: scale(1.1);
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 24px;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding:20px 10px;
        }
        .card-body {
            padding: 30px;
            font-size: 18px;
            line-height: 1.6;
        }
        .action-link {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
            margin-bottom: 15px;
            display: block;
            transition: color 0.3s;
        }
        .action-link:hover {
            color: #2e59d9;
            text-decoration: underline;
        }
        .container {
            margin-top: 50px;
        }
        .sign-out-btn {
            background-color: #dc3545;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .sign-out-btn:hover {
            background-color: #c82333;
        }
        .action-link:active {
            transform: translateY(2px);
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <h1>Teacher Dashboard</h1>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        Your Actions
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><a href="/projet_web/teacher/creer_course/creer.php" class="action-link">Create a Course</a></li>
                            <li><a href="/projet_web/teacher/view_code/view.php" class="action-link">View Courses and Codes</a></li>
                            <li><a href="/projet_web/teacher/handle_students/handling_students.php" class="action-link">Manage Students</a></li>
                            <li><a href="/projet_web/teacher/quiz/quiz.php" class="action-link">Create Quiz</a></li>
                            <li><a href="/projet_web/teacher/teacher_fichiers/dispose_file.php" class="action-link">Dispose a File</a></li>
                            <li><a href="/projet_web/teacher/students_progress/quiz.php" class="action-link">View Progress of Students</a></li>
                        </ul>
                        <form method="POST" class="text-center mt-4">
                            <button type="submit" name="sign_out" class="btn sign-out-btn">Sign Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 Your School Name. All Rights Reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0AqYAK7+K0+2zMeJ9z5Fc5l8uwfGJSW5Afbm4+jo4yheFJX3" crossorigin="anonymous"></script>
</body>
</html>
