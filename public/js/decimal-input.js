/**
 * Универсален компонент за обработка на десетични полета
 * Поддържа както запетая, така и точка като десетичен разделител
 * Автоматично нормализира въведените стойности
 */

class DecimalInput {
    constructor(input, options = {}) {
        this.input = input;
        this.options = {
            maxDecimals: 2,
            minValue: 0.01,
            allowNegative: false,
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('blur', (e) => this.handleBlur(e));
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
    }
    
    handleInput(e) {
        let value = e.target.value;
        
        // Премахваме всички символи освен цифри, точка, запетая и минус (ако е позволен)
        const allowedChars = this.options.allowNegative ? '[^0-9.,-]' : '[^0-9.,]';
        value = value.replace(new RegExp(allowedChars, 'g'), '');
        
        // Обработваме минус знака (само в началото)
        if (this.options.allowNegative && value.includes('-')) {
            const minusCount = (value.match(/-/g) || []).length;
            if (minusCount > 1 || (value.indexOf('-') !== 0 && value.includes('-'))) {
                value = value.replace(/-/g, '');
                if (e.target.value.startsWith('-')) {
                    value = '-' + value;
                }
            }
        }
        
        // Заменяме запетая с точка
        value = value.replace(',', '.');
        
        // Ограничаваме до една точка
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Ограничаваме десетичните знаци
        if (parts.length === 2 && parts[1].length > this.options.maxDecimals) {
            value = parts[0] + '.' + parts[1].substring(0, this.options.maxDecimals);
        }
        
        e.target.value = value;
    }
    
    handleBlur(e) {
        let value = parseFloat(e.target.value);
        
        if (isNaN(value)) {
            e.target.value = this.options.minValue.toFixed(this.options.maxDecimals);
            return;
        }
        
        if (!this.options.allowNegative && value < 0) {
            value = Math.abs(value);
        }
        
        if (value < this.options.minValue) {
            value = this.options.minValue;
        }
        
        e.target.value = value.toFixed(this.options.maxDecimals);
    }
    
    handleKeydown(e) {
        // Позволяваме: backspace, delete, tab, escape, enter
        if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
            // Позволяваме: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Позволяваме: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        
        // Позволяваме: цифри от главната клавиатура и numpad
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && 
            (e.keyCode < 96 || e.keyCode > 105) &&
            // Позволяваме: точка и запетая
            e.keyCode !== 190 && e.keyCode !== 188 && e.keyCode !== 110 &&
            // Позволяваме: минус (ако е позволен)
            !(this.options.allowNegative && (e.keyCode === 189 || e.keyCode === 109))) {
            e.preventDefault();
        }
    }
    
    // Статичен метод за лесна инициализация
    static init(selector, options = {}) {
        const inputs = document.querySelectorAll(selector);
        const instances = [];
        
        inputs.forEach(input => {
            instances.push(new DecimalInput(input, options));
        });
        
        return instances;
    }
}

// Автоматична инициализация при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    // Инициализираме всички полета с клас 'decimal-input'
    DecimalInput.init('.decimal-input');
    
    // Инициализираме специфични полета
    DecimalInput.init('input[name="amount"]', { maxDecimals: 2, minValue: 0.01 });
    DecimalInput.init('input[name="amount_from"]', { maxDecimals: 2, minValue: 0.01 });
    DecimalInput.init('input[name="initial_balance"]', { maxDecimals: 2, minValue: 0 });
    DecimalInput.init('input[name="exchange_rate"]', { maxDecimals: 6, minValue: 0.000001 });
});

// Експортираме класа за използване в други скриптове
window.DecimalInput = DecimalInput;
