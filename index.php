<?php
session_start();
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
        require_once('php/config.php');
        $countCart = 0;
        if (isset($_SESSION['userId'])) {
            $userId = intval($_SESSION['userId']);
            $stmt = $conn->prepare("SELECT COUNT(id) FROM `cart` WHERE `userId` = ?");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($countCart);
            $stmt->fetch();
        }

        $productsHtml = '';
        // Проверяем, были ли GET-параметры 'cat' или 'offer'
        if ($_SERVER['REQUEST_METHOD'] == "GET" && (isset($_GET['cat']) || isset($_GET['offer']))) {

            $categoryParam = $_GET['cat'] ?? ''; // Используем ?? для безопасного получения значения
            $offerParam = $_GET['offer'] ?? '';

            // Обработка нескольких вариантов категории (через запятую)
            $categories = [];
            if (!empty($categoryParam)) {
                // Разделяем строку категорий на массив, удаляя пробелы по краям каждого элемента
                $categories = array_map('trim', explode(',', $categoryParam));
                // Удаляем пустые строки, если они появились после explode
                $categories = array_filter($categories);
            }
        
            $queryParts = []; // Массив для частей SQL-запроса (WHERE ... AND ...)
            $bindParams = []; // Массив для параметров bind_param
            $paramTypes = ''; // Строка для типов параметров bind_param
        
            // 1. Обработка категорий
            if (!empty($categories)) {
                // Создаем условие для каждой категории
                $categoryPlaceholders = implode(',', array_fill(0, count($categories), '?'));
                $queryParts[] = "`category` IN ($categoryPlaceholders)";
                $paramTypes .= str_repeat('s', count($categories)); // Добавляем 's' для каждой категории (строка)
                $bindParams = array_merge($bindParams, $categories); // Добавляем сами категории в параметры
            }
        
            // 2. Обработка предложений/акций (offer)
            if (!empty($offerParam)) {
                if ($offerParam == 'Sale Items') {
                    $queryParts[] = "`sale` = ?";
                    $paramTypes .= 'i'; // 'i' для целого числа (0 или 1)
                    $bindParams[] = 1;
                } else if ($offerParam == 'New Arrivals') {
                    $queryParts[] = "`new` = ?";
                    $paramTypes .= 'i';
                    $bindParams[] = 1;
                }
                // Дополнительные условия для 'offer' можно добавить здесь
                // Например, если offerParam может быть "Sale Items, New Arrivals"
                // В таком случае, вам нужно будет переработать логику, чтобы правильно обработать OR/AND.
                // Пока что предполагаем, что offerParam - это одно значение.
            }
        
            // Формируем финальный SQL-запрос
            $sql = 'SELECT * FROM `products`';
            if (!empty($queryParts)) {
                $sql .= ' WHERE ' . implode(' AND ', $queryParts);
            }
        
            // Готовим и выполняем запрос
            $queryProducts = $conn->prepare($sql);
        
            // Привязываем параметры, если они есть
            if (!empty($bindParams)) {
                $queryProducts->bind_param($paramTypes, ...$bindParams); // Используем оператор ... для распаковки массива
            }
        
            $queryProducts->execute();
            $result = $queryProducts->get_result(); // Получаем результат запроса
            $productsResult = $result->fetch_all(MYSQLI_ASSOC);

        } else {
            // Если GET-параметра 'filter' нет, получаем все товары
            $queryProducts = $conn->query('SELECT * FROM `products`');
            if ($queryProducts) {
                $productsResult = $queryProducts->fetch_all(MYSQLI_ASSOC);
            } else {
                // Обработка ошибки, если запрос не удался
                error_log("Ошибка при выполнении запроса: " . $conn->error);
                $productsHtml = "<p>Произошла ошибка при загрузке товаров.</p>";
            }
        }

        $maxRangeQuery = $conn->query('SELECT MAX(price) FROM products;');
        $maxRangeResult = $maxRangeQuery->fetch_assoc(); // Максимальная цена товара
        $maxRange = intval($maxRangeResult['MAX(price)']);

        // Генерация HTML для каждого товара
        if (!empty($productsResult)) {
            $maxPrice = null; // Значение из параметра GET
            
            if ($_SERVER['REQUEST_METHOD'] == "GET" && (isset($_GET['maxPrice']))) {
                $maxPrice = $_GET['maxPrice'];
            }
            foreach ($productsResult as $prod) {
                // Предполагаем, что $prod - это ассоциативный массив с ключами:
                // 'id', 'name', 'category', 'rating', 'price', 'lowPrice', 'sale', 'new', 'comsValue', 'imgHref'
            
                $intPrice = intval($prod['price']);
                if ($maxPrice != null & $intPrice > $maxPrice || $intPrice == 0) {
                    continue; // Фильтр на максимальную цену
                }

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
                        <a class="tedNone" href="php/add.php?prodId='.$prod['id'].'"><button class="add-to-cart">В Корзину</button></a>
                    </div>
                ';
            }
        } elseif (empty($productsHtml)) { // Если нет товаров и не было ошибки
            $productsHtml = "<p>Товары не найдены.</p>";
        }
        ?>
		<div class="container">
			<header class="main-header">
				<a href="index.php" class="logo">Urban Decor</a>
				<div class="header-actions">
					<button class="search-btn">Search</button>
                    <?php if (!isset($_SESSION['userId'])) { ?>
					    <a href="login.php"><button class="account-btn">Войти</button></a>
                    <?php } else { 
                        echo (htmlspecialchars($_SESSION['login'])); ?>
                        <a href="php/exit.php"><button class="account-btn">Выйти</button></a>
                        <a href="cart.php"><button class="cart-btn">Корзина (<?php echo htmlspecialchars($countCart) ?>)</button></a>
                        <?php } ?>
					
				</div>
			</header>
			
			<nav class="main-menu">
                <ul>
                    <li><a href="index.php" class="active">Все Предметы</a></li>
                    <li><a href="index.php?cat=Фурнитура">Фурнитура</a></li>
                    <li><a href="index.php?cat=Освещение">Освещение</a></li>
                    <li><a href="index.php?cat=Текстиль">Текстиль</a></li>
                    <li><a href="index.php?cat=Мебель">Мебель</a></li>
                    <li><a href="index.php?cat=Декор">Декор</a></li>
                    <li><a href="index.php?offer=Sale Items">Распродажа</a></li>
                </ul>
            </nav>
			
			<div class="content-wrapper">
				<button class="filter-toggle">Show Filters</button>
				
				<main class="products">
                    <?php echo $productsHtml; ?>
				</main>
				
				<aside class="filters-sidebar">
					<div class="filters-header">
						<h3>Filters</h3>
						<button class="close-filters">&times;</button>
					</div>
					
					<div class="filter-section">
						<div class="filter-title">Категории</div>
						<div class="filter-options">
							<label class="filter-option">
								<input type="checkbox"> Фурнитура
							</label>
							<label class="filter-option">
								<input type="checkbox"> Освещение
							</label>
							<label class="filter-option">
								<input type="checkbox"> Текстиль
							</label>
							<label class="filter-option">
								<input type="checkbox"> Мебель
							</label>
                            <label class="filter-option">
								<input type="checkbox"> Декор
							</label>
						</div>
					</div>
					
					<div class="filter-section">
						<div class="filter-title">Ценовой диапозон</div>
						<div class="price-range">
							<input type="range" min="0" max="<?php echo htmlspecialchars($maxRange); ?>"
                            value="<?php echo htmlspecialchars($maxRange); ?>">
							<div class="range-values">
								<span>₽0</span>
								<span>₽<?php echo htmlspecialchars($maxRange); ?></span>
							</div>
						</div>
					</div>
					
					<div class="filter-section">
						<div class="filter-title">Специальные предложения</div>
						<div class="filter-options">
							<label class="filter-option">
                                <input type="radio" name="special-offer" value="Sale Items"> Sale Items
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="special-offer" value="New Arrivals"> New Arrivals
                            </label>
						</div>
					</div>
					
					<div class="filter-actions">
						<button class="apply-filters">Apply</button>
						<button class="reset-filters">Reset</button>
					</div>
				</aside>
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
        <script src="main.js"></script>
	</body>
</html>