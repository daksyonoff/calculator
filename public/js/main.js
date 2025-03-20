document.addEventListener('DOMContentLoaded', function() {
    // Валидация форм
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Калькулятор параметров
    const calculator = document.getElementById('calculator-form');
    if (calculator) {
        // Автоматический расчет объема и веса
        ['length', 'width', 'height', 'material_thickness'].forEach(field => {
            document.getElementById(field)?.addEventListener('input', calculateVolume);
        });

        calculator.addEventListener('submit', function(e) {
            if (!calculator.checkValidity()) {
                e.preventDefault();
                return;
            }
            
            // Предварительный расчет перед отправкой
            calculateVolume();
        });
    }
});

function calculateVolume() {
    const length = parseFloat(document.getElementById('length')?.value || 0);
    const width = parseFloat(document.getElementById('width')?.value || 0);
    const height = parseFloat(document.getElementById('height')?.value || 0);
    const thickness = parseFloat(document.getElementById('material_thickness')?.value || 0);
    
    if (length && width && height && thickness) {
        // Расчет объема с учетом толщины материала
        const volume = (length + 2 * thickness) * (width + 2 * thickness) * (height + 2 * thickness);
        
        // Приблизительный расчет веса (для стали)
        const density = 7.85; // г/см³ (для стали)
        const weight = (volume * density) / 1000000; // Перевод в кг
        
        // Автоматическое заполнение поля веса
        const weightInput = document.getElementById('weight');
        if (weightInput) {
            weightInput.value = weight.toFixed(2);
        }
    }
}

function getMaterialFactor(material) {
    const factors = {
        'steel': 1.5,
        'aluminum': 1.0,
        'plastic': 0.8
    };
    return factors[material] || 1.0;
}

// Функция для форматирования чисел
function formatNumber(number) {
    return new Intl.NumberFormat('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}
