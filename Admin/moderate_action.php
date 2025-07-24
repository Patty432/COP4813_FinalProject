<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.html");
    exit;
}

$pdo = new PDO("mysql:host=sql308.infinityfree.com;dbname=if0_39520049_student_groupie;charset=utf8mb4", 'if0_39520049', 'StudentGroupies');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$groupieId = $_POST['groupie_id'];
$action = $_POST['action'];

if (!in_array($action, ['approve', 'reject'])) {
    die("Invalid action");
}

$stmt = $pdo->prepare("UPDATE groupies SET status = ? WHERE id = ?");
$stmt->execute([$action, $groupieId]);

header("Location: moderate_groupies.php");
?>
