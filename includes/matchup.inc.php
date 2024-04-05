<?php
// Require files
require_once 'dbconfig.inc.php';
require_once 'functions.inc.php';

$date = date_format(date_create_from_format('m/d/Y', $_POST['date']), 'Y-m-d');
print_r($date);
// Check if this was accessed by submit
if (!isset($_POST['submit'])) {
    header("Location: ../calculate-scores.php");
    exit();
}

// Check if any fields empty
if (scoreEmpty($_POST, 1)) {
    printf("Team 1 Score is empty");
    // Redirect back to scores
    header("Location: ../calculate-scores.php?error=empty");
    exit();
}

if (scoreEmpty($_POST, 2)) {
    printf("Team 2 Score is empty");
    // Redirect back to scores
    header("Location: ../calculate-scores.php?error=empty");
    exit();
}

// Check if matchup is valid
if (!$schedule = matchupValid($conn, $date, $_POST['team1'], $_POST['team2'])) {
    header('Location: ../calculate-scores.php?error=invalidmatchup');
    exit();
}

print_r($schedule);

// Check if matchup score already exists in database
/* if (matchupExists($conn, $schedule['id'])) {
    header("Location: ../calculate-scores.php?error=scoreExists");
} */

// Determine winner
if ($_POST['total_1'] > $_POST['total_2']) {
    $team1_result = 'W';
    $team2_result = 'L';
}
else {
    $team1_result = 'L';
    $team2_result = 'W';
}
try {
    // Team 1
    $team1 = getMatchupRoster($_POST, '1');
    create_matchup($conn, $schedule['id'], $date, $_POST['team1'], $team1, $_POST['total_1'], $team1_result);
    //print_r($team1);

    // Team 2
    $team2 = getMatchupRoster($_POST, '2');
    create_matchup($conn, $schedule['id'], $date, $_POST['team2'], $team2, $_POST['total_2'], $team2_result);
    //print_r($team2);
    header("Location: ../calculate-scores.php?success=true");
}
catch (Exception $e) {
    header("Location: ../calculate-scores.php?error=savefailed");
    echo $e->getMessage();
}
