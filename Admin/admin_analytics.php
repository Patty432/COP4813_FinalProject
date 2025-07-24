<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit;
}

$adminUsername = htmlspecialchars($_SESSION['admin_username'] ?? '');

$host = 'sql308.infinityfree.com'; // infinity free SQL hostname 
$dbname = 'if0_39520049_student_groupie'; // infinity free MySQL DB name
$user = 'if0_39520049'; // infinity free MySQL username
$pass = 'StudentGroupies'; // database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total Users
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // Active vs Inactive Users
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
    $inactiveUsers = $totalUsers - $activeUsers;

    // Groupies Count and Categories
    $totalGroupies = $pdo->query("SELECT COUNT(*) FROM groupies")->fetchColumn();

    $categoryCountsStmt = $pdo->query("SELECT subject, COUNT(*) as count FROM groupies GROUP BY subject");
    $categoryCounts = $categoryCountsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Groupie Status Counts (Pending, Approved, Rejected)
    $statusCountsStmt = $pdo->query("SELECT status, COUNT(*) as count FROM groupies GROUP BY status");
    $statusCounts = $statusCountsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // User Registrations per Month (for past 6 months)
    $monthlyRegistrationsStmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count
        FROM users
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month
    ");
    $monthlyRegistrations = $monthlyRegistrationsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial; margin: 20px; }
        h2 { color: #ff8c00; }
        .stat-boxes { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .box {
            background-color: #ffe0b2;
            padding: 20px;
            border-radius: 8px;
            flex: 1 1 200px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        canvas { max-width: 700px; margin: 30px auto; display: block; }
        .back-link {
            margin-top: 20px;
            display: inline-block;
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-link:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<h1>Analytics Dashboard</h1>

<div class="stat-boxes">
    <div class="box"><strong>Total Users</strong><br><?= $totalUsers ?></div>
    <div class="box"><strong>Active Users</strong><br><?= $activeUsers ?></div>
    <div class="box"><strong>Inactive Users</strong><br><?= $inactiveUsers ?></div>
    <div class="box"><strong>Total Groupies</strong><br><?= $totalGroupies ?></div>
</div>

<h2>User Registrations (Past 6 Months)</h2>
<canvas id="userChart"></canvas>

<h2>Groupie Categories</h2>
<canvas id="categoryChart"></canvas>

<h2>Groupie Status</h2>
<canvas id="statusChart"></canvas>

<a href="admin_dashboard.php" class="back-link">← Back to Admin Dashboard</a>

<script>
    // Monthly registration chart
    const userCtx = document.getElementById('userChart').getContext('2d');
    const userChart = new Chart(userCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthlyRegistrations, 'month')) ?>,
            datasets: [{
                label: 'Registrations',
                data: <?= json_encode(array_column($monthlyRegistrations, 'count')) ?>,
                backgroundColor: 'rgba(255, 140, 0, 0.2)',
                borderColor: '#ff8c00',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Groupie category chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($categoryCounts)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($categoryCounts)) ?>,
                backgroundColor: ['#FF8C00', '#FFA500', '#FFD700', '#FFE4B5', '#FF6347', '#FFA07A'],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Groupie status chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($statusCounts)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($statusCounts)) ?>,
                backgroundColor: ['#4CAF50', '#f44336', '#ff9800'], // Approved, Rejected, Pending
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

</body>
</html>
