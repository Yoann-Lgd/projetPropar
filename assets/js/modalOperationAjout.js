const DIV_STEP = document.getElementById('divStep');
const DIV_BODY = document.getElementById('divBody');
const DIV_BTN  = document.getElementById('divBtn');

const STEP = [
            {'libelle': "Initialisation", 'description': "Nom et déscription de l’opération"}, // Id => 0
            {'libelle': "Budget", 'description': "Type de budget de l’opération"}, // Id => 1
            {'libelle': "Client", 'description': "Client pour l’opération"}, // Id => 2
            {'libelle': "Prestataire", 'description': "Utilisateur affecter à l'opération"}, // Id => 3
            {'libelle': "Récapitulatif", 'description': "Examiner et soumettre "} // Id => 4
        ];

function modalAjout() {
    DIV_STEP.innerHTML = "Hello World";
}
