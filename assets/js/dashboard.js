// Animation d'apparition des cards
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
// Fonction de compte à rebours
document.addEventListener('DOMContentLoaded', () => {
    const countdownElements = document.querySelectorAll('.countdown');

    countdownElements.forEach(element => {
        const endDate = new Date(element.getAttribute('data-date')).getTime();

        if (isNaN(endDate)) {
            element.textContent = 'Date invalide';
            return;
        }

        const updateCountdown = () => {
            const now = new Date().getTime();
            const timeRemaining = endDate - now;

            if (timeRemaining <= 0) {
                element.textContent = 'Élection clôturée';
                element.classList.add('closed');
                return;
            }

            const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

            element.textContent = `Temps restant : ${days}j ${hours}h ${minutes}m ${seconds}s`;
        };

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
});
