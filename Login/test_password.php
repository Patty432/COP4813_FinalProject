<?php
$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$user = 'if0_39520049';
$pass = 'StudentGroupies';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database '$dbname' successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
