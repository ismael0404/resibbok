<?php
// app/controllers/ReviewsController.php

class ReviewsController extends Controller {
    public function __construct() {
        // Optionnel : on peut limiter l'accès à certaines méthodes, mais on le gère dans add()
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Vérifier que l'utilisateur est connecté et est un client (rôle 3) ou tout utilisateur qui peut réserver
            if (!isset($_SESSION['user_id'])) {
                $this->redirect('auth/login');
                return;
            }

            // Assainir POST
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'property_id' => trim($_POST['property_id']),
                'client_id' => $_SESSION['user_id'],
                'rating' => trim($_POST['rating']),
                'comment' => trim($_POST['comment'])
            ];

            // Valider rating
            if ($data['rating'] < 1 || $data['rating'] > 5) {
                // Rediriger avec erreur
                $this->redirect('residences/show/' . $data['property_id'] . '?error=rating');
                return;
            }

            $reviewModel = $this->model('Review');

            // Vérifier s'il n'a pas déjà commenté
            if ($reviewModel->hasUserReviewed($data['property_id'], $data['client_id'])) {
                $this->redirect('residences/show/' . $data['property_id'] . '?error=already_reviewed');
                return;
            }

            // Ajouter
            if ($reviewModel->addReview($data)) {
                // Mettre à jour le score du bien
                $reviewModel->updatePropertyScore($data['property_id']);
                
                $this->redirect('residences/show/' . $data['property_id'] . '?success=review_added');
            } else {
                $this->redirect('residences/show/' . $data['property_id'] . '?error=server');
            }
        } else {
            $this->redirect('pages/index');
        }
    }
}
