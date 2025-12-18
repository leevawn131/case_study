<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$motel_id = intval($_GET['id']);

$check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = $user_id AND motel_id = $motel_id");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $user_id AND motel_id = $motel_id");
} else {
    mysqli_query($conn, "INSERT INTO favorites (user_id, motel_id) VALUES ($user_id, $motel_id)");
}

if(isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: dashboard.php");
}
exit();
?>