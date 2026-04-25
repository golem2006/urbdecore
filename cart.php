<?php
session_start();

require_once('php/config.php');

if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit();
}

    $stmt = $conn->prepare("SELECT `id`, `pasw` FROM `users` WHERE `login` = ?");
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hash);
    $stmt->fetch();
    if (password_verify($pasw, $hash)) {
        $_SESSION['userId'] = $id;
        $_SESSION['login'] = $login;
        header('Location: index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="styles.css">
		<title>Urban Decor</title>
	</head>
<body>
    <?php

    ?>
<div class="container">
			<header class="main-header">
				<a href="index.php" class="logo">Urban Decor</a>
			</header>
			
			<div class="content-wrapper"></div>

			<footer class="footer">
				<div class="footer-content">
					<div class="footer-section">
						<h4>Shop</h4>
						<ul>
							<li><a href="#">New Arrivals</a></li>
							<li><a href="#">Best Sellers</a></li>
							<li><a href="#">Gift Cards</a></li>
						</ul>
					</div>
					
					<div class="footer-section">
						<h4>Customer Service</h4>
						<ul>
							<li><a href="#">Shipping Info</a></li>
							<li><a href="#">Returns</a></li>
							<li><a href="#">Contact Us</a></li>
						</ul>
					</div>
					
					<div class="footer-section">
						<h4>About Us</h4>
						<ul>
							<li><a href="#">Our Story</a></li>
							<li><a href="#">Sustainability</a></li>
							<li><a href="#">Careers</a></li>
						</ul>
					</div>
				</div>
				
				<div class="footer-bottom">
					<p>2025 &copy; Urban Decor. All rights reserved.</p>
				</div>
			</footer>
		</div>
	</body>
</html>