<?php
require_once 'dbconfig.inc.php';
require_once 'functions.inc.php';

if (isset($_POST["submit"])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $team_id = $_POST['team_id'];

    // Check for any empty fields
    if (accountEmpty($name, $email) !== false) {
        header("Location: ../account.php?error=empty");
        exit();
    }

    // Check if email is valid
    if (invalidEmail($email) !== false) {
        header("Location: ../account.php?error=invalidEmail");
        exit();
    }

    $user = getUser($conn, $user_id);

    if ($team_id != $user["team_id"]) {
        updateTeam($conn, $user_id, $team_id);
    }

    if ($name != $user['user_name'] || $email !== $user["user_email"]) {
        updateUser($conn, $user_id, $name, $email);
    }

}
else {
    header("Location: ../account.php?isset=false");
}
