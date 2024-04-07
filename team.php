<?php 
// Require files
require_once 'includes/includes.php';

// Page variables
$teamID = $_GET['teamID'];

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
$teamName = $tn['team_name'];
$pageTitle =  "Team" . $teamName;

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
<body class="hold-transition sidebar-mini dark-mode">
    <div class="wrapper">

        <?php include 'nav.php'; ?>

        <!-- Main Sidebar Container -->
        <?php include 'side-menu.php'; ?>

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
                                    <button type="button" class="btn btn-primary mr-auto" id="recommended-lineup" data-toggle="modal" data-target="#modal-lineup" onclick="recommendLineup()">
                                        <i class="fa fa-align-left"></i><span class="ml-2">Recommended Lineup</span>
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
            $q = $pdo->query($sql);
            $q->setFetchMode(PDO::FETCH_ASSOC);
            $playerids = array();
        } catch (PDOException $e) {
            die("Error occurred:" . $e->getMessage());
        }
        ?>

                                            <?php while ($r = $q->fetch()): 
                                                $id = $r['srid'];
                                                if (empty($id) || strpos($id, 'tmp') !== false) {
                                                    continue;
                                                }
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
                                                <td class="todays-game">
                                                    <?php showTodaysGame($g); ?>
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
                                <div class="col-lg-4 col-md-6 col-sm-12">
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
    <?php include 'scripts.php';?>

    <!-- Page specific script -->
    <script>
    $(function() {
        $('.hitters-pos').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [7, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                },
            ]
        });
        $('#hitters-all').removeAttr('width').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [18, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "fixedColumns": true,
            "scrollX": true,
            "columnDefs": [{
                    width: 120,
                    targets: 0
                },
                {
                    width: 300,
                    targets: 1
                }
            ]
        });
        $('#pitching').removeAttr('width').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [5, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "fixedColumns": true,
            "scrollX": true,
            "columnDefs": [{
                    width: 200,
                    targets: 0
                },
                {
                    width: 300,
                    targets: 1
                }
            ]
        });
    });

    function recommendLineup() {
        // Catcher
        var c = document.createElement('html');
        c.innerHTML = $('#hitters-C').DataTable().row(0).column(0).data()[0];
        var c_name = c.getElementsByTagName('a')[0].innerHTML;
        var c_team = c.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        var catcher = "C: " + c_name + " - " + c_team;

        // First base
        var fb = document.createElement('html');
        fb.innerHTML = $('#hitters-1B').DataTable().row(0).column(0).data()[0];
        var fb_name = fb.getElementsByTagName('a')[0].innerHTML;
        var fb_team = fb.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        var firstbase = "1B: " + fb_name + " - " + fb_team;

        // Second base
        var sb = document.createElement('html');
        sb.innerHTML = $('#hitters-2B').DataTable().row(0).column(0).data()[0];
        var sb_name = sb.getElementsByTagName('a')[0].innerHTML;
        var sb_team = sb.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        var secondbase = "2B: " + sb_name + " - " + sb_team;

        // Third base
        var tb = document.createElement('html');
        tb.innerHTML = $('#hitters-3B').DataTable().row(0).column(0).data()[0];
        var tb_name = tb.getElementsByTagName('a')[0].innerHTML;
        var tb_team = tb.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        var thirdbase = "3B: " + tb_name + " - " + tb_team;

        // Shortstop
        var ss = document.createElement('html');
        ss.innerHTML = $('#hitters-SS').DataTable().row(0).column(0).data()[0];
        var ss_name = ss.getElementsByTagName('a')[0].innerHTML;
        var ss_team = ss.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        var short = "1B: " + ss_name + " - " + ss_team;

        // Outfield
        var of = document.createElement('html');
        of.innerHTML = $('#hitters-OF').DataTable().row(0).column(0).data()[0];
        let of_name = of.getElementsByTagName('a')[0].innerHTML;
        let of_team = of.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        let outfield_1 = "OF: " + of_name + " - " + of_team;

        of.innerHTML = $('#hitters-OF').DataTable().row(0).column(0).data()[1];
        of_name = of.getElementsByTagName('a')[0].innerHTML;
        of_team = of.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        let outfield_2 = "OF: " + of_name + " - " + of_team;

        of.innerHTML = $('#hitters-OF').DataTable().row(0).column(0).data()[2];
        of_name = of.getElementsByTagName('a')[0].innerHTML;
        of_team = of.getElementsByTagName('span')[0].innerHTML.substring(0,3);
        let outfield_3 = "OF: " + of_name + " - " + of_team;

        // Pitching
        var p = document.createElement('html');
        var p_team  = $('#pitching').DataTable().row(0).column(0).data()[0];
        var pitching = "P: " + p_team;

        $('#lineup-content > #lineup-body')[0].innerHTML = catcher + "<br>" + firstbase + "<br>" +secondbase+"<br>"+short+"<br>"+thirdbase+"<br>"+outfield_1+"<br>"+outfield_2+"<br>"+outfield_3+"<br>"+pitching;
        $('modal-lineup').modal('show');

    };

    function showModal(id) {
        let modal_id = "#" + id
        $(modal_id).modal('show')
    };

    $(function() {
/*         

         */

        function updateButton() {
            const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
            });

            if (params.start_date != null) {
                var start = params.start_date;
            }
            else {
                var start = '2023-03-30';
            }
            if (params.end_date != null) {
                var end = params.end_date;
            }
            else {
                var end = moment().format('YYYY-MM-DD');
            }

            if (start == moment().format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("Today");
            }
            else if (start == moment().subtract(1, 'days').format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("Yesterday");
            }
            else if (start == moment().subtract(6, 'days').format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("Last 7");
            }
            else if (start == moment().subtract(29, 'days').format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("Last 30");
            }
            else if (start == moment().startOf('month').format('YYYY-MM-DD') && end == moment().endOf('month').format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("This Month");
            }
            else if (start == moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD') && end == moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("Last Month");
            }
            else if (start == '2023-03-30' && end == moment().format('YYYY-MM-DD')) {
                $('#daterange-btn span').html("Year to date");
            }
            else {
                $('#daterange-btn span').html(moment(start, 'YYYY-MM-DD').format('MMMM D') + ' - ' + moment(end, 'YYYY-MM-DD').format('MMMM D'));
            }
            /* 'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Year to date': [moment('2023-03-30', 'YYYY-MM-DD'), moment()] */
        }

        function getStart() {
            const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
            });

            if (params.start_date != null) {
                var start = moment(params.start_date, 'YYYY-MM-DD');
            }
            else {
                var start = moment('2023-03-30', 'YYYY-MM-DD');
            }
            return start;
        }
        function getEnd() {
            const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
            });

            if (params.end_date != null) {
                var end = moment(params.end_date, 'YYYY-MM-DD');
            }
            else {
                var end = moment();
            }
            return end;
        }

        updateButton();

        //var start = moment().subtract(29, 'days');
        var start = getStart();
        
        //var end = moment();
        var end = getEnd();


        function cb(start, end) {
            // Default option if no start of end
            $('#daterange-btn span').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
            var url = window.location.href + '&';
            
            var startDate = start ? start.format('YYYY-MM-DD') : '';
            var endDate = end ? end.format('YYYY-MM-DD') : '';
            var params = {};
            if(startDate && endDate){
                params.start_date =  startDate;
                params.end_date =  endDate;
            }
            url+= jQuery.param( params );
            location.href = url
        }

        $('#daterange-btn').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Year to date': [moment('2023-03-30', 'YYYY-MM-DD'), moment()]
            }
        }, cb);

/*         function (start, end, label) {
            var url = "http://tallyfantasy.com/myteam.php"+'?';
            var startDate = start ? start.format('YYYY-MM-DD') : '';
            var endDate = end ? end.format('YYYY-MM-DD') : '';
            var params = {};
            if(startDate && endDate){
                params.start_date =  startDate;
                params.end_date =  endDate;
            }
            url+= jQuery.param( params );
            location.href = url
        } */
        
    });
    </script>
</body>

</html>