<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { display: flex; height: 100vh; }
        .container2 { flex: 1; display: flex; flex-direction: column; }
        .nav_bar { background: #333; color: #fff; padding: 10px; }
        .cours { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }

        .add-course {
            margin: 20px 0;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .add-course h3 {
            margin-bottom: 10px;
        }
        .add-course input {
            padding: 5px;
            margin-right: 10px;
        }
        .add-course button {
            padding: 5px 10px;
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .add-course button:hover {
            background-color: #155ab2;
        }
        .add-course p {
            margin-top: 10px;
            color: #d9534f;
        }
        .container_cours {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .container_cours .course_display {
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            width: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'side_bar.php'; ?> <!-- Include the sidebar -->
        <div class="container2">
            <div class="nav_bar">
                <h1>Student Dashboard</h1>
            </div>
            <div class="cours">
                <h2>Your Courses</h2>
                <?php include 'add_course_form.php'; ?>
                <?php if (count($courses) > 0): ?>
                    <div class="container_cours">
                        <?php foreach($courses as $course): ?>
                            <div class="course_display">
                                <p><strong>ID:</strong> <?php echo htmlspecialchars($course['id']); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($course['name']); ?></p>
                                <a href="../courses/courses.php?id=<?php echo htmlspecialchars($course['id']); ?>">View Course</a>
                                <a href="../courses/view_quiz/view_quiz.php?id=<?php echo htmlspecialchars($course['id']);?>">view quiz</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>You are not enrolled in any courses.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
