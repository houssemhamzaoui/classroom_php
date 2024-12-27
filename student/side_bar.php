
<?php
include "get_courses.php";
// Ensure session is active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo "Unauthorized access. Please log in as a student.";
    exit();
}

// Ensure $courses is defined
if (!isset($courses)) {
    $courses = []; // Default to an empty array if not provided
}
?>

<div class="side_bar">
    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
    <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>
    <h4>Your Courses</h4>
    <?php if (count($courses) > 0): ?>
        <ul>
            <?php foreach ($courses as $course): ?>
                <li>
                    <a href="../courses/courses.php?id=<?php echo urlencode($course['id']); ?>">
                        <?php echo htmlspecialchars($course['name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No courses enrolled.</p>
    <?php endif; ?>
</div>

<style>
    .side_bar {
        width: 20%;
        background: #f4f4f4;
        padding: 10px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }
    .side_bar ul {
        list-style: none;
        padding: 0;
    }
    .side_bar ul li {
        margin: 5px 0;
        font-size: 1em;
    }
    .side_bar ul li a {
        text-decoration: none;
        color: #333;
    }
    .side_bar ul li a:hover {
        color: #1a73e8;
    }
</style>
