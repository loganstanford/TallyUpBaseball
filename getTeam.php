<?php 
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

// HTTPS Redirector
//redirectHTTPS();
$id = intval($_GET['id']);
$date = $_GET['date'];
$opp = $_GET['opp'];
//print_r("TeamId: " . $id);
//print_r("Date: " . $date);

if (!$conn) {
  die('Could not connect: ' . mysqli_error($conn));
}

mysqli_select_db($conn,"c0baseball");
//$sql="CALL getPositionStatsOnRange('" . $id . "', '" . $date . "', '" . $date . "')";
//$result = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <?php include_once 'head.php'; ?>
    <?php include_once 'styles.php'; ?>
    <script>
        function updatePoints(points, position) {
            $(`#${position}-points`).innerHTML = points.substring(points.indexOf('&')+1);
        }
    </script>
    <style>
        .player-position {
            display: inline-flex;
            width: 30px;
        }
        .player-select {
            display: inline-flex;
            width: 200px;
        }
    </style>

</head>
<table id="opp-<?php echo $id;?>" class="datatable w-100">
    <thead>
        <tr>
            <th>Player</th>
            <th>Points</th>
        </tr>
    </thead>
    <tbody>
        <!-- Catcher - C -->
        <tr id="row-catcher">
        <td class="py-2">
        <?php 
            $sql="CALL getPositionStatsOnRange(" . $id . ", 'C', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            //print_r($conn);
            $c_result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">C:</div>
            <select name="c_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'catcher', <?php echo $id; ?>)">
            <option disabled selected value="">Select catcher...</option>
            <?php
            while($row = mysqli_fetch_array($c_result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            mysqli_free_result($c_result);
            ?>
            </select></td>
            <td class="points" id="catcher-points">
                -
            </td>
        </tr>
        <!-- First Base - 1B -->
        <tr id="row-1B">
        <td class="py-2">
        <?php
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $fb_sql="CALL getPositionStatsOnRange(" . $id . ", '1B', '" . $date . "', '" . $date . "')";
            $fb_result = mysqli_query($conn,$fb_sql);?>
            <div class="player-position">1B:</div>
            <select name="1b_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, '1B', <?php echo $id; ?>)">
            <option disabled selected value="">Select 1B...</option>
            <?php
            while($fb_row = mysqli_fetch_array($fb_result)) {
                ?>
                <option value="<?php echo $fb_row['srid'] . '&' . $fb_row['Total points'];?>"><?php echo $fb_row['First Name'] . " " . $fb_row['Last Name'] . " - " . $fb_row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="1B-points">
                -
            </td>
        </tr>
        <!-- Second Base - 2B -->
        <tr id="row-2B">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getPositionStatsOnRange(" . $id . ", '2B', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $sb_result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">2B:</div>
            <select name = "2b_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, '2B', <?php echo $id; ?>)">
            <option disabled selected value="">Select 2B...</option>
            <?php
            while($row = mysqli_fetch_array($sb_result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="2B-points">
                -
            </td>
        </tr>
        <!-- Shortstop - SS -->
        <tr id="row-SS">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getPositionStatsOnRange(" . $id . ", 'SS', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">SS:</div>
            <select name="ss_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'SS', <?php echo $id; ?>)">
            <option disabled selected value="">Select SS...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="SS-points">
                -
            </td>
        </tr>
        <!-- Third Base - 3B -->
        <tr id="row-3B">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getPositionStatsOnRange(" . $id . ", '3B', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">3B:</div>
            <select name="3b_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, '3B', <?php echo $id; ?>)">
            <option disabled selected value="">Select 3B...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="3B-points">
                -
            </td>
        </tr>
        <!-- Outfield 1 - OF -->
        <tr id="row-OF">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getPositionStatsOnRange(" . $id . ", 'OF', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">OF:</div>
            <select name="of1_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'OF1', <?php echo $id; ?>)">
            <option disabled selected value="">Select OF...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="OF1-points">
                -
            </td>
        </tr>
        <!-- Outfield 2 - OF -->
        <tr id="row-OF">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getPositionStatsOnRange(" . $id . ", 'OF', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">OF:</div>
            <select name="of2_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'OF2', <?php echo $id; ?>)">
            <option disabled selected value="">Select OF...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="OF2-points">
                -
            </td>
        </tr>
        <!-- Outfield 3 - OF -->
        <tr id="row-OF">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getPositionStatsOnRange(" . $id . ", 'OF', '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">OF:</div>
            <select name="of3_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'OF3', <?php echo $id; ?>)">
            <option disabled selected value="">Select OF...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="OF3-points">
                -
            </td>
        </tr>
        <!-- Designated Hitter - DH -->
        <tr id="row-DH">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getTeamStatsOnRange(" . $id . ", '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">DH:</div>
            <select name="dh_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'DH', <?php echo $id; ?>)">
            <option disabled selected value="">Select DH...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['First Name'] . " " . $row['Last Name'] . " - " . $row['Team'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="DH-points">
                -
            </td>
        </tr>
        <!-- Pitching - P -->
        <tr id="row-P">
            <td class="py-2">
            <?php 
            $conn = mysqli_connect($host,$username,$password,$dbname);
            $sql="CALL getTeamPitchStats(" . $id . ", '" . $date . "', '" . $date . "')";
            //print_r($sql);
            $result = mysqli_query($conn,$sql);
            //print_r($c_result); ?>
            <div class="player-position">P:</div>
            <select name="p_<?php echo $opp;?>" class="form-control player-select" onchange="updatePoints(this.value, 'P', <?php echo $id; ?>)">
            <option disabled selected value="">Select P...</option>
            <?php
            while($row = mysqli_fetch_array($result)) {
                ?>
                <option value="<?php echo $row['srid'] . '&' . $row['Total points'];?>"><?php echo $row['name'];?></option>
            <?php
            }
            ?>
            </select></td>
            <td class="points" id="P-points">
                -
            </td>
        </tr>
    </tbody>
    <tfoot>
        <td clas="px-4" style="text-align: right;"><input name="total_<?php echo $opp;?>" type="hidden" id="total" /></td><td></td>
    </tfoot>
</table>