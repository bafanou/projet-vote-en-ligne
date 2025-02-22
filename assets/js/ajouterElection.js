document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('electionForm');
    const successMessage = document.querySelector('.success');
    const errorMessage = document.querySelector('.error');

    form.addEventListener('submit', (e) => {
        let valid = true;
        errorMessage.style.display = 'none';

        const nom = document.getElementById('nom').value.trim();
        const dateDebut = document.getElementById('date_debut').value;
        const heureDebut = document.getElementById('heure_debut').value;
        const dateFin = document.getElementById('date_fin').value;
        const heureFin = document.getElementById('heure_fin').value;

        if (nom === '' || dateDebut === '' || heureDebut === '' || dateFin === '' || heureFin === '') {
            valid = false;
            errorMessage.textContent = 'Veuillez remplir tous les champs.';
        } else if (new Date(dateDebut + ' ' + heureDebut) >= new Date(dateFin + ' ' + heureFin)) {
            valid = false;
            errorMessage.textContent = 'La date de fin doit être postérieure à la date de début.';
        }

        if (!valid) {
            e.preventDefault();
            errorMessage.style.display = 'block';
            errorMessage.classList.add('fade-in');
        } else {
            successMessage.style.display = 'block';
        }
    });
});
