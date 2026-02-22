document.addEventListener('DOMContentLoaded', () => {
    const flipCardButton = document.getElementById('flip-card-button');
    if (flipCardButton) {
        flipCardButton.addEventListener('click', () => {
            const card = document.querySelector('.card');
            card.style.transform = card.style.transform !== 'rotateY(180deg)' ? 'rotateY(180deg)' : 'rotateY(0deg)';
        });
    }
});
