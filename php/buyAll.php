<?php
session_start();
require_once('config.php');
if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit();
}

$userId = intval($_SESSION['userId']);

$stmt = $conn->prepare("DELETE FROM `cart` WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
header('Location: ../cart.php');
exit();
?>