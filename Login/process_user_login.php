<?php
session_start();

// Database credentials
$servername = "sql308.infinityfree.com";
$db_username = "if0_39520049";
$db_password = "StudentGroupies"; 
$db_name = "if0_39520049_student_groupie";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data and sanitize
$username = trim(strtolower($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    header("Location: ../Login/login_error.php?error=empty");
    exit;
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Check password 
    if (password_verify($password, $user['password'])) {
        // Credentials are correct, set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['email'];

        // Redirect to dashboard
        header("Location: ../Dashboard/index.html");
        exit;
    } else {
        // Incorrect password
        header("Location: login.html?error=invalid");
        exit;
    }
} else {
    // User not found
    header("Location: login.html?error=invalid");
    exit;
}

$stmt->close();
$conn->close();
?>
