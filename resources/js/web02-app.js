import * as bootstrap from 'bootstrap';
import $ from 'jquery';

const popoverTriggerList = document.querySelectorAll(
    '[data-bs-toggle="popover"]'
);

[...popoverTriggerList].forEach(
    element => new bootstrap.Popover(element)
);

const uploadToast = document.getElementById('uploadToast');

$('#uploadButton').on('click', () => {
    const toast = bootstrap.Toast.getOrCreateInstance(uploadToast);
    toast.show();
});



const modalElements = [...document.querySelectorAll('.modal')];

let activeModalIndex = -1;
let isSwitchingModal = false;

modalElements.forEach((modalElement, index) => {
    modalElement.addEventListener('shown.bs.modal', () => {
        activeModalIndex = index;
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        if (!isSwitchingModal) {
            activeModalIndex = -1;
        }
    });
});

function switchModal(direction) {
    if (activeModalIndex === -1 || isSwitchingModal) {
        return;
    }

    const currentIndex = activeModalIndex;

    const nextIndex =
        (currentIndex + direction + modalElements.length)
        % modalElements.length;

    const currentElement = modalElements[currentIndex];
    const nextElement = modalElements[nextIndex];

    const currentModal =
        bootstrap.Modal.getOrCreateInstance(currentElement);

    const nextModal =
        bootstrap.Modal.getOrCreateInstance(nextElement);

    isSwitchingModal = true;

    currentElement.addEventListener(
        'hidden.bs.modal',
        () => {
            activeModalIndex = nextIndex;
            nextModal.show();
            isSwitchingModal = false;
        },
        { once: true }
    );

    currentModal.hide();
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight') {
        switchModal(1);
    }

    if (event.key === 'ArrowLeft') {
        switchModal(-1);
    }
});