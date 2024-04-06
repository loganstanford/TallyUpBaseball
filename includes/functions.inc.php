<?php

// Calculate Scores function
function scoreEmpty($post, $opp) {
    if (empty($post["c_$opp"]) || empty($post["1b_$opp"]) || empty($post["2b_$opp"]) || empty($post["ss_$opp"]) || empty($post["3b_$opp"]) || 
    empty($post["of1_$opp"]) || empty($post["of2_$opp"]) || empty($post["of3_$opp"]) || empty($post["dh_$opp"]) || empty($post["p_$opp"])) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function getMatchupRoster($post, $opp) {
    $roster= array();
    $roster['c'] = substr($post["c_$opp"], 0, strpos($post["c_$opp"], '&'));
    $roster['1b'] = substr($post["1b_$opp"], 0, strpos($post["1b_$opp"], '&'));
    $roster['2b'] = substr($post["2b_$opp"], 0, strpos($post["2b_$opp"], '&'));
    $roster['ss'] = substr($post["ss_$opp"], 0, strpos($post["ss_$opp"], '&'));
    $roster['3b'] = substr($post["3b_$opp"], 0, strpos($post["3b_$opp"], '&'));
    $roster['of1'] = substr($post["of1_$opp"], 0, strpos($post["of1_$opp"], '&'));
    $roster['of2'] = substr($post["of2_$opp"], 0, strpos($post["of2_$opp"], '&'));
    $roster['of3'] = substr($post["of3_$opp"], 0, strpos($post["of3_$opp"], '&'));
    $roster['dh'] = substr($post["dh_$opp"], 0, strpos($post["dh_$opp"], '&'));
    $roster['p'] = substr($post["p_$opp"], 0, strpos($post["p_$opp"], '&'));
    return $roster;
}

function matchupValid($conn, $date, $team1_id, $team2_id) {
    $sql = "SELECT * FROM schedule WHERE date = ? AND (opp1_id = ? OR opp2_id = ?) AND (opp1_id = ? OR opp2_id = ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../calcultate-scores.php?error=stmtfail");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "siiii", $date, $team1_id, $team1_id, $team2_id, $team2_id);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    }
    else {
        $result = false;
        return $result;
    }
}

function matchupExists($conn, $schedule_id) {

}

function create_matchup($conn, $schedule_id, $date, $team_id, $lineup, $total, $result) {
    $sql = "INSERT INTO matchups (schedule_id, team_id, player_id_c, player_id_1b, player_id_2b, player_id_ss, player_id_3b, player_id_of1, player_id_of2, player_id_of3, player_id_dh, pitching_id, total_points, result) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../calculate-scores.php?error=stmtfail");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "iissssssssssis", $schedule_id, $team_id, $lineup['c'], $lineup['1b'], $lineup['2b'], $lineup['ss'], $lineup['3b'], $lineup['of1'], $lineup['of2'], $lineup['of3'], $lineup['dh'], $lineup['p'], $total, $result);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Registration functions
function formEmpty($name, $email, $pwd, $pwdRepeat) {
    if (empty($name) || empty($email) || empty($pwd) || empty($pwdRepeat)) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function accountEmpty($name, $email) {
    if (empty($name) || empty($email)) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function formEmptyLogin($email, $pwd) {
    if (empty($email) || empty($pwd)) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function invalidEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function pwdNotMatch($pwd, $pwdRepeat) {
    if ($pwd !== $pwdRepeat) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function emailExists($conn, $email) {
    $sql = "SELECT * FROM users WHERE user_email = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../register.php?error=stmtfail");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    }
    else {
        $result = false;
        return $result;
    }
    //mysqli_stmt_close($stmt);
}

function createUser($conn, $name, $email, $pwd) {
    $sql = "INSERT INTO users (user_name, user_email, password) VALUES (?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../register.php?error=stmtfail");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPwd);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: ../login.php?register=true");
    exit();

}

function loginUser($conn, $email, $pwd) {
    $emailExists = emailExists($conn, $email);

    if ($emailExists === false) {
        header("Location: ../login.php?error=badEmail");
        exit();
    }

    $pwdHashed = $emailExists['password'];
    $checkPwd = password_verify($pwd, $pwdHashed);

    if ($checkPwd === false) {
        header("Location: ../login.php?error=badPass");
        exit();
    }
    else if ($checkPwd === true) {
        //print_r($emailExists);
        session_start();
        $_SESSION["user_id"] = $emailExists["id"];
        $_SESSION["user_name"] = $emailExists["user_name"];
        $_SESSION["user_email"] = $emailExists["user_email"];
        $_SESSION["team_id"] = $emailExists["team_id"];
        $_SESSION["admin"] = $emailExists["admin"];
        header("Location: ../myteam.php");
        exit();
    }
}

function getUser($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../account.php?error=stmtfail");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    }
    else {
        header("Location: ../account.php?error=noUser");
        exit();
    }
}

function getApprovals($conn) {
    $sql = "SELECT * FROM team_approvals WHERE approved_by_id IS NULL ORDER BY created DESC;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../account.php?error=stmtfail");
        exit();
    }

    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);

    if ($rows = mysqli_fetch_all($resultData)) {
        return($rows);
    }
    else {
        return $rows = [];
    }
}

function updateTeam($conn, $user_id, $team_id) {
    $sql = "INSERT INTO team_approvals (user_id, team_id) VALUES (?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../account.php?error=stmtfail");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ii", $user_id, $team_id);
    mysqli_stmt_execute($stmt);

    //$result = mysqli_stmt_get_result($stmt);


    if (mysqli_stmt_errno($stmt) == 0) {
        header("Location: ../account.php?success=teamUpdate");
        exit();
    }
    else {
        header("Location: ../account.php?error=teamupdatefail");
        exit();
    }
}

function updateUser($conn, $user_id, $name, $email) {
    $sql = "UPDATE users SET user_name = ?, user_email = ? WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../account.php?error=stmtfail");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
    $result = mysqli_stmt_execute($stmt);

    if ($result === true) {
        if ($user = getUser($conn, $user_id)) {
            session_start();
            unset($_SESSION['user_name']);
            unset($_SESSION['user_email']);
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
        }
        else {
            header("Location: ../account.php?success=acctUpdateNoInfo");
            exit();
        }

        header("Location: ../account.php?success=acctUpdate");
        exit();
    }
    else {
        header("Location: ../account.php?error=acctUpdateFail");
        exit();
    }
}

function checkLogin($con) {
    if (isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE user_id = '$id' LIMIT 1";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }
    // Redirect to login
    header("Location: login.php");
    die;
}

function redirectHTTPS() {
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
        $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
        exit;
    }
}

function showPlayerName($row, $injuryIds, $lineup) {
    $playerNameHtml = '';
    $bbrefRoot = "https://www.baseball-reference.com/players/";
    $bbrefPath = $row['bbref_id'] ? $row['bbref_id'][0] . "/" . $row['bbref_id'] . ".shtml" : "";
    $bbrefUrl = $bbrefRoot . $bbrefPath;
    $statusClass = 'nav-icon far fa-circle text-';
    $statusClass .= in_array($row['srid'], $lineup) ? 'success' : (isset($lineup) ? 'danger' : 'warning');
    
    $playerName = htmlspecialchars($row['first_name'] . " " . $row['last_name'], ENT_QUOTES, 'UTF-8');
    $teamName = htmlspecialchars($row['team_name'], ENT_QUOTES, 'UTF-8');
    $position = htmlspecialchars(str_replace(' ', ', ', $row['pos']), ENT_QUOTES, 'UTF-8');
    $bats = htmlspecialchars($row['bats'], ENT_QUOTES, 'UTF-8');

    if (!empty($bbrefPath)) {
        $playerNameHtml .= "<a href='$bbrefUrl' target='_blank'>$playerName</a>"; 
    } else {
        $playerNameHtml .= $playerName; 
    }
    
    if (in_array($row['srid'], $injuryIds)) { 
        $status = $row['Status'] == "A" ? 'DTD' : $row['Status'];
        $playerNameHtml .= ' - <a href="#" class="injury-status" style="color: red;" data-toggle="modal" data-target="#' . htmlspecialchars($row['srid'], ENT_QUOTES, 'UTF-8') . '" onclick="showModal(' . "'" . htmlspecialchars($row['srid'], ENT_QUOTES, 'UTF-8') . "')" . '">' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . "</a>";
    }

    $rotowireLink = '<a href="https://www.rotowire.com/baseball/daily-lineups.php" target="_blank"><i class="' . $statusClass . ' ml-2"></i></a>';
    
    // Now combine the HTML parts
    $html = <<<HTML
<div class="player-name">
    $playerNameHtml
    $rotowireLink
</div>
<div class="player-bio text-muted">
    <span class="bio-team">$teamName - </span>
    <span class="bio-pos">$position</span> | 
    <span class="bat-hand" data-toggle"tooltip" title="show splits here">Bats: $bats</span>
</div>
HTML;

    return $html;
}

function showPitching($row) {
    $bbrefroot = "https://www.baseball-reference.com/teams/";
    $bbrefpath = $row['abbr'] . "/" . date("Y") . ".shtml#all_team_pitching";
    $bbrefurl = $bbrefroot . $bbrefpath;
    echo "<a href='$bbrefurl' target='_blank'>" . $row['name'] . "</a>";
}

function getLineup($g) {
    if ($g['l1']) {
        $lineup = array($g['l1'], $g['l2'],$g['l3'],$g['l4'],$g['l5'],$g['l6'],$g['l7'],$g['l8'],$g['l9']);
    } else {
        $lineup = "No lineup";
    }
    return $lineup;
}

function showTodaysGame($row) {
    echo '<div class="row">';
    echo '<div class="col-6">';
    if (isset($row['player_id'])) {
        echo '<a onclick="showModal(' . "'" . $row['player_id'] . "-split')". '">' . $row['Opponent'] . " " . ltrim($row['Time'], "0") . '</a>';                                         
    }
    else {
        echo '<a>' . $row['Opponent'] . " " . ltrim($row['Time'], "0") . '</a>';                                               
    }
    
    if ($row['probable_starter'] or $row['starter']) {
        echo '<div class="starter text-muted">';
        echo $row['probable_starter'] . ' (' . $row['throws'] . ') - ';
        echo "ERA: " . $row['probable_starter_era'];
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="col-6 weather">';
    if ($row['v_type'] == 'outdoor') {
        switch($row['condition']) {
            case "Sunny":
                echo '<img class="weather-icon visible-sm-block hidden-sm" src="weather_icons/sunny.png">';
                break;
            case "Partly cloudy":
                echo '<img class="weather-icon" src="weather_icons/partly_sunny.png">';
                break;
            case "Overcast":
                echo '<img class="weather-icon" src="weather_icons/overcast.png">';
                break;
            case "Cloudy":
                echo '<img class="weather-icon" src="weather_icons/cloudy.png">';
                break;
            case "Light rain shower":
                echo '<img class="weather-icon" src="weather_icons/showers.png">';
                break;
            case "Patchy rain possible":
                echo '<img class="weather-icon" src="weather_icons/rain.png">';
                break;
            default:
                echo "No weather icon found";
        }
        
        if (strpos($row['condition'], 'rain') !== false) {
            echo '<span class= "text-danger">' . $row['condition'] . "</span>";
            echo '<div class="weather-summary text-danger">' . $row['temp'] . '°, ' . 'Wind: ' . $row['wind_speed'] . ' ' . $row['wind_direction'] . '</div>';
        }
        else {
            echo $row['condition'];
            echo '<div class="weather-summary text-muted">' . $row['temp'] . '°, ' . 'Wind: ' . $row['wind_speed'] . ' ' . $row['wind_direction'] . '</div>';
        }
    }
    if ($row['v_type'] == 'indoor' or $row['v_type'] == 'retractable') {
        echo '<img src="dome.png" width="36px">&nbsp;&nbsp;Dome';
    }
    echo '</div>';
    echo '</div>';
}

function showSplits($throws, $day_night, $home_away, $venue, $row_array) {
    echo "<h5>Splits for next game</h5>";
    ?>
    <table class="career-stats mb-4">
        <thead>
            <th>Split</th>
            <th>AB</th>
            <th>AVG</th>
            <th>OBP</th>
            <th>SLG</th>
            <th>OPS</th>
        </thead>
        <tbody>
            <?php
            // First loop through and assign to vars to evaluate
            foreach ($row_array as $row) {
                switch($row['split_type']) {
                    case 1: 
                        $rhp_ops = $row['ops'];
                        break;
                    case 2:
                        $lhp_ops = $row['ops'];
                        break;
                    case 3:
                        $home_ops = $row['ops'];
                        break;
                    case 4:
                        $away_ops = $row['ops'];
                        break;
                    case 5:
                        continue;
                    case 6:
                        $day_ops = $row['ops'];
                        break;
                    case 7:
                        $night_ops = $row['ops'];
                        break;
                    default:
                        continue;
                }
            }


            foreach ($row_array as $row) {
                echo "<tr>";
                if ($throws == 'R' && $row['split_type'] == 1) {
                    if ($rhp_ops - $lhp_ops > 0.200) {
                        echo '<td class="text-success">vs. RHP</td>';
                    }
                    else if ($rhp_ops - $lhp_ops < -0.200) {
                        echo '<td class="text-danger">vs. RHP</td>';
                    }
                    else {
                        echo '<td class="">vs. RHP</td>';
                    }
                    
                }
                else if ($throws == 'L' && $row['split_type'] == 2) {
                    $split = 'vs. LHP';
                    if ($lhp_ops - $rhp_ops > 0.200) {
                        echo '<td class="text-success">' . $split . '</td>';
                    }
                    else if ($lhp_ops - $rhp_ops < -0.200) {
                        echo '<td class="text-danger">' . $split . '</td>';
                    }
                    else {
                        echo '<td class="">' . $split .'</td>';
                    }
                }
                else if ($home_away == 'home' && $row['split_type'] == 3) {
                    $split = 'Home';
                    if ($home_ops - $away_ops > 0.200) {
                        echo '<td class="text-success">' . $split . '</td>';
                    }
                    else if ($home_ops - $away_ops < -0.200) {
                        echo '<td class="text-danger">' . $split . '</td>';
                    }
                    else {
                        echo '<td class="">' . $split .'</td>';
                    }
                }
                else if ($home_away == 'away' && $row['split_type'] == 4) {
                    $split = 'Away';
                    if ($away_ops - $home_ops > 0.200) {
                        echo '<td class="text-success">' . $split . '</td>';
                    }
                    else if ($away_ops - $home_ops < -0.200) {
                        echo '<td class="text-danger">' . $split . '</td>';
                    }
                    else {
                        echo '<td class="">' . $split .'</td>';
                    }
                }
                else if ($row['split_type'] == 5 && $venue <> $row['venue_id']) {
                    continue;
                }
                else if ($row['split_type'] == 5 && $venue == $row['venue_id']) {
                    echo "<td>" . $row['name'] . "</td>";
                }
                else if ($day_night == 'D' && $row['split_type'] == 6) {
                    $split = 'Day';
                    if ($day_ops - $night_ops > 0.200) {
                        echo '<td class="text-success">' . $split . '</td>';
                    }
                    else if ($day_ops - $night_ops < -0.200) {
                        echo '<td class="text-danger">' . $split . '</td>';
                    }
                    else {
                        echo '<td class="">' . $split .'</td>';
                    }
                }
                else if ($day_night == 'N' && $row['split_type'] == 7) {
                    $split = 'Night';
                    if ($night_ops - $day_ops > 0.200) {
                        echo '<td class="text-success">' . $split . '</td>';
                    }
                    else if ($night_ops - $day_ops < -0.200) {
                        echo '<td class="text-danger">' . $split . '</td>';
                    }
                    else {
                        echo '<td class="">' . $split .'</td>';
                    }
                }
                else {
                    continue;
                }
                echo "<td>" . $row['ab'] . "</td>";
                echo "<td>" . $row['avg'] . "</td>";
                echo "<td>" . $row['obp'] . "</td>";
                echo "<td>" . $row['slg'] . "</td>";
                echo "<td>" . $row['ops'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <?php
    echo "<h5>2023 Splits</h5>";
    ?>
    <table class="career-stats">
        <thead>
            <th>Split</th>
            <th>AB</th>
            <th>AVG</th>
            <th>OBP</th>
            <th>SLG</th>
            <th>OPS</th>
        </thead>
        <tbody>
            <?php
            foreach ($row_array as $row) {
                echo "<tr>";
                switch($row['split_type']) {
                    case 1:
                        echo "<td>vs. RHP</td>";
                        break;
                    case 2:
                        echo "<td>vs. LHP</td>";
                        break;
                    case 3:
                        echo "<td>Home</td>";
                        break;
                    case 4:
                        echo "<td>Away</td>";
                        break;
                    case 6:
                        echo "<td>Day</td>";
                        break;
                    case 7:
                        echo "<td>Night</td>";
                        break;
                }
                if ($row['split_type'] == 5) {
                    continue;
                }
                echo "<td>" . $row['ab'] . "</td>";
                echo "<td>" . $row['avg'] . "</td>";
                echo "<td>" . $row['obp'] . "</td>";
                echo "<td>" . $row['slg'] . "</td>";
                echo "<td>" . $row['ops'] . "</td>";
                echo "</tr>";
            }
            ?>

        </tbody>
    </table>
    <?php
}

function splitModal($throws, $day_night, $home_away, $venue, $row_array) {
    echo '<div class="modal fade" id="'. $row_array[0]['player_id'] .'-split" tabindex="-1" role="dialog" aria-hidden="true">';
    echo '<div class="modal-dialog modal-dialog-scrollable" role="document">';
    echo '<div class="modal-content">';
    echo '<div class="modal-header">';
    echo '<h4 class="modal-title" id="exampleModalScrollableTitle">';
    echo $row_array[0]['first_name'] . " " . $row_array[0]['last_name'] . " Splits</h4>";
    echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
    echo '<div class="modal-body">';
    echo showSplits($throws, $day_night, $home_away, $venue, $row_array);
    echo '</div>';
    echo '<div class="modal-footer">';
    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}