<?php
// ============================================================
//  Smart Sweep — Smart Bins page (admin only).
//
//  Every row of the table is a row of the bins table in the
//  database, and the four numbers at the top are counted from
//  it. If a bin's readings change in the database, this page
//  changes with them.
// ============================================================

require 'includes/auth.php';

$registered = (int) $pdo->query('SELECT COUNT(*) FROM bins')->fetchColumn();
$onlineNow  = (int) $pdo->query('SELECT COUNT(*) FROM bins WHERE online = 1')->fetchColumn();
$fullBins   = $pdo->query('SELECT bin_code FROM bins WHERE online = 1 AND fill_level >= 80 ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
$offline    = (int) $pdo->query('SELECT COUNT(*) FROM bins WHERE online = 0')->fetchColumn();

$bins = $pdo->query('SELECT * FROM bins ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Bins — Smart Sweep</title>
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
            <a href="bins.php" class="active">Smart Bins</a>
            <a href="alerts.php">Alerts <span class="count"><?php echo $alertCount; ?></span></a>
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
            <h1>Smart Bins</h1>
            <div class="date"><?php echo date('l, j F Y'); ?></div>
        </div>

        <div class="content">

            <!-- The four number cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-title">REGISTERED BINS</div>
                    <div class="number"><?php echo $registered; ?></div>
                    <div class="note">In the database</div>
                </div>
                <div class="card">
                    <div class="card-title">ONLINE NOW</div>
                    <div class="number"><?php echo $onlineNow; ?></div>
                    <div class="note">Sending telemetry readings</div>
                </div>
                <div class="card">
                    <div class="card-title">FULL (80%+)</div>
                    <div class="number red"><?php echo count($fullBins); ?></div>
                    <div class="note"><?php echo htmlspecialchars(implode(' and ', $fullBins)); ?></div>
                </div>
                <div class="card">
                    <div class="card-title">OFFLINE</div>
                    <div class="number orange"><?php echo $offline; ?></div>
                    <div class="note">Check the sensor units</div>
                </div>
            </div>

            <!-- The table of all bins.
                 Each bar is as wide as the bin's fill level. -->
            <div class="box">
                <div class="box-head">
                    <h2>All Smart Bins</h2>
                    <span class="right">From the bins table</span>
                </div>
                <table>
                    <tr>
                        <th>BIN CODE</th><th>LOCATION</th><th>WASTE TYPE</th><th>FILL LEVEL</th><th class="num">WEIGHT (KG)</th><th>LAST READING</th><th>STATUS</th>
                    </tr>
                    <?php foreach ($bins as $bin): ?>
                    <?php list($statusLabel, $badgeClass, $barClass) = binStatus($bin['fill_level'], $bin['online']); ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($bin['bin_code']); ?></b></td>
                        <td><?php echo htmlspecialchars($bin['location']); ?></td>
                        <td><?php echo htmlspecialchars($bin['waste_type']); ?></td>
                        <?php if ($bin['online']): ?>
                        <td><div class="bar"><div class="fill <?php echo $barClass; ?>" style="width:<?php echo (int) $bin['fill_level']; ?>%"></div></div> <b><?php echo (int) $bin['fill_level']; ?>%</b></td>
                        <td class="num"><?php echo number_format($bin['weight_kg'], 1); ?></td>
                        <?php else: ?>
                        <td><div class="bar"><div class="fill" style="width:0%"></div></div> <b>—</b></td>
                        <td class="num">—</td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($bin['last_reading']); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <p class="foot-note">Fill level and weight come from each bin's ultrasonic and load-cell sensors, sent over MQTT.</p>

        </div>
    </div>
</div>

</body>
</html>
