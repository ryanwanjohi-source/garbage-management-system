<?php
// ============================================================
//  Smart Sweep — Reports page (admin only).
//
//  The money side of the system. Both tables are read live
//  from the database, and the four numbers at the top are
//  worked out from the invoices and payments tables.
// ============================================================

require 'includes/auth.php';

$invoiceCount = (int) $pdo->query('SELECT COUNT(*) FROM invoices')->fetchColumn();
$collected    = (int) $pdo->query('SELECT COALESCE(SUM(amount), 0) FROM payments')->fetchColumn();
$paymentCount = (int) $pdo->query('SELECT COUNT(*) FROM payments')->fetchColumn();
$unpaid        = (int) $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM invoices WHERE status = 'Unpaid'")->fetchColumn();
$unpaidCount   = (int) $pdo->query("SELECT COUNT(*) FROM invoices WHERE status = 'Unpaid'")->fetchColumn();
$residents     = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Resident'")->fetchColumn();

$invoices = $pdo->query('SELECT * FROM invoices ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$payments = $pdo->query('SELECT * FROM payments ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports — Smart Sweep</title>
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
            <a href="reports.php" class="active">Reports</a>
            <a href="users.php">Users</a>
        </div>
        <div class="logout">
            <a href="logout.php">Log Out</a>
        </div>
    </div>

    <div class="main">
        <div class="top">
            <div class="user"><?php echo htmlspecialchars($_SESSION['admin_name']); ?><small>Administrator</small></div>
            <h1>Reports</h1>
            <div class="date"><?php echo date('l, j F Y'); ?></div>
        </div>

        <div class="content">

            <!-- The four number cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-title">INVOICES ISSUED</div>
                    <div class="number"><?php echo $invoiceCount; ?></div>
                    <div class="note">Billing statements in the database</div>
                </div>
                <div class="card">
                    <div class="card-title">COLLECTED (M-PESA)</div>
                    <div class="number">KSh <?php echo number_format($collected); ?></div>
                    <div class="note">From <?php echo $paymentCount; ?> payments received</div>
                </div>
                <div class="card">
                    <div class="card-title">STILL UNPAID</div>
                    <div class="number orange">KSh <?php echo number_format($unpaid); ?></div>
                    <div class="note"><?php echo $unpaidCount; ?> invoices outstanding</div>
                </div>
                <div class="card">
                    <div class="card-title">ACTIVE RESIDENTS</div>
                    <div class="number"><?php echo $residents; ?></div>
                    <div class="note">Registered households</div>
                </div>
            </div>

            <!-- Invoices table -->
            <div class="box">
                <div class="box-head">
                    <h2>Latest Invoices</h2>
                    <span class="right">From the invoices table</span>
                </div>
                <table>
                    <tr>
                        <th>INVOICE</th><th>RESIDENT</th><th>ESTATE</th><th class="num">AMOUNT (KSH)</th><th>STATUS</th>
                    </tr>
                    <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($invoice['invoice_code']); ?></b></td>
                        <td><?php echo htmlspecialchars($invoice['resident_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['estate']); ?></td>
                        <td class="num"><?php echo number_format($invoice['amount']); ?></td>
                        <?php if ($invoice['status'] === 'Paid'): ?>
                        <td><span class="badge">Paid</span></td>
                        <?php else: ?>
                        <td><span class="badge orange">Unpaid</span></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Payments table -->
            <div class="box">
                <div class="box-head">
                    <h2>Latest Payments</h2>
                </div>
                <table>
                    <tr>
                        <th>PAYMENT</th><th>INVOICE</th><th>METHOD</th><th>M-PESA REFERENCE</th><th>DATE</th><th class="num">AMOUNT (KSH)</th>
                    </tr>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><b><?php echo htmlspecialchars($payment['payment_code']); ?></b></td>
                        <td><?php echo htmlspecialchars($payment['invoice_code']); ?></td>
                        <td><?php echo htmlspecialchars($payment['method']); ?></td>
                        <td><?php echo $payment['mpesa_ref'] ? htmlspecialchars($payment['mpesa_ref']) : '—'; ?></td>
                        <td><?php echo htmlspecialchars($payment['pay_date']); ?></td>
                        <td class="num"><?php echo number_format($payment['amount']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <p class="foot-note">Reports are generated by the Accountant from the invoices and payments tables.</p>

        </div>
    </div>
</div>

</body>
</html>
