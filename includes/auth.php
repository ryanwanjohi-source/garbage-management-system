<?php
// ============================================================
//  auth.php — the guard that keeps the backend admin-only.
//
//  Every protected page (dashboard, bins, alerts, routes,
//  reports, users, about) includes this file at the very top,
//  before any HTML. It starts the session and checks whether
//  an administrator is logged in. If not, the visitor is sent
//  straight to the login page — the page below never loads.
//
//  It also loads the database connection and counts the
//  unacknowledged alerts, so the sidebar badge on every page
//  always shows the real number.
// ============================================================

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

// The number shown on the "Alerts" menu item in the sidebar.
$alertCount = (int) $pdo->query('SELECT COUNT(*) FROM alerts WHERE acknowledged = 0')->fetchColumn();

// ------------------------------------------------------------
//  binStatus — works out a bin's status from its fill level.
//  Returns the label to show, the badge colour and the bar
//  colour, the same way the static pages did it by hand:
//  80% or more = Full (red), 60% or more = Near Full (orange),
//  below that = Normal (green). An offline bin shows Offline.
// ------------------------------------------------------------
function binStatus($fillLevel, $online) {
    if (!$online) {
        return array('Offline', 'grey', '');
    }
    if ($fillLevel >= 80) {
        return array('Full', 'red', 'red');
    }
    if ($fillLevel >= 60) {
        return array('Near Full', 'orange', 'orange');
    }
    return array('Normal', '', '');
}
