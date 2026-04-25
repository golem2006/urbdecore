<?php
session_start();
require_once('config.php');
if (!isset($_SESSION['userId']) && !isset($_GET['prodId'])) {
    header('Location: ../index.php');
    exit();
}

$prodId = intval($_GET['prodId']);
$userId = intval($_SESSION['userId']);

$stmt = $conn->prepare("INSERT INTO `cart`(`prodId`, `userId`) VALUES (?, ?)");
$stmt->bind_param('ii', $prodId, $userId);
$stmt->execute();
header('Location: ../index.php');
exit();
?>