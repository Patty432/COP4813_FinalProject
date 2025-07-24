<?php
session_start();

// Protect page: only logged-in admins allowed
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

    // Fetch all users
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all pending groupie submissions
    $groupieStmt = $pdo->query("SELECT * FROM groupies WHERE status = 'pending' ORDER BY created_at DESC");
    $pendingGroupies = $groupieStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard - User Management</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #ff8c00;
        color: white;
    }
    .btn {
        padding: 5px 10px;
        margin: 0 2px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .activate { background-color: #4CAF50; color: white; }
    .deactivate { background-color: #f44336; color: white; }
    .edit { background-color: #2196F3; color: white; }
    .delete { background-color: #555; color: white; }
</style>
</head>
<body>

<div class="top-bar">
    <div class="admin-info">Logged in as: <?= $adminUsername ?></div>
    <form action="admin_logout.php" method="post" style="margin:0;">
        <button type="submit" class="signout-btn">Sign Out</button>
    </form>
</div>

<a href="admin_analytics.php" style="display:inline-block; margin:10px; background:#2196F3; color:white; padding:10px 15px; border-radius:5px; text-decoration:none;">📊 View Analytics Dashboard</a>

<h1>User Management</h1>

<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>        
            <th>Major</th>          
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['fname']) ?></td>
            <td><?= htmlspecialchars($user['lname']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>   
            <td><?= htmlspecialchars($user['major']) ?></td>  
            <td><?= !empty($user['is_active']) ? 'Active' : 'Inactive' ?></td>
            <td>

                <?php if (!empty($user['is_active'])): ?>
                    <form action="admin_user_action.php" method="post" style="display:inline;">
                        <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                        <input type="hidden" name="action" value="deactivate">
                        <button type="submit" class="btn deactivate">Deactivate</button>
                    </form>
                <?php else: ?>
                    <form action="admin_user_action.php" method="post" style="display:inline;">
                        <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                        <input type="hidden" name="action" value="activate">
                        <button type="submit" class="btn activate">Activate</button>
                    </form>
                <?php endif; ?>

                <form action="admin_edit_user.php" method="get" style="display:inline;">
                    <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn edit">Edit</button>
                </form>

                <form action="admin_user_action.php" method="post" style="display:inline;" onsubmit="return confirm('Delete user? This cannot be undone.')">
                    <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn delete">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h1>Pending Groupie Submissions</h1>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Contact Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($pendingGroupies) === 0): ?>
        <tr><td colspan="5">No groupies pending approval.</td></tr>
    <?php else: ?>
        <?php foreach ($pendingGroupies as $groupie): ?>
            <tr>
                <td><?= htmlspecialchars($groupie['title']) ?></td>
                <td><?= htmlspecialchars($groupie['description']) ?></td>
                <td><?= htmlspecialchars($groupie['contact_email']) ?></td>
                <td>
                    <form action="process_groupies.php" method="post" style="display:inline;">
                        <input type="hidden" name="groupie_id" value="<?= $groupie['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn activate">Approve</button>
                    </form>
                    <form action="process_groupies.php" method="post" style="display:inline;">
                        <input type="hidden" name="groupie_id" value="<?= $groupie['id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn deactivate">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<h1>Create New Groupie</h1>

<form action="admin_submit_groupie.php" method="POST" style="margin-bottom: 2rem;">
    <table>
        <tr>
            <td><label for="title">Groupie Title</label></td>
            <td><input type="text" id="title" name="title" required placeholder="Enter a descriptive title for your study group"></td>
        </tr>

        <tr>
            <td><label for="description">Groupie Description</label></td>
            <td><textarea id="description" name="description" rows="4" required placeholder="Describe what your study group will focus on, meeting frequency, and any specific goals..."></textarea></td>
        </tr>

        <tr>
            <td><label for="max_members">Maximum Number of Members</label></td>
            <td><input type="number" id="max_members" name="max_members" min="1" max="50" required placeholder="Enter maximum capacity"></td>
        </tr>

        <tr>
            <td><label for="subject">Subject Related</label></td>
            <td>
                <select id="subject" name="subject" required>
                    <option value="">-- Select a Subject --</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="Biology">Biology</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Economics">Economics</option>
                    <option value="Psychology">Psychology</option>
                    <option value="Sociology">Sociology</option>
                    <option value="History">History</option>
                    <option value="Philosophy">Philosophy</option>
                    <option value="English">English</option>
                    <option value="Spanish">Spanish</option>
                    <option value="French">French</option>
                    <option value="Political Science">Political Science</option>
                    <option value="Business">Business</option>
                    <option value="Accounting">Accounting</option>
                    <option value="Law">Law</option>
                    <option value="Art">Art</option>
                    <option value="Music">Music</option>
                </select>
            </td>
        </tr>

        <tr>
            <td><label for="contact_email">Email of Groupie Leader</label></td>
            <td><input type="email" id="contact_email" name="contact_email" required placeholder="your.email@example.com"></td>
        </tr>

        <tr>
            <td><label>Groupie Status</label></td>
            <td>
                <div style="background: rgba(74,144,226,0.1); border: 2px solid rgba(74,144,226,0.3); border-radius: 12px; padding: 1rem; color: #4a90e2; font-weight: 600;">
                    Your groupie will be set to "Pending" status and reviewed before being published
                </div>
                <input type="hidden" name="status" value="pending">
            </td>
        </tr>

        <tr>
            <td colspan="2" style="text-align:right;">
                <button type="submit" class="btn activate">Create Groupie</button>
            </td>
        </tr>
    </table>
</form>


</body>
</html>
