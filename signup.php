<?php
// ============================================================
//  Smart Sweep — Sign Up page (resident registration).
//
//  A new resident fills in the form; PHP checks the two
//  passwords match and that the username is free, then saves
//  the account into the users table with the password stored
//  as a hash. The new resident then appears on the admin's
//  Users page straight away.
// ============================================================

require 'includes/db.php';

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $estate   = trim($_POST['estate']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = 'The two passwords do not match. Please type them again.';
    } else {
        // One username can only be used once.
        $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $check->execute(array($username));

        if ($check->fetchColumn() > 0) {
            $error = 'That username is already taken. Please choose another one.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (full_name, username, role, phone, email, estate, password_hash, status)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute(array($fullName, $username, 'Resident', $phone, $email, $estate, $hash, 'Active'));
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up — Smart Sweep</title>
    <!-- This line brings in all the colours and layout from the CSS file -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="login-page">
        <div class="login-box">

            <!-- The system logo and name -->
            <div class="logo-box">S</div>
            <div class="logo-name" style="color:#1c2b23;">Smart Sweep</div>
            <div class="logo-small">Garbage Management System</div>

            <?php if ($success): ?>

            <!-- Shown after the account has been saved -->
            <h1>Account created</h1>
            <p class="sub">Thank you for registering with Smart Sweep. Your account has been
            saved and the administration team can now see it in the system.</p>
            <a href="login.php" class="btn">Go to the login page</a>

            <?php else: ?>

            <h1>Create your account</h1>
            <p class="sub">Sign up as a resident to request collections, follow your bin and pay for the service.</p>

            <?php if ($error !== ''): ?>
            <div class="demo" style="color:#b3261e;">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- The sign up form. It sends the details back to
                 this same page, which saves them to the database. -->
            <form action="signup.php" method="post">
                <div class="field">
                    <label for="fullname">Full name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="e.g. Mary Wanjiku" required>
                </div>
                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="field">
                    <label for="phone">Phone number</label>
                    <input type="tel" id="phone" name="phone" placeholder="e.g. 0712 345 678" required>
                </div>
                <div class="field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" placeholder="e.g. mary@example.com" required>
                </div>
                <div class="field">
                    <label for="estate">Estate / Area</label>
                    <input type="text" id="estate" name="estate" placeholder="e.g. Kilimani" required>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="field">
                    <label for="confirm">Confirm password</label>
                    <input type="password" id="confirm" name="confirm" placeholder="Type the password again" required>
                </div>
                <button type="submit" class="btn">Create Account</button>
            </form>

            <div class="login-links">
                <a href="login.php">Already have an account? Log in</a>
            </div>

            <?php endif; ?>
        </div>
        <p class="login-foot">Smart Sweep · Smart garbage collection for Nairobi estates</p>
    </div>

</body>
</html>
