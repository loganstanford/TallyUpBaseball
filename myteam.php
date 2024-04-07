<?php
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

//checkLogin($con);

// Page variables
session_start();
if (isset($_SESSION['team_id'])){
    $teamID = $_SESSION['team_id'];
}
else {
    header("Location: index.php");
}


// Database calls
try {
    $teamsSQL = "SELECT m.name as 'team_name' FROM `divisions` as d
    JOIN managers as m
    ON d.manager_id = m.id
    WHERE d.manager_id = $teamID
    AND d.year = YEAR(CURDATE())";
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $q = $pdo->query($teamsSQL);
    $q->setFetchMode(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error occurred:" . $e->getMessage());
}
$tn = $q->fetch();

// Database call variables
$pageTitle = $tn['team_name'];

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
    <style>
    .career-stats {
        width: 100%;
    }
    .player-position {
        float: left;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini dark-mode">
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
                                <div class="modal fade" id="modal-select-lineup">
                                    <div class="modal-dialog">
                                        <div class="modal-content" id="select-lineup-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Select Lineup</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div id="lineup-body" class="modal-body">
                                                <table id="select-lineup" class="datatable w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>Player</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $positions = array('C', '1B', '2B', 'SS', '3B', 'OF', 'OF', 'OF', 'DH', 'P'); 
                                                        foreach ($positions as $pos) {
                                                            ?>
                                                            <tr id="row">
                                                                <td class="py-2" style="align-items: center; display: flex;">
                                                                    <?php 
                                                                    if ($pos == 'DH') {
                                                                        $sql="SELECT * FROM current_rosters WHERE manager_id = $teamID AND pitching_name IS NULL";
                                                                    }
                                                                    else {
                                                                        $sql="CALL getTeamPositionPlayers($teamID, '$pos')";
                                                                    }
                                                                    
                                                                    //print_r($sql);
                                                                    //print_r($conn);
                                                                    $conn = mysqli_connect($host,$username,$password,$dbname);
                                                                    $result = mysqli_query($conn,$sql);
                                                                    //print_r($c_result); ?>
                                                                    <div class="player-position" style="width: 25px;"><?php echo $pos;?>:</div>
                                                                    <select name="<?php echo $pos;?>" class="form-control player-select ml-3" id="select-<?php echo $pos;?>" style="max-width: 300px;">
                                                                        <option disabled selected value="">Select <?php echo $pos;?>...</option>
                                                                        <?php
                                                                        while($row = mysqli_fetch_array($result)) {
                                                                            if ($pos == 'P') {
                                                                                ?>
                                                                                <option value="<?php echo $pos . ': ' . $row['pitching_name'];?>">
                                                                                    <?php echo $row['pitching_name'];?>
                                                                                </option>
                                                                                <?php
                                                                            }
                                                                            else {
                                                                                ?>
                                                                                <option value="<?php echo $pos . ': ' . $row['player_first_name'] . " " . $row['player_last_name'] . " - " . $row['team_abbr'];?>">
                                                                                    <?php echo $row['player_first_name'] . " " . $row['player_last_name'] . " - " . $row['team_abbr'];?>
                                                                                </option>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        
                                                                        <?php
                                                                        }
                                                                        //mysqli_free_result($result);
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            mysqli_close($conn);
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" onclick="copyLineupToClipboard()">
                                                    <i class="fa fa-copy"></i>Copy to clipboard</button>
                                                <a href="https://mb.boardhost.com/bullpen/" target="_blank">
                                                    <button type="button" class="btn btn-primary">
                                                        <i class="far fa-comments"></i>Go to message board</button>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <div class="modal fade" id="modal-lineup">
                                    <div class="modal-dialog">
                                        <div class="modal-content" id="lineup-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Recommended Lineup</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div id="lineup-body" class="modal-body">
                                                Error loading lineup. Tell Logan about it so he can fix it.
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary"
                                                    onclick="copyToClipboard()">
                                                    <i class="fa fa-copy"></i>
                                                    Copy to clipboard</button>
                                                <a href="https://mb.boardhost.com/bullpen/" target="_blank">
                                                    <button type="button" class="btn btn-primary">
                                                        <i class="far fa-comments"></i>
                                                        Go to message board</button>
                                                </a>
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
                                    <button type="button" class="btn btn-primary mr-auto" id="recommended-lineup"
                                        data-toggle="modal" data-target="#modal-lineup" onclick="recommendLineup()">
                                        <i class="fa fa-align-left"></i><span class="ml-2">Recommended Lineup</span>
                                    </button>
                                    <button type="button" class="btn btn-primary mr-auto" id="select-lineup"
                                        data-toggle="modal" data-target="#modal-select-lineup" onclick="selectLineup()">
                                        <i class="fa fa-align-left"></i><span class="ml-2">Select Lineup</span>
                                    </button>
                                    <div class="status-legend d-flex flex-wrap align-self-end mt-auto">
                                        <div class=""><i class="nav-icon far fa-circle text-success ml-2"></i><span
                                                class="m-2">In starting lineup</span></div>
                                        <div class=""><i class="nav-icon far fa-circle text-warning ml-2"></i><span
                                                class="m-2">Lineup not submitted</span></div>
                                        <div class=""><i class="nav-icon far fa-circle text-danger ml-2"></i><span
                                                class="m-2">Not in lineup</span></div>
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
                                                <th>BABIP</th>
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
                                                    $table = "agg_stats_ytd";  // Year to date
                                                } elseif ($startDate == $last7Days->format('Y-m-d') && $endDate == $today->format('Y-m-d')) {
                                                    $table = "agg_stats_last7";
                                                } elseif ($startDate == $last15Days->format('Y-m-d') && $endDate == $today->format('Y-m-d')) {
                                                    $table = "agg_stats_last15";
                                                } elseif ($startDate == $last30Days->format('Y-m-d') && $endDate == $today->format('Y-m-d')) {
                                                    $table = "agg_stats_last30";
                                                }
                                                $sql = "SELECT player_srid as srid, player_first_name as first_name, player_last_name as last_name, team_abbr as team_name, COALESCE(agg.pos, player_positions, 'N/A') as pos, player_status as 'Status', player_bbref as bbref_id, agg.AB, agg.R, agg.H, agg.singles, agg.doubles, agg.triples, agg.RBI, agg.SB, agg.BB, agg.HR, agg.AVG, agg.TB, agg.OBP, agg.SLG, agg.OPS, agg.BABIP, agg.Total_points, agg.bats FROM current_rosters LEFT JOIN $table as agg ON current_rosters.player_srid = agg.srid WHERE manager_id = $teamID ORDER BY Total_points DESC";
                                                // call the stored procedure
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
                                                <td class="player-name">
                                                    <?php 
                                                array_push($playerids, $r['srid']); 
                                                $lineup = getLineup($g);
                                                echo showPlayerName($r, $injuryIds, $lineup);
                                                ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    showTodaysGame($g);
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
                                                    <?php echo (is_Null($r['BABIP'])) ? "-" : $r['BABIP']; ?></td>
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
                        <?php $positions = array('C', '1B', '2B', 'SS', '3B', 'OF');
                            foreach ($positions as $pos) {
                                ?>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top <?php echo $pos; ?></h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-<?php echo $pos;?>" class="table hitters-pos table-striped">
                                        <thead>
                                            <tr style="text-align: center;">
                                                <th style="text-align: left;">Player</th>
                                                <th>Today's game</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getPositionStatsOnRange($teamID, '$pos', '$startDate', '$endDate')";
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
                                                $game_sql = "CALL getTodaysGame('$id')";
                                                $gq = $pdo->query($game_sql);
                                                $gq->setFetchMode(PDO::FETCH_ASSOC);
                                                $g = $gq->fetch();?>
                                            <tr>
                                                <td>
                                                    <?php 
                                                $lineup = getLineup($g);
                                                echo showPlayerName($r, $injuryIds, $lineup); ?>
                                                </td>
                                                <td><?php 
                                                    showTodaysGame($g);
                                                    ?></td>
                                                <td class="box-stats"><?php echo $r['AB']; ?></td>
                                                <td class="box-stats"><?php echo $r['R']; ?></td>
                                                <td class="box-stats"><?php echo $r['H']; ?></td>
                                                <td class="box-stats"><?php echo $r['HR']; ?></td>
                                                <td class="box-stats"><?php echo $r['TB']; ?></td>
                                                <td class="box-stats"><?php echo $r['OPS']; ?></td>
                                                <td class="box-stats"><?php echo $r['Total_points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <?php
                            }
                            ?>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="card col-sm-12 mb-4" style="max-width: 1200px; margin: auto;">
                            <div class="card-header">
                                Pitching
                            </div>
                            <div class="card-body">
                                <table id="pitching" class="table pitching table-striped" style="width: 1000px">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th style="text-align: left;">Team</th>
                                            <th>Today's game</th>
                                            <th>K/9</th>
                                            <th>RA/9</th>
                                            <th>WHIP</th>
                                            <th>Total runs</th>
                                            <th>Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        
                                        try {
                                            $sql = "CALL getTeamPitchStats($teamID, '$startDate', '$endDate')";
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
                                            <td><?php showTodaysGame($g)?></td>
                                            <td class="box-stats"><?php echo $r['K/9']; ?></td>
                                            <td class="box-stats"><?php echo $r['RA/9']; ?></td>
                                            <td class="box-stats"><?php echo $r['WHIP']; ?></td>
                                            <td class="box-stats"><?php echo $r['Total runs']; ?></td>
                                            <td class="box-stats"><?php echo $r['Total_points']; ?></td>
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

    <!-- Sweet alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Page specific script -->
    <script>
    <?php include 'baseball-datatables.php';?>
    <?php include 'scripts.js';?>
    function copyLineupToClipboard() {
        var text = "";
        $('.player-select').each(function() {
            text += this.value + "\n";
        })
        navigator.clipboard.writeText(text);
        Swal.fire({
            icon: 'success',
            title: 'Lineup copied to clipboard!',
            showConfirmButton: false,
            timer: 1500
        })
        console.log(text);
    }
    </script>
</body>

</html>