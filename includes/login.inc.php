<?php
require_once 'dbconfig.inc.php';
require_once 'functions.inc.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pwd = $_POST['password'];

    // Check for any empty fields
    if (formEmptyLogin($email, $pwd) !== false) {
        header("Location: ../login.php?error=formEmpty");
        exit();
    }

    loginUser($conn, $email, $pwd);
}
else {
    header("Location: ../login.php");
    exit();
}