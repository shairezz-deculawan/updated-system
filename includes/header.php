<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_path = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samar Tourism Directory</title>
    <link rel="stylesheet" href="<?= $base_path ?>assets/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="<?= $base_path ?>index.php" class="logo">🌲 Samar Tourism Directory</a>
            <nav>
                <a href="<?= $base_path ?>index.php">Home / Explore</a>
                <?php if (isset($_SESSION['admin_logged_in'])): ?>
                    <a href="<?= $base_path ?>admin/dashboard.php" class="btn-nav">Admin Dashboard</a>
                    <a href="<?= $base_path ?>login.php?logout=1">Logout</a>
                <?php else: ?>
                    <a href="<?= $base_path ?>login.php">Admin Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">
