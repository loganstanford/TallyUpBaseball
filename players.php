<?php
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

//checkLogin($con);

// Database call variables
$pageTitle = "All players and pitching staffs";

// HTTPS Redirector
redirectHTTPS();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php 
include 'head.php';
include 'styles.php';
?>
</head>
<?php require_once 'dbconfig.php'; ?>
<body class="hold-transition sidebar-mini dark-mode">
    <div id="loading-overlay">
            <img src="https://media4.giphy.com/media/v1.Y2lkPTc5MGI3NjExdGtjZ2hvZDZyZmF4enViazRnYW92N213bTA5eDI5ZWVicHM5d2MzNyZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9cw/1Aftgizz0ekprKsRHJ/giphy.gif" alt="Loading..." id="loading-image">
        </div>
    </div>
    <div class="wrapper">

        <?php include 'nav.php';?>
        <?php include 'side-menu.php';?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <?php include 'content-header.php';?>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <div class="modal_wrapper">
                                <div class="modal fade" id="modal-lineup">
                                    <div class="modal-dialog">
                                        <div class="modal-content" id="lineup-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Recommended Lineup</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div id="lineup-body" class="modal-body">
                                                Test
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                            <!-- /.modal -->
                                <!-- Modal -->
                                <?php
                                try {
                                    $sql = 'SELECT * FROM injuries';
                                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                    // call the stored procedure
                                    $q = $pdo->query($sql);
                                    $q->setFetchMode(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                    die("Error occurred:" . $e->getMessage());
                                }
                                $injuryIds = array();
                                while ($i=$q->fetch()):
                                    array_push($injuryIds, $i['player_srid']);
                            ?>
                                <div class="modal fade" id="<?php echo $i['player_srid']  ?>" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalScrollableTitle">
                                                    <?php echo $i['status'] . " - " . $i['description']; ?></h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <?php 
                                                    
                                                    echo "<b>" . $i['update_date'] . "</b><p>" . $i['comment']; 
                                                    ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="card" style="max-width: 1200px; margin: auto;">
                                <!-- .card-header -->
                                <div class="card-header d-flex flex-wrap-reverse">
                                    <button type="button" class="btn btn-default mr-auto" id="daterange-btn">
                                        <i class="far fa-calendar-alt"></i><span class="ml-2"></span>
                                        <i class="fas fa-caret-down"></i>
                                    </button>
                                    <button type="button" class="btn btn-default mr-auto" id="free-agent">
                                        <i class="far fa-user"></i><span class="ml-2">Free agents</span>
                                    </button>
                                    <div class="status-legend d-flex flex-wrap align-self-end mt-auto">
                                        <div class=""><i class="nav-icon far fa-circle text-success ml-2"></i><span class="m-2">In starting lineup</span></div>
                                        <div class=""><i class="nav-icon far fa-circle text-warning ml-2"></i><span class="m-2">Lineup not submitted</span></div>
                                        <div class=""><i class="nav-icon far fa-circle text-danger ml-2"></i><span class="m-2">Not in lineup</span></div>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-all" class="table hitters table-striped" style="width: 1142px">
                                        <thead>
                                            <tr style="text-align: center;">
                                                <th style="text-align: left;">Player</th>
                                                <th>Today's Game</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>1B</th>
                                                <th>2B</th>
                                                <th>3B</th>
                                                <th>RBI</th>
                                                <th>SB</th>
                                                <th>BB</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>AVG</th>
                                                <th>OBP</th>
                                                <th>SLG</th>
                                                <th>OPS</th>
                                                <th>Team</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php                                    
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $currentYear = date("Y");
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : "$currentYear-01-01";
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");

    $today = new DateTime();
    $last7Days = (new DateTime())->sub(new DateInterval('P6D'));
    $last15Days = (new DateTime())->sub(new DateInterval('P14D'));
    $last30Days = (new DateTime())->sub(new DateInterval('P29D'));

    // Determine which aggregated table or stored procedure to use
    if ($startDate == "$currentYear-01-01" && $endDate == $today->format('Y-m-d')) {
        $sql = "SELECT * FROM agg_stats_ytd";  // Year-to-date
    } elseif ($startDate == $last7Days->format('Y-m-d') && $endDate == $today->format('Y-m-d')) {
        $sql = "SELECT * FROM agg_stats_last7";  // Last 7 days
    } elseif ($startDate == $last15Days->format('Y-m-d') && $endDate == $today->format('Y-m-d')) {
        $sql = "SELECT * FROM agg_stats_last15";  // Last 15 days
    } elseif ($startDate == $last30Days->format('Y-m-d') && $endDate == $today->format('Y-m-d')) {
        $sql = "SELECT * FROM agg_stats_last30";  // Last 30 days
    } else {
        $sql = "CALL getAllPlayerStats('$startDate', '$endDate')";  // Custom range
    }

    $q = $pdo->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $playerids = array();
} catch (PDOException $e) {
    die("Error occurred:" . $e->getMessage());
}                                      
?>
                                            <?php while ($r = $q->fetch()): 
                                                $id = $r['srid'];
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                $game_sql = "CALL getTodaysGame('$id')";
                                                $gq = $pdo->query($game_sql);
                                                $gq->setFetchMode(PDO::FETCH_ASSOC);
                                                $g = $gq->fetch();
                                                ?>
                                            <tr>
                                                <td>
                                                    <?php 
                                                array_push($playerids, $r['srid']); 
                                                $lineup = getLineup($g);
                                                showPlayerName($r, $injuryIds, $lineup);

                                                ?>
                                                </td>
                                                <td>
                                                    <?php showTodaysGame($g); 
                                                    try {
                                                        $playerId = $r['srid'];
                                                        $sql = "CALL getSplits('$playerId')";
                                                        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                        //print_r($sql);
                                                        // call the stored procedure
                                                        $s = $pdo->query($sql);
                                                        //print_r($s);
                                                        $s->setFetchMode(PDO::FETCH_ASSOC);
                                                    } catch (PDOException $e) {
                                                        die("Error occurred:" . $e->getMessage());
                                                    }
                                                    $u=$s->fetchAll();
                                                    //print_r($u);
                                                    splitModal($g['throws'], $g['day_night'], $g['home_away'], $g['venue_id'], $u);
                                                    ?>
                                                </td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['AB'])) ? "-" : $r['AB']; ?></td>
                                                <td class="box-stats"><?php echo (is_Null($r['R'])) ? "-" : $r['R']; ?>
                                                </td>
                                                <td class="box-stats"><?php echo (is_Null($r['H'])) ? "-" : $r['H']; ?>
                                                </td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['singles'])) ? "-" : $r['singles']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['doubles'])) ? "-" : $r['doubles']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['triples'])) ? "-" : $r['triples']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['RBI'])) ? "-" : $r['RBI']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['SB'])) ? "-" : $r['SB']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['BB'])) ? "-" : $r['BB']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['HR'])) ? "-" : $r['HR']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['TB'])) ? "-" : $r['TB']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['AVG'])) ? "-" : $r['AVG']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['OBP'])) ? "-" : $r['OBP']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['SLG'])) ? "-" : $r['SLG']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['OPS'])) ? "-" : $r['OPS']; ?></td>
                                                <td class="box-stats">
                                                    <?php echo (is_Null($r['team_name'])) ? "FA" : $r['team_name']; ?></td>
                                                <td class="box-stats total">
                                                    <?php echo (is_Null($r['Total_points'])) ? "0" : $r['Total_points']; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="card col-sm-12 mb-4" style="max-width: 1200px; margin: auto;">
                            <div class="card-header">
                                Pitching
                            </div>
                            <div class="card-body">
                                <table id="pitching" class="table pitching table-striped" style="width: 1000px">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th style="text-align: left;">Pitching</th>
                                            <th>Today's game</th>
                                            <th>K/9</th>
                                            <th>RA/9</th>
                                            <th>WHIP</th>
                                            <th>Total runs</th>
                                            <th>Team</th>
                                            <th>Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        
                                        try {
                                            $sql = "CALL getAllPitchingStats('$startDate', '$endDate')";
                                            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                            // call the stored procedure
                                            $q = $pdo->query($sql);
                                            $q->setFetchMode(PDO::FETCH_ASSOC);
                                        } catch (PDOException $e) {
                                            die("Error occurred:" . $e->getMessage());
                                        }
                                        
                                        while ($r = $q->fetch()): 
                                            $id = $r['srid'];
                                            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                            $game_sql = "CALL getTodaysGameByTeam('$id')";
                                            $gq = $pdo->query($game_sql);
                                            $gq->setFetchMode(PDO::FETCH_ASSOC);
                                            $g = $gq->fetch();?>
                                        <tr>
                                            <td><?php showPitching($r);?></td>
                                            <td><?php showTodaysGame($g);?></td>
                                            <td class="box-stats"><?php echo $r['K/9']; ?></td>
                                            <td class="box-stats"><?php echo $r['RA/9']; ?></td>
                                            <td class="box-stats"><?php echo $r['WHIP']; ?></td>
                                            <td class="box-stats"><?php echo $r['Total runs']; ?></td>
                                            <td class="box-stats"><?php echo $r['team_name']; ?></td>
                                            <td class="box-stats"><?php echo $r['Total points']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <?php include 'footer.php';?>
        </footer>
    </div>
    <!-- ./wrapper -->
    <!-- REQUIRED SCRIPTS -->
    <?php include 'scripts.php'; ?>

    <!-- Page specific script -->
    <script>     
    $('#hitters-all').removeAttr('width').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "order": [[ 18, "desc" ], [ 2, "desc" ]],
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "fixedColumns": true,
            "scrollX": true,
            "columnDefs": [{
                    width: 130,
                    targets: 0
                },
                {
                    width: 340,
                    targets: 1
                }
            ]
        });
        $('#pitching').removeAttr('width').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [7, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "fixedColumns": true,
            "scrollX": true,
            "columnDefs": [{
                    width: 140,
                    targets: 0
                },
                {
                    width: 300,
                    targets: 1
                }
            ]
        });
        function filterColumn( value ) {
            table.column(17).search( value ).draw();
        }
        
        var table = $('#hitters-all').DataTable();
        
        $('#free-agent').on('click', function () {
            filterColumn('FA');
        });
    </script>
</body>

</html>