<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "hosshoss";
$dbname = "projet";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests
if (isset($_POST['action'])) {
    $userId = intval($_POST['user_id']);
    if ($_POST['action'] === 'confirm') {
        $sql = "UPDATE login SET validated = TRUE WHERE id = $userId";
        $conn->query($sql);
        echo json_encode(['success' => true, 'message' => 'User validated successfully.']);
    } elseif ($_POST['action'] === 'decline') {
        $sql = "DELETE FROM login WHERE id = $userId";
        $conn->query($sql);
        echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
    }
    exit;
}

// Fetch non-validated users
$sql = "SELECT id, username, email, created_at FROM login WHERE validated = FALSE";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handle User Validation</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        button {
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
        }
    </style>
    <script>
        // Function to handle confirm and decline actions
        async function handleAction(userId, action) {
            const response = await fetch('handling.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ user_id: userId, action })
            });

            const result = await response.json();
            if (result.success) {
                alert(result.message);
                // Remove the row from the table
                document.getElementById(`user-row-${userId}`).remove();
            } else {
                alert('An error occurred: ' + (result.message || 'Unknown error'));
            }
        }
    </script>
</head>
<body>
    <h1>Pending User Validations</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="user-row-<?= $row['id'] ?>">
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <button onclick="handleAction(<?= $row['id'] ?>, 'confirm')" style="background-color: green; color: white;">Confirm</button>
                            <button onclick="handleAction(<?= $row['id'] ?>, 'decline')" style="background-color: red; color: white;">Decline</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users awaiting validation.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
