<h2 class="text-center">... un micro framework PHP simple, performant et sécurisé. </h2>

<?php
if (isset($_GET['state'])) {
    echo '<div class="alert alert-success text-center" role="alert">Votre message a bien été envoyé. Merci !</div>';
}