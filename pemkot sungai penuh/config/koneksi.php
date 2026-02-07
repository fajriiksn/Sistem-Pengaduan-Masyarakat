<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "db_city_report";

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>