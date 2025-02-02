window.addEventListener("DOMContentLoaded", function() {
    initializeTransforms();
    setTransforms(0); // Инициализация при загрузке страницы
});

let currentScroll = 0; // Глобальная переменная для хранения текущего "скролла"
let touchStartY = 0; // Начальная позиция касания по Y (для мобильных)

window.addEventListener("wheel", function(event) {
    if (window.innerWidth <= 768) return; //  Не обрабатываем колесо на мобильных

    event.preventDefault(); // Предотвращаем стандартный скролл

    // Обновляем текущий "скролл" на основе вращения колеса мыши
    currentScroll += event.deltaY * 0.75;

    // Ограничиваем currentScroll
    const maxScroll = calculateMaxScroll();
    currentScroll = Math.max(0, Math.min(currentScroll, maxScroll));

    setTransforms(currentScroll); // Применяем изменения при скролле
});

// Добавляем обработчики тач-событий для мобильных устройств
window.addEventListener("touchstart", function(event) {
    if (window.innerWidth > 768) return; // Не обрабатываем тач на десктопах
    touchStartY = event.touches[0].clientY;
});

window.addEventListener("touchmove", function(event) {
    if (window.innerWidth > 768) return; // Не обрабатываем тач на десктопах

    event.preventDefault(); // Предотвращаем стандартный скролл на мобильных

    const touchY = event.touches[0].clientY;
    const touchDelta = touchStartY - touchY; // Разница между начальной и текущей позицией касания

    // Обновляем текущий "скролл" на основе перемещения пальца
    currentScroll += touchDelta;

    // Ограничиваем currentScroll
    const maxScroll = calculateMaxScroll();
    currentScroll = Math.max(0, Math.min(currentScroll, maxScroll));

    setTransforms(currentScroll); // Применяем изменения при скролле

    touchStartY = touchY; // Обновляем начальную позицию касания для следующего перемещения
});

window.addEventListener("touchend", function(event) {
    if (window.innerWidth > 768) return; // Не обрабатываем тач на десктопах
    // Можем добавить тут логику для инерции, если нужно
});

function initializeTransforms() {
    const projectItems = document.querySelectorAll(".project");

    projectItems.forEach((item, index) => {
        const itemHeight = item.offsetHeight;
        const imgContainer = item.querySelector(".layer-thumbnail");
        const textContainer = item.querySelector(".text-wrapper");

        // Применяем начальные стили только если ширина экрана больше 768px
        if (window.innerWidth > 768) {
            // Устанавливаем начальные смещения для каждого элемента
            const initialItemOffset = itemHeight * index;
            const initialImgOffset = itemHeight * (1 - index);

            item.style.transform = `translate3d(0, ${initialItemOffset}px, 0)`;
            imgContainer.style.transform = `translate3d(0, ${initialImgOffset - itemHeight}px, 0)`;
            imgContainer.style.top = `-${itemHeight}px`;
            imgContainer.style.position = `absolute`;

            textContainer.style.transform = `translate3d(0, ${initialImgOffset - itemHeight}px, 0)`;
            textContainer.style.top = `-${itemHeight}px`;
            textContainer.style.position = `absolute`;

            // Управление z-index для перекрытия
            item.style.zIndex = projectItems.length - index;
        } else {
            // Сбрасываем стили, если ширина меньше или равна 768px
            item.style.transform = "";
            imgContainer.style.transform = "";
            imgContainer.style.top = "";
            imgContainer.style.position = "";
            textContainer.style.transform = "";
            textContainer.style.top = "";
            textContainer.style.position = "";
            item.style.zIndex = "";
        }
    });
}

function setTransforms(scrollY) {
    const projectItems = document.querySelectorAll(".project");
    const windowHeight = window.innerHeight;

    projectItems.forEach((item, index) => {
        const itemHeight = item.offsetHeight;
        const imgContainer = item.querySelector(".layer-thumbnail");
        const textContainer = item.querySelector(".text-wrapper");

        // Смещение для текущего элемента
        const itemOffset = itemHeight * index - scrollY;

        // Смещение для изображения и текста внутри текущего элемента
        const imgOffset = scrollY + itemHeight * (1 - index);
        const textOffset = scrollY + itemHeight * (1 - index);

        // Применяем стили
        item.style.transform = `translate3d(0, ${itemOffset}px, 0)`;
        imgContainer.style.transform = `translate3d(0, ${imgOffset}px, 0)`;
        textContainer.style.transform = `translate3d(0, ${textOffset}px, 0)`;

        // Параллакс для фона
        const itemRect = item.getBoundingClientRect();
        const isVisible = itemRect.top + itemHeight > 0 && itemRect.top < windowHeight;
        let parallaxOffset = 0;
      
        if (window.innerWidth <= 768) {
            // Мобильные устройства (<= 768px)            
            parallaxOffset = itemRect.top * 0.25;
            item.style.backgroundPositionY = `-${parallaxOffset}px`;
        } else {
            // Десктопы (> 768px)
            parallaxOffset = (scrollY - itemHeight * index) * 0.25;
            if (parallaxOffset >= 0) {
                item.style.backgroundPositionY = `-${parallaxOffset}px`;
            }
        }
    });
}

// Функция для вычисления максимального значения скролла
function calculateMaxScroll() {
    const projectItems = document.querySelectorAll(".project");
    let totalHeight = 0;

    projectItems.forEach((item) => {
        totalHeight += item.offsetHeight;
    });

    // Вычитаем высоту окна, чтобы последний элемент не уходил за пределы экрана
    return totalHeight - window.innerHeight;
}

// Добавляем обработчик изменения размера окна
window.addEventListener("resize", function() {
    initializeTransforms();
    setTransforms(currentScroll);
});