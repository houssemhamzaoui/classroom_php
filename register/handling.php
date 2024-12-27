<?php
// Database connection parameters
$servername = "localhost"; // Change if necessary
$username = "root";        // Your database username
$password = "hosshoss";            // Your database password
$dbname = "projet";        // Your database name

try {
    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    echo "Connected to database successfully.<br>";

    // Check if the 'users' table exists
    $result = $conn->query("SHOW TABLES LIKE 'login'");
    if ($result->num_rows == 0) {
        throw new Exception("Table 'users' does not exist in the database.");
    }

    // Retrieve form data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['Email']);
        $password = htmlspecialchars($_POST['password']);
        $cpassword = htmlspecialchars($_POST['Cpassword']);
        $role = htmlspecialchars($_POST['Role']);

        // Check if passwords match
        if ($password !== $cpassword) {
            throw new Exception("Passwords do not match!");
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the 'users' table
        $sql = "INSERT INTO login (username, email, password, role) 
                VALUES ('$username', '$email', '$hashed_password', '$role')";

        if (!$conn->query($sql)) {
            throw new Exception("SQL error: " . $conn->error);
        }

        echo "Registration successful!";
    }
} catch (Exception $e) {
    // Display the error message
    echo "Error: " . $e->getMessage();
} finally {
    // Close the connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style3.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="popup" id="popup">
        <div class="container">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
            <h2>Registretion successful</h2>
                <p>wait for our email the admin will aprove your registration in less than 24 hours</p>
            <a href="../login/login.html    "><button >ok</button></a>
        </div>
    </div>
</body>
</html>