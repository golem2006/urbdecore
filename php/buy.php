<?php
session_start();
require_once('config.php');
if (!isset($_SESSION['userId']) && !isset($_GET['cartId'])) {
    header('Location: ../index.php');
    exit();
}

$cartId = intval($_GET['cartId']);
$userId = intval($_SESSION['userId']);

$stmt = $conn->prepare("DELETE FROM `cart` WHERE `id` = ?");
$stmt->bind_param('i', $cartId);
$stmt->execute();
header('Location: ../cart.php');
exit();
?>