<?php
// app/controllers/PagesController.php

class PagesController extends Controller {
    public function __construct() {}

    public function index() {
        $propModel = $this->model('Property');
        $featured = $propModel->getFeatured(6);
        $categories = $propModel->getCategories();

        $data = [
            'title' => 'Accueil',
            'description' => 'La plateforme immobilière de référence en Côte d\'Ivoire. Réservation, location et vente de biens.',
            'featured' => $featured,
            'categories' => $categories
        ];
        $this->view('pages/index', $data);
    }
}
