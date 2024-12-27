<?php
var_dump($_POST);
// Start the session
session_start();
// Connect to the database
$conn = new mysqli("localhost", "root", "hosshoss", "projet");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input fields
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        // Get the submitted username and password
        $username = $conn->real_escape_string($_POST["username"]);
        $password = $_POST["password"];

        // Check if the user exists in the admin table
        $adminQuery = "SELECT * FROM admin WHERE username = '$username'";
        $adminResult = $conn->query($adminQuery);

        if ($adminResult && $adminResult->num_rows > 0) {
            $admin = $adminResult->fetch_assoc();

            // Verify the password
            if ($password==$admin['password']) {
                // Redirect to the admin interface
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'admin';
                header("Location: ../admin/handling_quest/handling.php");
                exit();
            } else {
                echo "Error: Invalid password for admin.";
                exit();
            }
        }

        // Check if the user exists in the login table
        $userQuery = "SELECT * FROM login WHERE username = '$username'";
        $userResult = $conn->query($userQuery);

        if ($userResult && $userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
            // Check if the account is validated by the admin

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Redirect based on the user's role
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id']=$user["id"];
                if (!$user['validated']) {
                    echo "Error: Your account has not been validated by the admin.";
                    exit();
                }

                if ($user['role'] == "teacher") {
                    header("Location: ../teacher/teacher.php");
                    exit();
                } elseif ($user['role'] == "student") {
                    header("Location: ../student/student.php");
                    exit();
                } else {
                    echo "Error: Undefined role.";
                    exit();
                }
            } else {
                echo "Error: Invalid password.";
                exit();
            }
        } else {
            echo "Error: Username not found.";
            exit();
        }
    } else {
        echo "Error: Please fill in both username and password.";
        exit();
    }
} else {
    echo "Error: Invalid request method.";
    exit();
}

// Close the database connection
$conn->close();
?>
