document.addEventListener("DOMContentLoaded", function() {
    const addPictureBtn = document.getElementById('add-picture');
    if (addPictureBtn) {
        addPictureBtn.addEventListener('click', function() {
            // Je récupère le numéro du futur champs que je vais créer
            const widgetsCounter = document.getElementById('widgets-counter');
            const index = parseInt(widgetsCounter.value, 10);

            // Je récupère le prototype des entrées
            const adPictures = document.getElementById('ad_pictures');
            const prototype = adPictures.getAttribute('data-prototype');
            const template = prototype.replace(/__name__/g, index);

            // J'injecte ce code au sein de la div
            adPictures.insertAdjacentHTML('beforeend', template);

            // J'incrémente le compteur de widgets
            widgetsCounter.value = index + 1;

            // Je gère le bouton supprimer
            handleDeleteButtons();
        });
    }
});

function handleDeleteButtons() {
    // Sélectionne tous les boutons ayant l'attribut data-action égal à 'delete'
    const deleteButtons = document.querySelectorAll('button[data-action="delete"]');
    
    // Ajoute un écouteur d'événement de clic à chaque bouton
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetSelector = this.dataset.target;
            const targetElement = document.querySelector(targetSelector);
            if (targetElement) {
                // Supprime l'élément cible du DOM
                targetElement.remove();
            }
        });
    });
}

function updateCounter() {
    const count = document.querySelectorAll('#ad_pictures div.form-group').length;
    document.getElementById('widgets-counter').value = count;
}

document.addEventListener('DOMContentLoaded', () => {
    updateCounter();
    handleDeleteButtons();
});