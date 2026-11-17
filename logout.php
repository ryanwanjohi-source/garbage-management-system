<?php
// ============================================================
//  logout.php — ends the administrator's session and sends
//  them back to the login page.
// ============================================================

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
