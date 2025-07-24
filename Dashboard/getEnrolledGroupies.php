<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$host = 'sql308.infinityfree.com';
$dbname = 'if0_39520049_student_groupie';
$user = 'if0_39520049';
$pass = 'StudentGroupies';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "
SELECT 
    g.id, 
    g.subject, 
    g.title, 
    g.description, 
    g.max_members,
    COUNT(gm2.user_id) AS current_members
FROM groupies g
INNER JOIN groupie_members gm ON g.id = gm.groupie_id
LEFT JOIN groupie_members gm2 ON g.id = gm2.groupie_id
WHERE gm.user_id = ?
AND g.status = 'approved'
GROUP BY g.id, g.subject, g.title, g.description, g.max_members
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $groupies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($groupies);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
}
?>
