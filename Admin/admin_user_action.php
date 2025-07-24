<?php
session_start();

// Protect admin pages
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit;
}

// DB connection
$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$user = 'if0_39520049';
$pass = 'StudentGroupies';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['userId'] ?? null;
        $action = $_POST['action'] ?? null;

        if (!$userId || !$action) {
            throw new Exception('Missing parameters.');
        }

        if ($action === 'activate') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($action === 'deactivate') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        } else {
            throw new Exception('Invalid action.');
        }

        // Redirect back to dashboard
        header("Location: admin_dashboard.php");
        exit;
    } else {
        throw new Exception('Invalid request method.');
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
