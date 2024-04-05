<?php
// Require files
require_once 'dbconfig.inc.php';
require_once 'functions.inc.php';

if (isset($_POST["submit"])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pwd = $_POST['password'];
    $pwdRepeat = $_POST['passwordRepeat'];

    // Check for any empty fields
    if (formEmpty($name, $email, $pwd, $pwdRepeat) !== false) {
        header("Location: ../register.php?error=formEmpty");
        exit();
    }

    // Check if email is valid
    if (invalidEmail($email) !== false) {
        header("Location: ../register.php?error=invalidEmail");
        exit();
    }

    // Check if passwords match
    if (pwdNotMatch($pwd, $pwdRepeat) !== false) {
        header("Location: ../register.php?error=pwdMatch");
        exit();
    }

    // Check if email already taken
    if (emailExists($conn, $email) !== false) {
        header("Location: ../register.php?error=emailExists");
        exit();
    }

    createUser($conn, $name, $email, $pwd);

}
else {
    header("Location: ../register.php?isset=false");
}