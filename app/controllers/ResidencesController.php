<?php
// app/controllers/ResidencesController.php — Listing public des biens

class ResidencesController extends Controller {
    public function __construct() {}

    public function index() {
        $propModel = $this->model('Property');
        $filters = [
            'city' => $_GET['city'] ?? '',
            'category' => $_GET['category'] ?? '',
            'type' => $_GET['type'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'bedrooms' => $_GET['bedrooms'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        $properties = $propModel->getAll($filters);
        $categories = $propModel->getCategories();

        $data = [
            'title' => 'Explorer les biens',
            'properties' => $properties,
            'categories' => $categories,
            'filters' => $filters
        ];
        $this->view('residences/index', $data);
    }

    public function show($id) {
        $propModel = $this->model('Property');
        $property = $propModel->getById($id);

        if (!$property) {
            $this->redirect('pages/index');
            return;
        }

        $images = $propModel->getImages($id);
        $amenities = $propModel->getAmenities($id);
        $reviews = $propModel->getReviews($id);

        // Vérifier favoris
        $isFavorite = false;
        if (isset($_SESSION['user_id'])) {
            $db = new Database();
            $db->query('SELECT id FROM favorites WHERE user_id = :uid AND property_id = :pid');
            $db->bind(':uid', $_SESSION['user_id']);
            $db->bind(':pid', $id);
            $isFavorite = $db->single() ? true : false;
        }

        $similar = $propModel->getSimilarProperties($id, $property->category_id, $property->city, 4);

        // Nombre d'annonces actives du propriétaire
        $ownerPropertyCount = $propModel->getOwnerPropertyCount($property->owner_id);

        // Assigner les images de la galerie pour le helper d'images
        $property->gallery_images = $images;

        // Vérifier si l'utilisateur a déjà commenté
        $hasReviewed = false;
        if (isset($_SESSION['user_id'])) {
            require_once APPROOT . '/models/Review.php';
            $reviewModel = new Review();
            $hasReviewed = $reviewModel->hasUserReviewed($id, $_SESSION['user_id']);
        }

        $data = [
            'title' => $property->title,
            'property' => $property,
            'hasReviewed' => $hasReviewed,
            'images' => $images,
            'amenities' => $amenities,
            'reviews' => $reviews,
            'similar' => $similar,
            'is_favorite' => $isFavorite,
            'owner_property_count' => $ownerPropertyCount
        ];
        $this->view('residences/show', $data);
    }
}
