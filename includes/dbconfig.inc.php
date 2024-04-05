<?php
$host = "localhost"; 
$username = "c0_baseball";  // Database username
$password = "3!cZ6QfrREkoT";   // Database password
$dbname = "c0baseball";   // Database NAME
$accu_key = '16aAI8NEyHK5gOe3MHTsHtIauzgcUGSc';

if (!$conn = mysqli_connect($host,$username,$password,$dbname)) {
    die("Failed to connect to database server");
}