<?php 
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

// Page variables
session_start();

// Page variables
$pageTitle = "Home page";

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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            Recent results
                        </div>
                        <div class="card-body">
                            <table id="recent-results" class="datatable table-striped">
                                <thead>
                                    <tr>
                                        <th>Matchup</th>
                                        <th>Game 1</th>
                                        <th>Game 2</th>
                                        <th>Game 3</th>
                                        <th>Total</th>
                                        <th>Series record</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                        
                                        $sql = "CALL getRecentResults()";
                                        // call the stored procedure
                                        $q = $pdo->query($sql);
                                        $q->setFetchMode(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        die("Error occurred:" . $e->getMessage());
                                    }
                                    ?>

                                    <?php while ($r = $q->fetch()): 
                                        ?>
                                    
                                    <?php endwhile; ?>
                                    <tr>
                                        <td>Jeff/Logan</td>
                                        <td>22</td>
                                        <td>32</td>
                                        <td>30</td>
                                    </tr>
                                    <tr>
                                        <td>v.</td>
                                    </tr>
                                    <tr>
                                        <td>Bobby2</td>
                                        <td>29</td>
                                        <td>24</td>
                                        <td>44</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer class="main-footer">
            <?php include 'footer.php';?>
        </footer>
    </div>
    <?php include 'scripts.php';?>
    <script>
        $('#recent-results').DataTable();
    </script>
</body>
</html>