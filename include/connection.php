<?php
$conn = new mysqli("localhost", "root", "", "pkulogin");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
