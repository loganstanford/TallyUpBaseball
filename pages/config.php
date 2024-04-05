<?php
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
    echo '<div class="player-name">';
    if ($row['bbref'] != NULL) {
        $bbrefroot = "https://www.baseball-reference.com/players/";
        $path = $row['bbref'][0] . "/" . $row['bbref'] . ".shtml";
        $bbrefurl = $bbrefroot . $path;
        echo "<a href='$bbrefurl' target='_blank'>" . $row['First Name'] . " " . $row['Last Name'] . "</a>"; 
    }
    else {
        echo  $row['First Name'] . " " . $row['Last Name']; 
    }
    
    if (in_array($row['srid'], $injuryIds)) { 
        $srid = $row['srid'];
        $status = $row['Status'] == "A" ? 'DTD' : $row['Status'];
        echo ' - <a href="#" class="injury-status" style="color: red;" data-toggle="modal" data-target="#' . $srid . '" onclick="showModal(' . "'" . $srid . "')" . '">' . $status . "</a>";
    }
    echo "<a href='https://www.rotowire.com/baseball/daily-lineups.php' target='_blank'>";
    if ($lineup != "No lineup") {
        
        if (in_array($row['srid'], $lineup)) {
            echo '<i class="nav-icon far fa-circle text-success ml-2"></i>';
        }
        else {
            echo '<i class="nav-icon far fa-circle text-danger ml-2"></i>';
        }
    }
    else {
        echo '<i class="nav-icon far fa-circle text-warning ml-2"></i>';
    }
    echo "</a>";
    echo '</div>';
    echo '<div class="player-bio text-muted">';
    echo '<span class="bio-team">' . $row['Team'] . " - ";
    echo '</span>';
    echo  '<span class="bio-pos">' . str_replace(' ', ', ', $row['Pos']); 
    echo '</span> | ';
    echo '<span class="bat-hand" data-toggle"tooltip" title="show splits here">Bats: ' . $row['bats'] . '</span>';
    echo '</div>';
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
    echo '<a href="#" onclick="showModal(' . "'" . $row['player_id'] . "-split')". '">' . $row['Opponent'] . " " . ltrim($row['Time'], "0") . '</a>';                                               
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
            foreach ($row_array as $row) {
                echo "<tr>";
                
                if ($throws == 'R' && $row['split_type'] == 1) {
                    echo "<td>vs. RHP</td>";
                }
                else if ($throws == 'L' && $row['split_type'] == 2) {
                    echo "<td>vs. LHP</td>";
                }
                else if ($home_away == 'home' && $row['home_away'] == 3) {
                    echo "<td>Home</td>";
                }
                else if ($home_away == 'away' && $row['home_away'] == 4) {
                    echo "<td>Home</td>";
                }
                else if ($row['split_type'] == 5 && $venue <> $row['venue_id']) {
                    continue;
                }
                else if ($row['split_type'] == 5 && $venue == $row['venue_id']) {
                    echo "<td>" . $row['name'] . "</td>";
                }
                else if ($day_night == 'D' && $row['split_type'] == 6) {
                    echo "<td>Day</td>";
                }
                else if ($day_night == 'N' && $row['split_type'] == 7) {
                    echo "<td>Night</td>";
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
    echo '</div>';
    echo '<div class="modal-footer">';
    echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}