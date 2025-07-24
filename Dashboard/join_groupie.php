<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['groupie_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing groupie_id']);
    exit;
}
$groupieId = (int)$input['groupie_id'];

$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$username = 'if0_39520049';
$password = 'StudentGroupies';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the groupie exists and is approved
    $stmt = $pdo->prepare("SELECT max_members FROM groupies WHERE id = ? AND status = 'approved'");
    $stmt->execute([$groupieId]);
    $groupie = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$groupie) {
        http_response_code(404);
        echo json_encode(['error' => 'Groupie not found or not approved']);
        exit;
    }

    // Count current members
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM groupie_members WHERE groupie_id = ?");
    $stmt->execute([$groupieId]);
    $count = (int)$stmt->fetchColumn();

    if ($count >= $groupie['max_members']) {
        http_response_code(409);
        echo json_encode(['error' => 'Groupie is full']);
        exit;
    }

    // Check if user already joined
    $stmt = $pdo->prepare("SELECT 1 FROM groupie_members WHERE groupie_id = ? AND user_id = ?");
    $stmt->execute([$groupieId, $userId]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'You have already joined this groupie']);
        exit;
    }

    // Insert membership
    $stmt = $pdo->prepare("INSERT INTO groupie_members (groupie_id, user_id) VALUES (?, ?)");
    $stmt->execute([$groupieId, $userId]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
?>