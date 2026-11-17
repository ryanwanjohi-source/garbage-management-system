<?php
// ============================================================
//  Smart Sweep — Admin Login page.
//
//  This is the only door into the backend. The administrator
//  types a username and password; PHP checks them against the
//  admins table in the database (the password is compared
//  with password_verify, against the stored hash). If they
//  match, a session is started and the dashboard opens.
//  If not, an error shows and no session is created — so the
//  protected pages stay locked.
// ============================================================

session_start();
require 'includes/db.php';

// If the admin is already logged in, skip the form.
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Look the admin up by username only — the password is
    // never part of the query.
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute(array($username));
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Correct: remember who is logged in, then open the dashboard.
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Wrong username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Smart Sweep</title>
    <!-- This line brings in all the colours and layout from the CSS file -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="login-page">
        <div class="login-box">

            <!-- The system logo and name -->
            <div class="logo-box">S</div>
            <div class="logo-name" style="color:#1c2b23;">Smart Sweep</div>
            <div class="logo-big">Garbage Management System</div>

            <h1>Administrator login</h1>
            <p class="sub">This backend is for administrators only. Log in to manage bins, alerts, routes, reports and users.</p>

            <?php if ($error !== ''): ?>
            <!-- Shown only when a login attempt fails -->
            <div class="demo" style="color:#b3261e;">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- The login form. It sends the details back to this
                 same page (method POST, so nothing shows in the address bar). -->
            <form action="login.php" method="post">
                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="e.g. admin" required>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn">Log In</button>
            </form>

            <div class="login-links">
                <a href="index.php">Back to home</a>
                <a href="signup.php" class="right">Register as a resident</a>
            </div>
        </div>
        <p class="login-foot">Smart Sweep · Smart garbage collection for Nairobi estates</p>
    </div>

</body>
</html>
