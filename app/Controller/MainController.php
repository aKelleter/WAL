<?php
namespace App\Controller;

use Wal\Controller\AbstractController;

class MainController extends AbstractController {

    public function home() {
		return $this->renderView('main/home.php', ['title' => APP_NAME]);
	}

    public function contact() {		
		// Imaginons ici traiter la soumission d'un formulaire de contact et envoyer un mail...
		// On redirige ensuite vers la page d'accueil avec un message de succès
		return $this->redirectToRoute('home', ['state' => 'success']);
	}

}
