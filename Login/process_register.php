<?php
// Show PHP errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection using MySQL (not SQLite)
$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$user = 'if0_39520049';
$pass = 'StudentGroupies'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve form data
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$major = $_POST['major'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($firstName) || empty($lastName) || empty($major) || empty($email) || empty($password)) {
    echo "Please fill in all required fields.";
    exit;
}

// Hash the password securely before storing
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Match the correct column order: fname, lname, major, email, password, is_active
    $stmt = $pdo->prepare("INSERT INTO users (fname, lname, major, email, password, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->execute([$firstName, $lastName, $major, $email, $hashedPassword]);

    // Redirect to success page
    header("Location: success_register.html");
    exit;
} catch (PDOException $e) {
    echo "Error inserting into database: " . $e->getMessage();
}
?>
