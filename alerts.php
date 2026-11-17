<?php
// ============================================================
//  Smart Sweep — Alerts page (admin only).
//
//  The table is read live from the alerts table, and the four
//  numbers at the top are counted from it.
// ============================================================

require 'includes/auth.php';

$fillCount  = (int) $pdo->query("SELECT COUNT(*) FROM alerts WHERE alert_type = 'Fill Level'")->fetchColumn();
$delayCount = (int) $pdo->query("SELECT COUNT(*) FROM alerts WHERE alert_type = 'Delay'")->fetchColumn();
$ackCount   = (int) $pdo->query('SELECT COUNT(*) FROM alerts WHERE acknowledged = 1')->fetchColumn();

$alerts = $pdo->query('SELECT * FROM alerts ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);

// The badge colour for an alert's type: a fill level of 80% or
// more shows red, below that orange; delays orange; offline grey.
function alertBadge($alert) {
    if ($alert['alert_type'] === 'Fill Level') {
        $level = (int) $alert['detail'];
        return ($level >= 80) ? 'red' : 'orange';
    }
    if ($alert['alert_type'] === 'Delay') {
        return 'orange';
    }
    return 'grey';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts — Smart Sweep</title>
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
            <a href="alerts.php" class="active">Alerts <span class="count"><?php echo $alertCount; ?></span></a>
            <a href="routes.php">Routes</a>
            <a href="reports.php">Reports</a>
            <a href="users.php">Users</a>
        </div>
        <div class="logout">
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <div class="main">
        <div class="top">
            <div class="user"><?php echo htmlspecialchars($_SESSION['admin_name']); ?><small>Administrator</small></div>
            <h1>Alerts</h1>
            <div class="date"><?php echo date('l, j F Y'); ?></div>
        </div>

        <div class="content">

            <!-- The four number cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-title">UNACKNOWLEDGED</div>
                    <div class="number red"><?php echo $alertCount; ?></div>
                    <div class="note">Need an administrator's action</div>
                </div>
                <div class="card">
                    <div class="card-title">FILL-LEVEL ALERTS</div>
                    <div class="number"><?php echo $fillCount; ?></div>
                    <div class="note">Raised by the bins</div>
                </div>
                <div class="card">
                    <div class="card-title">DELAY ALERTS</div>
                    <div class="number orange"><?php echo $delayCount; ?></div>
                    <div class="note">Reported by drivers</div>
                </div>
                <div class="card">
                    <div class="card-title">ACKNOWLEDGED</div>
                    <div class="number"><?php echo $ackCount; ?></div>
                    <div class="note">Dealt with</div>
                </div>
            </div>

            <!-- The table of alerts -->
            <div class="box">
                <div class="box-head">
                    <h2>Fill-Level and Delay Alerts</h2>
                    <span class="right">Alert threshold: bin fill level at 80%</span>
                </div>
                <table>
                    <tr>
                        <th>ALERT</th><th>BIN / ROUTE</th><th>TYPE</th><th>RAISED</th><th>STATUS</th><th></th>
                    </tr>
                    <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($alert['alert_code']); ?></b></td>
                        <td><?php echo htmlspecialchars($alert['subject']); ?></td>
                        <td><span class="badge <?php echo alertBadge($alert); ?>"><?php echo htmlspecialchars($alert['alert_type']); ?><?php if ($alert['detail'] !== ''): ?> · <?php echo htmlspecialchars($alert['detail']); ?><?php endif; ?></span></td>
                        <td><?php echo htmlspecialchars($alert['raised']); ?></td>
                        <?php if ($alert['acknowledged']): ?>
                        <td><span class="badge">Acknowledged</span></td>
                        <td><span class="grey-text small"><?php echo htmlspecialchars($alert['note']); ?></span></td>
                        <?php else: ?>
                        <td><span class="badge red">Not Acknowledged</span></td>
                        <?php if ($alert['alert_type'] === 'Fill Level'): ?>
                        <td><a class="btn" href="routes.php">Schedule Collection</a></td>
                        <?php else: ?>
                        <td><a class="btn btn-white" href="#">Acknowledge</a></td>
                        <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <p class="foot-note">Acknowledging an alert records it against your administrator account in the audit log.</p>

        </div>
    </div>
</div>

</body>
</html>
