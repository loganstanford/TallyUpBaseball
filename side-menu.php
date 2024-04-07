<?php
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';  
?>   
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <img src="logo.png" alt="league-logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><b>Tallyup</b>Baseball</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
            </div>
            <?php
            if (isset($_SESSION["user_email"])) {
                    echo '<div class="info">';
                    echo '<a href="#" class="d-block">Welcome, ';
                    echo $_SESSION["user_name"] .'</a></div>';
                }
            ?>
            
                
            
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php 
                if (isset($_SESSION['team_id'])) {
                    echo '<li class="nav-item">';
                    echo '<a href="myteam.php" class="nav-link">';
                    echo '<p>My Team</p></a></li>';
                }
                ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <!-- <i class="nav-icon fas fa-group"></i>-->
                        <p>
                        Teams
                        <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                <?php
                try {
                    $teamsSQL = "SELECT d.name as 'division', m.name as 'team_name', d.manager_id as 'team_id' FROM `divisions` as d JOIN managers as m ON d.manager_id = m.id WHERE d.year = YEAR(CURDATE()) ORDER BY d.name, m.name;";
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    // call the stored procedure
                    $q = $pdo->query($teamsSQL);
                    $q->setFetchMode(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    print_r($e->getMessage());
                    die("Error occurred:" . $e->getMessage());
                }
                $injuryIds = array();
                while ($t=$q->fetch()):
                ?>
        
                        <li class="nav-item">
                            <a href="team.php?teamID=<?php echo $t['team_id'];?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo $t['division'] . ' - ' . $t['team_name'];?></p>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="players.php" class="nav-link">
                        <!-- <i class='nav-icon fas fa-user'></i> -->
                        <p>Players</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="calculate-scores.php" class="nav-link">
                        <!-- <i class='nav-icon fas fa-user'></i> -->
                        <p>Score Calculator</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
