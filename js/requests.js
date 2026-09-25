// Данные заявок
const requestsData = [
    {
        number: '№1',
        name: 'ВП (запуск УЭЦН после КРС)',
        color: 'red',
        date: '08.09.2026',
        kust: '23',
        dispatcher: 'Белослудцева Я.С.',
        well: 'СКВ-4021',
        status: 'В работе'
    },
    {
        number: '№2',
        name: 'НАСТР (телеметрия)',
        color: 'blue',
        date: '02.09.2026',
        kust: '18',
        dispatcher: 'Закурин Д.В.',
        well: 'СКВ-101',
        status: 'В работе'
    },
    {
        number: '№3',
        name: 'Освоение скважины после бурения',
        color: 'green',
        date: '05.09.2026',
        kust: '23',
        dispatcher: 'Климов С.А.',
        well: 'СКВ-50',
        status: 'Выполнено'
    },
    {
        number: '№4',
        name: 'ГРП (гидроразрыв)',
        color: 'red',
        date: '31.08.2026',
        kust: '23',
        dispatcher: 'Белослудцева Я.С.',
        well: 'СКВ-27',
        status: 'В работе'
    },
    {
        number: '№3',
        name: 'Освоение скважины после бурения',
        color: 'green',
        date: '05.09.2026',
        kust: '23',
        dispatcher: 'Климов С.А.',
        well: 'СКВ-50',
        status: 'Выполнено'
    },
    {
        number: '№3',
        name: 'Освоение скважины после бурения',
        color: 'green',
        date: '05.09.2026',
        kust: '23',
        dispatcher: 'Климов С.А.',
        well: 'СКВ-50',
        status: 'Выполнено'
    }
];

// Отрисовка карточек
const container = document.getElementById('requests-container');
const template = document.getElementById('request-card-template');

requestsData.forEach(req => {
    const clone = template.content.cloneNode(true);

    // Цвет рамки
    const borderDiv = clone.querySelector('.div_for_color_border_red');
    borderDiv.classList.remove('div_for_color_border_red');
    borderDiv.classList.add('div_for_color_border_' + req.color);

    // Основные данные
    clone.querySelector('.number_of_request').textContent = req.number;
    clone.querySelector('.name_of_request').textContent = req.name;
    clone.querySelector('.date_value').textContent = req.date;
    clone.querySelector('.kust_value').textContent = req.kust;
    clone.querySelector('.dispatcher_value').textContent = req.dispatcher;
    clone.querySelector('.well_value').textContent = req.well;
    clone.querySelector('.status_value').textContent = req.status;

    container.appendChild(clone);
});

// Обновляем счётчик "Всего заявок"
document.querySelector('.total_requests').textContent =
    'Всего заявок: ' + requestsData.length;