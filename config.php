<?php
// DATABASE VARIABLES
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "skdocutrack";

// CREATE CONNECTION
$conn = new mysqli($servername, $username, $password, $database);

// CHECK CONNECTION
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// START SESSION
session_start();
?>