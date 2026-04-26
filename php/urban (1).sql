-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4:3306
-- Время создания: Апр 26 2026 г., 18:43
-- Версия сервера: 8.4.8
-- Версия PHP: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `urban`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `prodId` int NOT NULL,
  `userId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `cart`
--

INSERT INTO `cart` (`id`, `prodId`, `userId`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `rating` tinyint UNSIGNED DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `lowPrice` decimal(10,2) DEFAULT '0.00',
  `sale` tinyint UNSIGNED DEFAULT '0',
  `new` tinyint UNSIGNED DEFAULT '0',
  `comsValue` int UNSIGNED DEFAULT '0',
  `imgHref` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `rating`, `price`, `lowPrice`, `sale`, `new`, `comsValue`, `imgHref`) VALUES
(1, 'Промышленный журнальный столик', 'Фурнитура', 4, 14000.00, 12000.00, 0, 0, 4, 'img/industrialTable.jpg'),
(2, 'Минималистичный торшер', 'Освещение', 5, 1800000.00, 0.00, 0, 0, 18, 'img/minimalistLamp.jpg'),
(3, 'Шерстяной плед', 'Текстиль', 4, 7920.00, 0.00, 0, 1, 12, 'img/wool-blend-throw.jpg'),
(4, 'Современная книжная полка', 'Мебель', 3, 17910.00, 15210.00, 0, 0, 8, 'img/modern-bookshelf.jpg'),
(5, 'Набор керамических ваз', 'Декор', 5, 5310.00, 0.00, 0, 0, 31, 'img/ceramic-vase-set.jpg'),
(6, 'Кожаное кресло', 'Мебель', 4, 53910.00, 44910.00, 1, 0, 27, 'img/leather-armchair.jpg'),
(7, 'Латунный настольный светильник', 'Освещение', 5, 11610.00, 0.00, 0, 0, 15, 'img/brass-table-lamp.jpg'),
(8, 'Льняные шторы', 'Текстиль', 4, 11610.00, 10440.00, 0, 0, 22, 'img/linen-curtains.jpg'),
(9, 'Супер шторы', 'Текстиль', 5, 13500.00, 12750.00, 0, 0, 28, 'img/super-curtains.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(255) NOT NULL,
  `pasw` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `pasw`) VALUES
(1, 'w', '$2y$12$owbsmOSf8ucYGSGXvre4aeHEopJIRyAeJ13Jg0/2i1udULF7xngTa');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
