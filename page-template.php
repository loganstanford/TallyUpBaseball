<?php 
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

// Page variables
session_start();

// Page variables
$pageTitle = "Page Title";

// HTTPS Redirector
redirectHTTPS();

?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <?php include 'head.php'; ?>
    <?php include 'styles.php'; ?>
</head>
<body class="hold-transition sidebar-mini dark-mode">
    <div class="wrapper">
        <?php include 'nav.php'; ?>
        <?php include 'side-menu.php'; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <?php include 'content-header.php'; ?>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Body Content -->
                </div>
            </section>
        </div>
        <footer class="main-footer">
            <?php include 'footer.php';?>
        </footer>
    </div>
    <?php include 'scripts.php';?>
</body>
</html>