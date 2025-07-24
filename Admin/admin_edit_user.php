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

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = $_GET['userId'] ?? null;
        if (!$userId) {
            throw new Exception("User ID not specified.");
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            throw new Exception("User not found.");
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['userId'] ?? null;
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $major = $_POST['major'] ?? '';
        $email = $_POST['email'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (!$userId || !$firstName || !$lastName || !$major || !$email) {
            throw new Exception("Please fill in all required fields.");
        }

        $stmt = $pdo->prepare("UPDATE users SET fname = ?, lname = ?, major = ?, email = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $major, $email, $isActive, $userId]);

        header("Location: admin_dashboard.php");
        exit;
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit User</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }
    /* Top bar styles */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #ff8c00;
        color: white;
        padding: 0.75rem 1.5rem;
        font-weight: bold;
        font-size: 1rem;
    }
    .top-bar .admin-info {
        user-select: none;
    }
    .top-bar .signout-btn {
        background-color: #f44336;
        border: none;
        color: white;
        padding: 8px 14px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    .top-bar .signout-btn:hover {
        background-color: #c0392b;
    }

    main {
        max-width: 600px;
        margin: 2rem auto;
        padding: 1rem 2rem;
        background-color: #fff8e1;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h1 {
        color: #ff8c00;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    form label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: bold;
        color: #333;
    }
    form input[type="text"],
    form input[type="email"] {
        width: 100%;
        padding: 8px 10px;
        margin-top: 4px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 1rem;
    }
    form input[type="checkbox"] {
        transform: scale(1.2);
        margin-left: 4px;
        vertical-align: middle;
        cursor: pointer;
    }

    .btn-save {
        background-color: #2196F3;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        margin-top: 1rem;
        display: block;
        width: 100%;
        transition: background-color 0.3s ease;
    }
    .btn-save:hover {
        background-color: #1769aa;
    }

    .back-link {
        display: block;
        margin-top: 1rem;
        text-align: center;
        font-weight: bold;
        color: #ff8c00;
        text-decoration: none;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="top-bar">
    <div class="admin-info">Logged in as: <?= $adminUsername ?></div>
    <form action="admin_logout.php" method="post" style="margin:0;">
        <button type="submit" class="signout-btn">Sign Out</button>
    </form>
</div>

<main>
    <h1>Edit User</h1>
    <form action="admin_edit_user.php" method="post">
        <input type="hidden" name="userId" value="<?= htmlspecialchars($userData['id']) ?>">

        <label for="first_name">First Name:
            <input id="first_name" type="text" name="first_name" value="<?= htmlspecialchars($userData['fname']) ?>" required>
        </label>

        <label for="last_name">Last Name:
            <input id="last_name" type="text" name="last_name" value="<?= htmlspecialchars($userData['lname']) ?>" required>
        </label>

        <label for="major">Major:
            <input id="major" type="text" name="major" value="<?= htmlspecialchars($userData['major']) ?>" required>
        </label>

        <label for="email">Email:
            <input id="email" type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
        </label>

        <label for="is_active">
            Active:
            <input id="is_active" type="checkbox" name="is_active" <?= $userData['is_active'] ? 'checked' : '' ?>>
        </label>

        <button type="submit" class="btn-save">Save Changes</button>
    </form>

    <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>
</main>

</body>
</html>
