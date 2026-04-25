<?php
session_start();

require_once('php/config.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $login = $_POST['login'];
    $pasw = $_POST['pasw'];
    $hash = password_hash($pasw, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO `users`(`login`, `pasw`) VALUES (?, ?)");
    $stmt->bind_param('ss', $login, $hash);
     // Выполняем запрос
     try {
        $stmt->execute();
        $id = $conn->insert_id; // Получаем ID вставленной записи
        $_SESSION['userId'] = $id;
        $_SESSION['login'] = $login;
        header('Location: index.php');
        exit();
     } catch (\Throwable $th) {
        $error_code = $stmt->errno; // Получаем код ошибки из объекта prepared statement
    
        if ($error_code == 1062) {
            echo "Логин '" . htmlspecialchars($login) . "' занят.<br>";
        } else {
            // Обработка других возможных ошибок выполнения
            echo "Ошибка при выполнении запроса: " . $stmt->error . " (Код: " . $error_code . ")<br>";
        }
     }
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
<div class="container">
			<header class="main-header">
				<a href="index.php" class="logo">Urban Decor</a>
			</header>
			
			<div class="content-wrapper">
                
                <form action="auth.php" method="post">
                    <h2>Регистрация</h2>
                    <input type="text" name="login" placeholder="Логин">
                    <input type="password" name="pasw" placeholder="Пароль">
                    <input type="submit" value="Зарегистрироваться">
                    <i>Есть аккаунт? <a href="login.php">Войти</a></i>
                </form>
                
            </div>

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