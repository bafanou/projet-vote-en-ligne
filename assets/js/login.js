// Validation du formulaire de connexion
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const errorMessage = document.querySelector('.error');

    form.addEventListener('submit', function(e) {
        let valid = true;
        errorMessage.style.display = 'none';

        if (!emailInput.value.includes('@')) {
            valid = false;
            errorMessage.textContent = 'Veuillez entrer une adresse e-mail valide.';
        } else if (passwordInput.value.length < 6) {
            valid = false;
            errorMessage.textContent = 'Le mot de passe doit contenir au moins 6 caractères.';
        }

        if (!valid) {
            e.preventDefault();
            errorMessage.style.display = 'block';
            errorMessage.classList.add('fade-in');
        }
    });
});
