<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$username = 'if0_39520049';
$password = 'StudentGroupies'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select approved groupies + current members count
    $stmt = $pdo->prepare("
        SELECT g.id, g.title, g.description, g.max_members, g.subject,
            COALESCE(COUNT(m.id), 0) AS current_members
        FROM groupies g
        LEFT JOIN groupie_members m ON g.id = m.groupie_id
        WHERE g.status = 'approved'
        GROUP BY g.id
        ORDER BY g.created_at DESC
    ");
    $stmt->execute();

    $groupies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($groupies);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
}
?>