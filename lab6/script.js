const modal = document.getElementById('modal');
const closeBtn = document.getElementsByClassName('close')[0];

// Закрытие модального окна при клике на крестик
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function validateForm(event) {
    event.preventDefault();

    const form = event.target;
    const name1 = form.name1.value.trim();
    const birth1 = form.birth1.value;
    const passport1 = form.passport1.value.trim();
    const phone1 = form.phone1.value.trim();
    const name2 = form.name2.value.trim();
    const birth2 = form.birth2.value;
    const passport2 = form.passport2.value.trim();
    const phone2 = form.phone2.value.trim();
    const weddingDate = form['wedding-date'].value;
    const venue = form.venue.value;
    const agreement = form.agreement.checked;

    // Проверка на заполнение обязательных полей
    if (!name1 || !birth1 || !passport1 || !phone1 || !name2 || !birth2 || !passport2 || !phone2 || !weddingDate || !venue || !agreement) {
        alert("Пожалуйста, заполните все обязательные поля.");
        return;
    }

    // Проверка на корректность значений
    const passportPattern = /^\d{4}\s?\d{6}$/;
    const phonePattern = /^\+7\d{10}$/;

    if (!passportPattern.test(passport1) || !passportPattern.test(passport2)) {
        alert("Неверный формат паспорта. Используйте формат: 0000 000000.");
        return;
    }

    if (!phonePattern.test(phone1) || !phonePattern.test(phone2)) {
        alert("Неверный формат телефона. Используйте формат: +7 (000) 000-00-00.");
        return;
    }

    // Если все проверки пройдены, показываем модальное окно с данными
    const modalOutput = document.getElementById('modal-output');
    modalOutput.innerHTML = `
        <h2>Данные успешно отправлены:</h2>
        <div class="modal-data">
            <h3>Первый супруг:</h3>
            <p><strong>ФИО:</strong> ${name1}</p>
            <p><strong>Дата рождения:</strong> ${birth1}</p>
            <p><strong>Паспорт:</strong> ${passport1}</p>
            <p><strong>Телефон:</strong> ${phone1}</p>
            
            <h3>Второй супруг:</h3>
            <p><strong>ФИО:</strong> ${name2}</p>
            <p><strong>Дата рождения:</strong> ${birth2}</p>
            <p><strong>Паспорт:</strong> ${passport2}</p>
            <p><strong>Телефон:</strong> ${phone2}</p>
            
            <h3>Информация о регистрации:</h3>
            <p><strong>Дата регистрации:</strong> ${weddingDate}</p>
            <p><strong>Место регистрации:</strong> ${venue}</p>
        </div>
    `;

    // Показываем модальное окно
    modal.style.display = "block";
    
    // Очищаем форму
    form.reset();
}