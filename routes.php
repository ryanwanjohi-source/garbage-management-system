<?php
// ============================================================
//  Smart Sweep — Routes page (admin only).
//
//  Each route box is built from the routes table, and its
//  stops come from the route_stops table in visit order.
//  The four numbers at the top are counted from the routes.
// ============================================================

require 'includes/auth.php';

$totalRoutes  = (int) $pdo->query('SELECT COUNT(*) FROM routes')->fetchColumn();
$completed    = (int) $pdo->query("SELECT COUNT(*) FROM routes WHERE status = 'Completed'")->fetchColumn();
$inProgress   = (int) $pdo->query("SELECT COUNT(*) FROM routes WHERE status = 'In Progress'")->fetchColumn();
$planned      = (int) $pdo->query("SELECT COUNT(*) FROM routes WHERE status = 'Planned'")->fetchColumn();

$routes = $pdo->query('SELECT * FROM routes ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$stopStmt = $pdo->prepare('SELECT label FROM route_stops WHERE route_id = ? ORDER BY stop_order');

// The badge colour for a route's status.
function routeBadge($status) {
    if ($status === 'Planned')     return 'orange';
    if ($status === 'In Progress') return 'blue';
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes — Smart Sweep</title>
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
            <a href="routes.php" class="active">Routes</a>
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
            <h1>Routes</h1>
            <div class="date"><?php echo date('l, j F Y'); ?></div>
        </div>

        <div class="content">

            <!-- The four number cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-title">ROUTES TODAY</div>
                    <div class="number"><?php echo $totalRoutes; ?></div>
                    <div class="note">Planned by the route optimiser</div>
                </div>
                <div class="card">
                    <div class="card-title">COMPLETED</div>
                    <div class="number"><?php echo $completed; ?></div>
                    <div class="note">All stops emptied</div>
                </div>
                <div class="card">
                    <div class="card-title">IN PROGRESS</div>
                    <div class="number orange"><?php echo $inProgress; ?></div>
                    <div class="note">Trucks are on the road</div>
                </div>
                <div class="card">
                    <div class="card-title">PLANNED</div>
                    <div class="number"><?php echo $planned; ?></div>
                    <div class="note">Waiting to start</div>
                </div>
            </div>

            <!-- One box per route, built from the database.
                 The stops are listed in the order the driver visits them. -->
            <?php foreach ($routes as $route): ?>
            <?php $stopStmt->execute(array($route['id'])); $stops = $stopStmt->fetchAll(PDO::FETCH_COLUMN); ?>
            <div class="box">
                <div class="route-head">
                    <span class="rname">Route <?php echo htmlspecialchars($route['route_code']); ?> — <?php echo htmlspecialchars($route['name']); ?></span>
                    <span class="badge <?php echo routeBadge($route['status']); ?>"><?php echo htmlspecialchars($route['status']); ?></span>
                    <div class="small grey-text">Driver: <?php echo htmlspecialchars($route['driver']); ?> · Vehicle: <?php echo htmlspecialchars($route['vehicle']); ?> · <?php echo htmlspecialchars($route['details']); ?></div>
                </div>
                <div class="stops">
                    <?php foreach ($stops as $i => $stop): ?>
                    <span class="stop"><?php echo htmlspecialchars($stop); ?></span><?php if ($i < count($stops) - 1): ?> <span class="arrow">→</span><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <p class="foot-note">Routes are calculated from live fill levels, so trucks only visit bins that need emptying.</p>

        </div>
    </div>
</div>

</body>
</html>
