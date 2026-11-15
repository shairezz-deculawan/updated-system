<?php
session_start();
include 'config/database.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === 'admin' && $password === 'adminpassword') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: admin/dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

include 'includes/header.php';
?>

<div class="admin-form">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <p style="color:red; margin-bottom: 10px;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>