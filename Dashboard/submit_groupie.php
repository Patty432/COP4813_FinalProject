<?php
// Show errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB config
$host = "sql308.infinityfree.com";
$user = "if0_39520049";
$password = "StudentGroupies";
$dbname = "if0_39520049_student_groupie";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$title = $_POST['title'];
$description = $_POST['description'];
$max_members = intval($_POST['max_members']);
$subject = $_POST['subject'];
$contact_email = $_POST['contact_email'];
$status = $_POST['status'];

// Prepare SQL to insert record into groupies table
$sql = "INSERT INTO groupies (title, description, max_members, subject, contact_email, status)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ssisss", $title, $description, $max_members, $subject, $contact_email, $status);

// Execute and display message
if ($stmt->execute()) {
    echo "<h2>Groupie successfully added!</h2>";
    echo "<p><a href='index.html'>Return to Home</a></p>";
} else {
    echo "<h2>Error inserting data: " . $stmt->error . "</h2>";
    echo "<p><a href='index.html'>Return to Home</a></p>";
}

$stmt->close();
$conn->close();
?>
