<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['groupId'])) {
    echo json_encode(['success' => false, 'error' => 'Missing groupId']);
    exit;
}

$userId = $_SESSION['user_id'];
$groupId = (int)$data['groupId'];

$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$user = 'if0_39520049';
$pass = 'StudentGroupies';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete the record
    $stmt = $pdo->prepare("DELETE FROM groupie_members WHERE user_id = ? AND groupie_id = ?");
    $stmt->execute([$userId, $groupId]);

    // Check if any rows were deleted to confirm success
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'You were not a member of this group']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>