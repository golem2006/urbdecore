document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.querySelector('.filter-toggle');
    const productsContainer = document.querySelector('.products');
    const filterContainer = document.querySelector('.filters-sidebar');
    
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
});