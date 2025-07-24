<?php
session_start();

// Show PHP errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection variables
$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$user = 'if0_39520049';
$pass = 'StudentGroupies';

try {
    // Create PDO instance with error mode set
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve and trim form data
$adminUsername = trim($_POST['admin_username'] ?? '');
$adminPassword = trim($_POST['admin_password'] ?? '');

// Basic validation
if (empty($adminUsername) || empty($adminPassword)) {
    echo "Please fill in both username and password.";
    exit;
}

try {
    // Prepare and execute select query
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$adminUsername]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($adminPassword, $admin['password'])) {
        // Password verified - start session and redirect
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $adminUsername;

        header("Location: ../Admin/admin_dashboard.php");
        exit;
    } else {
        // Invalid credentials - redirect to login with error
        header("Location: admin_login.html?error=1");
        exit;
    }
} catch (PDOException $e) {
    echo "Login error: " . $e->getMessage();
}
