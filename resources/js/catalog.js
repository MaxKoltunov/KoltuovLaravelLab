import { Popover, Toast } from 'bootstrap';

// Подсказки с определениями терминов на детальных страницах.
document.querySelectorAll('[data-bs-toggle="popover"]').forEach(
    element => new Popover(element)
);

// Сообщение о пока недоступном действии «Загрузить».
const uploadToast = document.getElementById('uploadToast');
const uploadButton = document.getElementById('uploadButton');

if (uploadToast && uploadButton) {
    uploadButton.addEventListener('click', () => {
        Toast.getOrCreateInstance(uploadToast).show();
    });
}
