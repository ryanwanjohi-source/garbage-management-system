<?php
// ============================================================
//  Smart Sweep — Dashboard page (admin only).
//
//  The auth.php guard runs first: anyone who is not logged in
//  as the administrator is sent to the login page. Everything
//  on this page — the four numbers, the bin table and the
//  alerts — is read live from the database.
// ============================================================

require 'includes/auth.php';

// The four numbers at the top, counted from the database.
$totalBins       = (int) $pdo->query('SELECT COUNT(*) FROM bins')->fetchColumn();
$nearFull        = (int) $pdo->query('SELECT COUNT(*) FROM bins WHERE online = 1 AND fill_level >= 70')->fetchColumn();
$routesCompleted = (int) $pdo->query("SELECT COUNT(*) FROM routes WHERE status = 'Completed'")->fetchColumn();
$routesInProgress = (int) $pdo->query("SELECT COUNT(*) FROM routes WHERE status = 'In Progress'")->fetchColumn();

// The first eight bins, for the live monitoring table.
$bins = $pdo->query('SELECT * FROM bins ORDER BY id LIMIT 8')->fetchAll(PDO::FETCH_ASSOC);

// The four latest alerts.
$latestAlerts = $pdo->query('SELECT * FROM alerts ORDER BY id DESC LIMIT 4')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Smart Sweep</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="page">

    <!-- ===== The left side menu. It is the same on every page.
         The link with class "active" is the page you are on now. ===== -->
    <div class="sidebar">
        <div class="logo">
            <div class="logo-box">S</div>
            <div class="logo-name">Smart Sweep</div>
            <div class="logo-small">Garbage Management</div>
        </div>
        <div class="menu">
            <div class="menu-title">MENU</div>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="about.php">About</a>
            <a href="bins.php">Smart Bins</a>
            <a href="alerts.php">Alerts <span class="count"><?php echo $alertCount; ?></span></a>
            <a href="routes.php">Routes</a>
            <a href="reports.php">Reports</a>
            <a href="users.php">Users</a>
        </div>
        <div class="logout">
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <!-- ===== The main part of the page ===== -->
    <div class="main">

        <!-- The white bar at the top -->
        <div class="top">
            <div class="user"><?php echo htmlspecialchars($_SESSION['admin_name']); ?><small>Administrator</small></div>
            <h1>Dashboard</h1>
            <div class="date"><?php echo date('l, j F Y'); ?></div>
        </div>

        <div class="content">

            <!-- The four number cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-title">TOTAL SMART BINS</div>
                    <div class="number"><?php echo $totalBins; ?></div>
                    <div class="note">Registered in the database</div>
                </div>
                <div class="card">
                    <div class="card-title">BINS NEAR FULL</div>
                    <div class="number orange"><?php echo $nearFull; ?></div>
                    <div class="note">Fill level above 70%</div>
                </div>
                <div class="card">
                    <div class="card-title">ACTIVE ALERTS</div>
                    <div class="number red"><?php echo $alertCount; ?></div>
                    <div class="note">Waiting to be acknowledged</div>
                </div>
                <div class="card">
                    <div class="card-title">ROUTES COMPLETED</div>
                    <div class="number"><?php echo $routesCompleted; ?></div>
                    <div class="note"><?php echo $routesInProgress; ?> route(s) in progress</div>
                </div>
            </div>

            <!-- Live bin monitoring table.
                 The bar in each row shows how full the bin is:
                 the coloured part is as wide as the fill level.
                 Green = fine, orange = near full, red = full. -->
            <div class="box">
                <div class="box-head">
                    <h2>Live Bin Monitoring</h2>
                    <a class="right" href="bins.php">View all bins</a>
                </div>
                <table>
                    <tr>
                        <th>BIN</th><th>LOCATION</th><th>WASTE TYPE</th><th>FILL LEVEL</th><th>STATUS</th>
                    </tr>
                    <?php foreach ($bins as $bin): ?>
                    <?php list($statusLabel, $badgeClass, $barClass) = binStatus($bin['fill_level'], $bin['online']); ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($bin['bin_code']); ?></b></td>
                        <td><?php echo htmlspecialchars($bin['location']); ?></td>
                        <td><?php echo htmlspecialchars($bin['waste_type']); ?></td>
                        <?php if ($bin['online']): ?>
                        <td><div class="bar"><div class="fill <?php echo $barClass; ?>" style="width:<?php echo (int) $bin['fill_level']; ?>%"></div></div> <b><?php echo (int) $bin['fill_level']; ?>%</b></td>
                        <?php else: ?>
                        <td><div class="bar"><div class="fill" style="width:0%"></div></div> <b>—</b></td>
                        <?php endif; ?>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- The latest alerts -->
            <div class="box">
                <div class="box-head">
                    <h2>Latest Alerts</h2>
                    <a class="right" href="alerts.php">View all alerts</a>
                </div>
                <?php foreach ($latestAlerts as $alert): ?>
                <div class="alert-row">
                    <div class="what"><?php echo htmlspecialchars($alert['message']); ?></div>
                    <div class="when"><?php echo htmlspecialchars($alert['message_sub']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <p class="foot-note">Smart Sweep — multi-sensor bin telemetry, automated segregation and route optimisation for urban Kenya.</p>

        </div>
    </div>
</div>

</body>
</html>
