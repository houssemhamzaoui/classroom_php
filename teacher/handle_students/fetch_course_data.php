<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo "Unauthorized access.";
    exit();
}

$conn = new mysqli("localhost", "root", "hosshoss", "projet");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$teacherId = $_SESSION['user_id'];
$selectedCourseId = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if ($selectedCourseId) {
    $sql = "SELECT C.id, C.name AS course_name, C.code_cours, L.username AS student_name, L.email AS student_email
            FROM courses C
            JOIN courses_student S ON C.id = S.id_course
            JOIN login L ON S.id_student = L.id
            WHERE C.id = ? AND C.id_teacher = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $selectedCourseId, $teacherId);
} else {
    $sql = "SELECT C.id, C.name AS course_name, C.code_cours, L.username AS student_name, L.email AS student_email
            FROM courses C
            JOIN courses_student S ON C.id = S.id_course
            JOIN login L ON S.id_student = L.id
            WHERE C.id_teacher = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacherId);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Student Name</th>
                <th>Student Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['code_cours']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_email']); ?></td>
                    <td>
                        <!-- Delete button -->
                        <form action="delete.php" method="POST">
                            <button type="submit" name="action" value="delete_student" class="btn delete-btn">Delete Student</button>
                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($row['student_name']); ?>">
                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No students found for the selected course.</p>
<?php endif;

$stmt->close();
$conn->close();
?>
