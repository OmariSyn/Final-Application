<?php

if (session_status() == PHP_SESSION_NONE){
    session_start();

}




$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname = 'cart_database';


$conn = mysqli_connect($hostname, $username, $password, $dbname );




if(!$conn){
    die("Database Connnection Failed: " . mysqli_connect_error());

}








?>







