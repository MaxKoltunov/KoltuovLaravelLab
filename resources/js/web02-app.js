import * as bootstrap from 'bootstrap';
import $ from 'jquery';

// Подсказки с определениями терминов на детальных страницах.
document.querySelectorAll('[data-bs-toggle="popover"]').forEach(
    element => new bootstrap.Popover(element)
);

// Сообщение о пока недоступном старом действии «Загрузить».
const uploadToast = document.getElementById('uploadToast');

if (uploadToast) {
    $('#uploadButton').on('click', () => {
        bootstrap.Toast.getOrCreateInstance(uploadToast).show();
    });
}
