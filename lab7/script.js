class Calculator {
    constructor() {
        this.currentNumber = '0';
        this.firstNumber = null;
        this.operation = null;
        this.shouldResetScreen = false;
        
        this.display = document.getElementById('result');
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.querySelectorAll('.number, .decimal').forEach(button => {
            button.addEventListener('click', () => this.appendNumber(button.textContent));
        });

        document.querySelectorAll('.operator').forEach(button => {
            button.addEventListener('click', () => this.handleOperator(button.dataset.action));
        });

        document.querySelector('[data-action="calculate"]').addEventListener('click', () => this.calculate());
        document.querySelector('[data-action="c"]').addEventListener('click', () => this.clear());
        document.querySelector('[data-action="ce"]').addEventListener('click', () => this.clearEntry());
    }

    appendNumber(number) {
        if (this.shouldResetScreen) {
            this.currentNumber = '';
            this.shouldResetScreen = false;
        }
        
        if (number === '.' && this.currentNumber.includes('.')) return;
        if (this.currentNumber === '0' && number !== '.') {
            this.currentNumber = number;
        } else {
            this.currentNumber += number;
        }
        this.updateDisplay();
    }

    handleOperator(operator) {
        switch(operator) {
            case 'percent':
                this.percent();
                break;
            case 'sqrt':
                this.sqrt();
                break;
            case 'inverse':
                this.inverse();
                break;
            case 'negate':
                this.negate();
                break;
            case 'backspace':
                this.backspace();
                break;
            default:
                this.setOperation(operator);
        }
    }

    setOperation(operator) {
        if (this.operation !== null) this.calculate();
        this.firstNumber = parseFloat(this.currentNumber);
        this.operation = operator;
        this.shouldResetScreen = true;
    }

    calculate() {
        if (this.operation === null || this.shouldResetScreen) return;
        
        const secondNumber = parseFloat(this.currentNumber);
        let result;

        switch(this.operation) {
            case 'add':
                result = this.firstNumber + secondNumber;
                break;
            case 'subtract':
                result = this.firstNumber - secondNumber;
                break;
            case 'multiply':
                result = this.firstNumber * secondNumber;
                break;
            case 'divide':
                if (secondNumber === 0) {
                    this.currentNumber = 'Ошибка';
                    this.updateDisplay();
                    return;
                }
                result = this.firstNumber / secondNumber;
                break;
        }

        this.currentNumber = result.toString();
        this.operation = null;
        this.firstNumber = null;
        this.shouldResetScreen = true;
        this.updateDisplay();
    }

    percent() {
        const current = parseFloat(this.currentNumber);
        this.currentNumber = (current / 100).toString();
        this.updateDisplay();
    }

    sqrt() {
        const current = parseFloat(this.currentNumber);
        if (current < 0) {
            this.currentNumber = 'Ошибка';
        } else {
            this.currentNumber = Math.sqrt(current).toString();
        }
        this.updateDisplay();
    }

    inverse() {
        const current = parseFloat(this.currentNumber);
        if (current === 0) {
            this.currentNumber = 'Ошибка';
        } else {
            this.currentNumber = (1 / current).toString();
        }
        this.updateDisplay();
    }

    negate() {
        this.currentNumber = (-parseFloat(this.currentNumber)).toString();
        this.updateDisplay();
    }

    backspace() {
        if (this.currentNumber.length === 1 || 
            (this.currentNumber.length === 2 && this.currentNumber.startsWith('-'))) {
            this.currentNumber = '0';
        } else {
            this.currentNumber = this.currentNumber.slice(0, -1);
        }
        this.updateDisplay();
    }

    clear() {
        this.currentNumber = '0';
        this.firstNumber = null;
        this.operation = null;
        this.shouldResetScreen = false;
        this.updateDisplay();
    }

    clearEntry() {
        this.currentNumber = '0';
        this.updateDisplay();
    }

    updateDisplay() {
        this.display.value = this.currentNumber;
    }
}

// Инициализация калькулятора
document.addEventListener('DOMContentLoaded', () => {
    new Calculator();
}); 