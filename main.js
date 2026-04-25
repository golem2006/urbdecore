document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.querySelector('.filter-toggle');
    const productsContainer = document.querySelector('.products');
    const filterContainer = document.querySelector('.filters-sidebar');

    const filtersSidebar = document.querySelector('.filters-sidebar');
    const closeFiltersButton = filtersSidebar.querySelector('.close-filters');
    const applyFiltersButton = filtersSidebar.querySelector('.apply-filters');
    const resetFiltersButton = filtersSidebar.querySelector('.reset-filters');
    const categoriesCheckboxes = filtersSidebar.querySelectorAll('.filter-section:nth-of-type(2) input[type="checkbox"]'); // Чекбоксы категорий
    const priceRangeInput = filtersSidebar.querySelector('.price-range input[type="range"]');
    const specialOfferRadios = filtersSidebar.querySelectorAll('.filter-section:nth-of-type(4) input[type="radio"]'); // Радиокнопки спец. предложений

    const navLinks = document.querySelectorAll('.main-menu a');
    const urlParams = new URLSearchParams(window.location.search);
    const currentUrl = window.location.pathname; // Получаем путь без параметров
    
    filterBtn.addEventListener('click', function() {
        productsContainer.classList.toggle('hide');
        filterContainer.classList.toggle('show_block');

        if (filterBtn.textContent == "Show Filters") {
            filterBtn.textContent = "Hide Filters";
        } else {
            filterBtn.textContent = "Show Filters";
        }
    });

    const rangeInput = document.querySelector('.filter-section input[type="range"]');
    const priceRangeDiv = document.querySelector('.price-range');

    // Создаем новый элемент для отображения текущего значения
    const currentValueDisplay = document.createElement('span');
    currentValueDisplay.classList.add('current-value');
    // Вставляем его сразу после input[type="range"]
    rangeInput.parentNode.insertBefore(currentValueDisplay, rangeInput.nextSibling);

    // Функция для обновления отображаемого значения
    function updateRangeValue() {
        const currentValue = rangeInput.value;
        currentValueDisplay.textContent = `$${currentValue}`;

        // Позиционируем отображаемое значение относительно ползунка
        const rangeRect = rangeInput.getBoundingClientRect();
        const inputLeft = rangeInput.offsetLeft;
        const inputWidth = rangeInput.offsetWidth;
        const percentage = (currentValue - parseInt(rangeInput.min)) / (parseInt(rangeInput.max) - parseInt(rangeInput.min));
        const thumbWidth = 14; // Примерная ширина "большого пальца" ползунка (может отличаться в зависимости от браузера и стилей)
        const leftPosition = inputLeft + (inputWidth * percentage) - (currentValueDisplay.offsetWidth / 2) + (thumbWidth / 2);

        currentValueDisplay.style.position = 'absolute';
        currentValueDisplay.style.left = `${leftPosition}px`;
        currentValueDisplay.style.top = `${rangeInput.offsetTop + rangeInput.offsetHeight + 20}px`; // Небольшой отступ вниз
    }

    // Первоначальное отображение значения при загрузке страницы
    updateRangeValue();

    // Обновляем значение при изменении ползунка
    rangeInput.addEventListener('input', updateRangeValue);

    // Также нужно обновить позицию, если размер окна меняется
    window.addEventListener('resize', updateRangeValue);

    // --- Логика Apply ---
    applyFiltersButton.addEventListener('click', function() {
        const selectedCategories = [];
        categoriesCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedCategories.push(checkbox.parentNode.textContent.trim()); // Получаем текст лэйбла
            }
        });

        const currentPrice = priceRangeInput.value;

        const selectedOffer = document.querySelector('.filter-section:nth-of-type(4) input[type="radio"]:checked');
        const selectedOfferValue = selectedOffer ? selectedOffer.value : '';

        // Формируем URL с GET-параметрами
        let url = window.location.pathname; // Начинаем с пути текущей страницы
        const queryParams = [];

        if (selectedCategories.length > 0) {
            // Для нескольких категорий, используем формат 'cat=Категория1,Категория2'
            queryParams.push(`cat=${encodeURIComponent(selectedCategories.join(','))}`);
        }

        if (currentPrice !== priceRangeInput.max) { // Если цена отличается от максимальной
            queryParams.push(`maxPrice=${currentPrice}`);
        }

        if (selectedOfferValue) {
            queryParams.push(`offer=${encodeURIComponent(selectedOfferValue)}`);
        }

        if (queryParams.length > 0) {
            url += '?' + queryParams.join('&');
        }

        // Переходим по сформированному URL
         window.location.href = url;
    });

        // --- Логика Reset ---
    resetFiltersButton.addEventListener('click', function() {
        // Сброс чекбоксов категорий
        categoriesCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Сброс ползунка цены к максимальному значению (как в HTML)
        const maxPrice = parseInt(priceRangeInput.max);
        priceRangeInput.value = maxPrice;
        updateRangeValue(); // Обновить отображение цены

        // Сброс радиокнопок спец. предложений
        specialOfferRadios.forEach(radio => {
            radio.checked = false;
        });

        // Переход на главную страницу или страницу без фильтров
        // window.location.href = window.location.pathname; // Перезагрузка текущей страницы без GET-параметров
    });

    // Удаляем класс 'active' со всех ссылок перед тем, как добавить его к нужной
    navLinks.forEach(link => {
        link.classList.remove('active');
    });

    // Обработка случая "All Items"
    if (window.location.search === '' || urlParams.toString() === '') {
        document.querySelector('.main-menu a[href="index.php"]').classList.add('active');
    } else {
        // Проверяем каждую ссылку на соответствие текущим GET-параметрам
        navLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            const linkUrl = new URL(linkHref, window.location.origin); // Создаем URL для сравнения

            let isActive = false;

            // Сравниваем параметры
            if (linkUrl.pathname === currentUrl) { // Проверяем, что путь совпадает
                if (linkHref.includes('?cat=')) {
                    // Если ссылка для категории
                    const categoryFromLink = linkHref.split('?cat=')[1].split('&')[0]; // Извлекаем категорию из href ссылки
                    const categoryFromUrl = urlParams.get('cat');
                    if (categoryFromUrl && categoryFromUrl.split(',').includes(categoryFromLink)) {
                        isActive = true;
                    }
                } else if (linkHref.includes('?offer=')) {
                    // Если ссылка для спец. предложения
                    const offerFromLink = linkHref.split('?offer=')[1];
                    const offerFromUrl = urlParams.get('offer');
                    if (offerFromUrl && offerFromUrl === offerFromLink) {
                        isActive = true;
                    }
                }
                // Добавьте сюда обработку других параметров, если они будут
            }

            if (isActive) {
                link.classList.add('active');
            }
        });
    }
});