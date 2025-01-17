
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
    <div>
    <form method="POST" class="zina">
                <button type="submit" name="sign_out" class="zina_btn">Sign Out</button>
    </form>
    </div>
</div>
    

<style>
    .zina_btn{
        text-decoration : none;
        border : 2px solid black;
        color:white;
        font-size:1em;
        padding:10px 15px;
        background-color:red;
        margin:0;
    }
    .zina_btn:hover{
        background-color:rgb(136, 4, 4);

    }
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
