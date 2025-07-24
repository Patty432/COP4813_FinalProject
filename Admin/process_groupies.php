<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die("Unauthorized access.");
}

$groupieId = $_POST['groupie_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$groupieId || !in_array($action, ['approve', 'reject'])) {
    die("Invalid request.");
}

$status = $action === 'approve' ? 'approved' : 'rejected';

try {
    $pdo = new PDO("mysql:host=sql308.infinityfree.com;dbname=if0_39520049_student_groupie;charset=utf8mb4", 'if0_39520049', 'StudentGroupies');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("UPDATE groupies SET status = ? WHERE id = ?");
    $stmt->execute([$status, $groupieId]);

    header("Location: admin_dashboard.php");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>