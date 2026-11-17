<?php
// ============================================================
//  Smart Sweep — Users page (admin only).
//
//  The table is read live from the users table — so a
//  resident who registers on signup.php appears here at
//  once. The four numbers at the top are counted from it.
// ============================================================

require 'includes/auth.php';

$totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$residents  = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Resident'")->fetchColumn();
$staff      = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role <> 'Resident'")->fetchColumn();
$active     = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'Active'")->fetchColumn();

$users = $pdo->query('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users — Smart Sweep</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="page">

    <!-- The left side menu (same on every page) -->
    <div class="sidebar">
        <div class="logo">
            <div class="logo-box">S</div>
            <div class="logo-name">Smart Sweep</div>
            <div class="logo-small">Garbage Management</div>
        </div>
        <div class="menu">
            <div class="menu-title">MENU</div>
            <a href="dashboard.php">Dashboard</a>
            <a href="about.php">About</a>
            <a href="bins.php">Smart Bins</a>
            <a href="alerts.php">Alerts <span class="count"><?php echo $alertCount; ?></span></a>
            <a href="routes.php">Routes</a>
            <a href="reports.php">Reports</a>
            <a href="users.php" class="active">Users</a>
        </div>
        <div class="logout">
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <div class="main">
        <div class="top">
            <div class="user"><?php echo htmlspecialchars($_SESSION['admin_name']); ?><small>Administrator</small></div>
            <h1>Users</h1>
            <div class="date"><?php echo date('l, j F Y'); ?></div>
        </div>

        <div class="content">

            <!-- The four number cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-title">TOTAL USERS</div>
                    <div class="number"><?php echo $totalUsers; ?></div>
                    <div class="note">Everyone in the users table</div>
                </div>
                <div class="card">
                    <div class="card-title">RESIDENTS</div>
                    <div class="number"><?php echo $residents; ?></div>
                    <div class="note">Registered households</div>
                </div>
                <div class="card">
                    <div class="card-title">STAFF</div>
                    <div class="number"><?php echo $staff; ?></div>
                    <div class="note">Dispatchers, drivers, accountants, administrators</div>
                </div>
                <div class="card">
                    <div class="card-title">ACTIVE</div>
                    <div class="number"><?php echo $active; ?></div>
                    <div class="note">Accounts in good standing</div>
                </div>
            </div>

            <!-- The table of users.
                 The badge shows each person's role in the system. -->
            <div class="box">
                <div class="box-head">
                    <h2>System Users</h2>
                    <span class="right">From the users table</span>
                </div>
                <table>
                    <tr>
                        <th>NAME</th><th>USERNAME</th><th>ROLE</th><th>PHONE</th><th>STATUS</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($user['full_name']); ?></b></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <?php if ($user['role'] === 'Resident'): ?>
                        <td><span class="badge grey">Resident</span></td>
                        <?php else: ?>
                        <td><span class="badge blue"><?php echo htmlspecialchars($user['role']); ?></span></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <?php if ($user['status'] === 'Active'): ?>
                        <td><span class="badge">Active</span></td>
                        <?php else: ?>
                        <td><span class="badge orange"><?php echo htmlspecialchars($user['status']); ?></span></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <p class="foot-note">A user's role decides what they can see and do after logging in. Only administrators can open this backend.</p>

        </div>
    </div>
</div>

</body>
</html>
