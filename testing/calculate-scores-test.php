<?php 
session_start();
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

// Page variables
$pageTitle = "Score Calculator";

// Database query
if (isset($_GET['team1'])) {
    $sql = "CALL getTeamStatsOnRange()";
}

if (isset($_GET['team2'])) {

}
// HTTPS Redirector
redirectHTTPS();

?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <?php include 'head.php'; ?>
    <?php include 'styles.php'; ?>
    <style>
        #matchup-date {
            width: 280px;
        }
        .player-row {
        }
        .player-position {
            display: inline-flex;
            width: 30px;
        }
        .player-select {
            display: inline-flex;
            width: 200px;
        }
        .points {
            text-align: center;
        }
        #team1, #team2 {
            color: grey;
        }
    </style>
    <script>
        function getTeam1(teamId) {
            var date = $('#matchup-date').datetimepicker('date').format("YYYY-MM-DD");
            console.log(date);
            if (teamId=="") {
                document.getElementById("opp1-card").innerHTML="";
                return;
            }
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) {
                    document.getElementById("opp1-card").innerHTML=this.responseText;
                }
            }
            xmlhttp.open("GET","getTeam.php?id="+teamId+"&date="+date,true);
            xmlhttp.send();
        }
        function getTeam2(teamId) {
            var date = $('#matchup-date').datetimepicker('date').format("YYYY-MM-DD");
            console.log(date);
            if (teamId=="") {
                document.getElementById("opp2-card").innerHTML="";
                return;
            }
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) {
                    document.getElementById("opp2-card").innerHTML=this.responseText;
                }
            }
            xmlhttp.open("GET","getTeam.php?id="+teamId+"&date="+date,true);
            xmlhttp.send();
        }
        function calculateColumn(index, id) {
            var total = 0;
            console.log("Index = " + index + " and id =  " + id);
            $(`#opp-${id} > tbody tr`).each(function() {
                var value = parseInt($('td', this).eq(1).text());
                console.log("Value: " + value)
                if (!isNaN(value)) {
                    total += value;
                    console.log("Total: " + total)
                }
            });
            $(`#opp-${id} > tfoot td`).eq(1).text('Total:   ' + total);
        }
        function updatePoints(points, position, id) {
            $(`#opp-${id} > tbody tr > #${position}-points`)[0].innerHTML = points;
            //$(`#${position}-points`)[0].innerHTML = points;
            //console.log("Calling calculatecolumn with id = " + id);
            //console.log(`#opp-${id} > thead th`);
            $(`#opp-${id} > thead th`).each(function(i) {
                //console.log("Calling calculatecolumn with i = " + i + " and id = " + id);
                calculateColumn(i, id);
            });
        }
        function enableTeams() {
            $('#team1').prop("disabled", false);
            $('#team1').css("color", "#FFF");
            $('#team2').prop("disabled", false);
            $('#team2').css("color", "#FFF");
        }
    </script>
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
                    <div class="input-group date" id="matchup-date" style="cursor: pointer" data-target-input="nearest">
                        <label class="mx-2">Select date:</label>
                        <input type="text" class="form-control datetimepicker-input" id="matchup-date-input" style="cursor: pointer" data-target="#matchup-date" data-toggle="datetimepicker" oninput="enableTeams()"/>
                        <div class="input-group-append" data-target="#matchup-date" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i><i class="fas fa-caret-down px-2"></i></div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Opponent 1 Table -->
                        <div class="card col-xl-5 col-md-12 m-2">
                            <div class="card-header">
                                <select disabled class="form-control" name="team1" id="team1" onchange="getTeam1(this.value)">
                                    <option value="" disabled selected>Select team 1</option>
                                    <?php 
                                    try {
                                        $teamsSQL = "SELECT d.name as 'division', m.name as 'team_name', d.manager_id as 'team_id' FROM `divisions` as d JOIN managers as m ON d.manager_id = m.id";
                                        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                        // call the stored procedure
                                        $q = $pdo->query($teamsSQL);
                                        $q->setFetchMode(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        print_r($e->getMessage());
                                        die("Error occurred:" . $e->getMessage());
                                    }
                                    while($t=$q->fetch()): ?>
                                        <option value="<?php echo $t['team_id'];?>"><?php echo $t['division'] . ' - ' . $t['team_name'];?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="card-body" id="opp1-card">
                            </div>
                        </div>
                        <!-- Opponent 2 Table -->
                        <div class="card col-xl-5 col-md-12 m-2">
                            <div class="card-header">
                                    <select disabled class="form-control" name="team2" id="team2" onchange="getTeam2(this.value)">
                                    <option value="" disabled selected>Select team 2</option>
                                <?php 
                                    try {
                                        $teamsSQL = "SELECT d.name as 'division', m.name as 'team_name', d.manager_id as 'team_id' FROM `divisions` as d JOIN managers as m ON d.manager_id = m.id";
                                        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                        // call the stored procedure
                                        $q = $pdo->query($teamsSQL);
                                        $q->setFetchMode(PDO::FETCH_ASSOC);
                                    } catch (PDOException $e) {
                                        print_r($e->getMessage());
                                        die("Error occurred:" . $e->getMessage());
                                    }
                                    while($t=$q->fetch()): ?>
                                        <option value="<?php echo $t['team_id'];?>"><?php echo $t['division'] . ' - ' . $t['team_name'];?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="card-body" id="opp2-card">
                            </div>
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
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script>
        function addDate() {
            $('#date')[0].value = $('#matchup-date').datetimepicker('date').format('MM/DD/YYYY');
        }

        

        $(function() {

            $('#team1')[0].value = "<?php echo $_GET["team1"]?>";
            $('#team2')[0].value = "<?php echo $_GET["team2"]?>";
            
            $('#matchup-date').datetimepicker({
                format: 'L'
            });

        })
    </script>
</body>
</html>