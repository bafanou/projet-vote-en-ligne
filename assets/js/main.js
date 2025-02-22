// Script général pour afficher les alertes
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.success, .error');
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.display = 'none';
        }, 5000); // Masquer les messages après 5 secondes
    });
});

// Confirmation de suppression (utilisé pour l'administration)
function confirmDelete(message) {
    return confirm(message || "Voulez-vous vraiment supprimer cet élément ?");
}


// main.js

document.addEventListener('DOMContentLoaded', () => {
    // Animation d'apparition en fondu pour les éléments de la page
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 200);
    });

    // Défilement fluide pour les liens d'ancrage
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute('href'));
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Effet de survol pour les boutons
    const buttons = document.querySelectorAll('.cta-button');
    buttons.forEach(button => {
        button.addEventListener('mouseover', () => {
            button.style.transform = 'scale(1.05)';
        });
        button.addEventListener('mouseout', () => {
            button.style.transform = 'scale(1)';
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 200);
    });
});
