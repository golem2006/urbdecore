<?php
session_start();

require_once('php/config.php');

if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit();
}

    $stmt = $conn->prepare("SELECT `prodId` FROM `cart` WHERE `userId` = ?");
    $stmt->bind_param('i', $_SESSION['userId']);
    $stmt->execute();
   
	$stmt->store_result();

	$cart = [];

	// Если запрос не пустой добавляем каждый элемент в массив
	if ($stmt->num_rows > 0) {
	    $stmt->bind_result($prodId);
	    while ($stmt->fetch()) {
	        $cart[] = $prodId;
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
    <?php
	$productsResultArr = [];
	$productsHtml = '';
	foreach ($cart as $key => $productId) {
		$stmt = $conn->prepare("SELECT * FROM `products` WHERE `id` = ?");
    	$stmt->bind_param('i', $productId);
    	$stmt->execute();
		$result = $stmt->get_result();
        $product = $result->fetch_all(MYSQLI_ASSOC);
		$productsResultArr[] = $product;
	}
	if (!empty($productsResultArr)) {
            foreach ($productsResultArr as $productsResult) {
            	foreach ($productsResult as $prod) {
            	    // Предполагаем, что $prod - это ассоциативный массив с ключами:
            	    // 'id', 'name', 'category', 'rating', 'price', 'lowPrice', 'sale', 'new', 'comsValue', 'imgHref'
				
            	    $intPrice = intval($prod['price']);
				

            	    $originalPrice = $prod['price'];
            	    $lowPrice = $prod['lowPrice'] ?? 0; // Если lowPrice не задан, считаем его 0
				
            	    $displayPrice = '';
            	    $displayOldPrice = '';
            	    $badge = '';
				
            	    // Формирование цен для отображения
            	    if ($prod['sale'] == 1 && $lowPrice > 0 && $originalPrice > $lowPrice) {
            	        $displayOldPrice = '<span class="old-price">$' . $originalPrice . '</span>';
            	        $displayPrice = '₽' . $lowPrice;
            	        $badge = '<div class="product-badge">Sale</div>';
            	    } elseif ($prod['new'] == 1) {
            	        $displayPrice = '₽' . $originalPrice;
            	        $badge = '<div class="product-badge">New</div>';
            	    } elseif ($prod['sale'] == 1 && $lowPrice > 0 && $originalPrice <= $lowPrice) { // Случай, когда sale=1, но lowPrice не ниже originalPrice
            	        $displayPrice = '₽' . $originalPrice;
            	        // В этом случае, если lowPrice не меньше originalPrice, мы не показываем скидку,
            	        // но если есть другой флаг, например 'sale', то можем показать "Sale"
            	        // В данном примере, если sale=1, но lowPrice не уменьшает цену, мы не показываем badge,
            	        // но вы можете добавить логику для отображения "Sale" отдельно.
            	    }
            	    else {
            	        $displayPrice = '₽' . $originalPrice;
            	    }
				
            	    // Формирование рейтинга
            	    $ratingStars = str_repeat('★', $prod['rating']) . str_repeat('☆', 5 - $prod['rating']);
				
            	    // Генерация HTML для одного товара
            	    $productsHtml .= '
            	        <div class="product-card">
            	            ' . ($prod['new'] == 1 ? '<div class="product-badge">New</div>' : '') . '
            	            ' . (($prod['sale'] == 1 && $lowPrice > 0 && $originalPrice > $lowPrice) ? '<div class="product-badge">Sale</div>' : '') . '
            	            ' . (($prod['sale'] == 0 && $lowPrice > 0 && $originalPrice > $lowPrice) ? '<div class="product-badge">-' . round(100 * (1 - $lowPrice / $originalPrice)) . '%</div>' : '') . '
            	            <div class="product-image" style="background-color: #' . (($prod['rating'] ?? 0) % 2 == 0 ? 'f5f5f5' : 'eeeeee') . ';">
            	            <img alt="img'.$prod['id'].'" src="'. $prod['imgHref'].'"></div>
            	            <div class="product-category">' . htmlspecialchars($prod['category']) . '</div>
            	            <h3>' . htmlspecialchars($prod['name']) . '</h3>
            	            <div class="price-container">
            	                ' . $displayOldPrice . '
            	                <span class="price">' . $displayPrice . '</span>
            	            </div>
            	            <div class="product-rating">' . $ratingStars . ' (' . ($prod['comsValue'] ?? 0) . ')</div>
            	            <a class="tedNone" href="php/buy.php?prodId='.$prod['id'].'"><button class="add-to-cart">Купить</button></a>
							<a class="tedNone" href="php/delete.php?prodId='.$prod['id'].'"><button class="add-to-cart delete">Удалить</button></a>
            	        </div>
            	    ';
            	}
			}
        	} elseif (empty($productsHtml)) { // Если нет товаров и не было ошибки
        	    $productsHtml = "<p>Товары не найдены.</p>";
        	}
		
    ?>
<div class="container">
			<header class="main-header">
				<a href="index.php" class="logo">Urban Decor</a>
			</header>
			<h2>Корзина</h2>
			<div class="content-wrapper">
				
				<main class="products">
                    <?php echo $productsHtml; ?>
				</main>
				
			</div>
			<a class="tedNone" href="php/buyAll.php"><button class="add-to-cart buyAll">Купить Всё</button></a>

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