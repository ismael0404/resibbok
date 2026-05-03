<?php
// app/controllers/ReservationsController.php

class ReservationsController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_err'] = 'Veuillez vous connecter.';
            $this->redirect('auth/login');
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { $this->redirect('pages/index'); return; }
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        $property_id = intval($_POST['property_id'] ?? 0);
        $check_in = trim($_POST['check_in'] ?? '');
        $check_out = trim($_POST['check_out'] ?? '');
        $guests = intval($_POST['guests'] ?? 1);
        $special_requests = trim($_POST['special_requests'] ?? '');

        if (empty($check_in) || empty($check_out) || $property_id <= 0) {
            $_SESSION['flash_err'] = 'Veuillez remplir tous les champs.';
            $this->redirect('residences/show/' . $property_id);
            return;
        }

        $resModel = $this->model('Reservation');
        $propModel = $this->model('Property');

        $date_in = new DateTime($check_in);
        $date_out = new DateTime($check_out);
        if ($date_out <= $date_in) {
            $_SESSION['flash_err'] = 'La date de départ doit être après la date d\'arrivée.';
            $this->redirect('residences/show/' . $property_id);
            return;
        }

        if (!$resModel->checkAvailability($property_id, $check_in, $check_out)) {
            $_SESSION['flash_err'] = 'Ces dates ne sont plus disponibles.';
            $this->redirect('residences/show/' . $property_id);
            return;
        }

        $property = $propModel->getById($property_id);
        if (!$property || $property->status != 'active') {
            $_SESSION['flash_err'] = 'Bien non disponible.';
            $this->redirect('pages/index');
            return;
        }

        $nights = $date_in->diff($date_out)->days;
        $ppn = $property->price_per_night;
        $subtotal = $ppn * $nights;
        $fee = $subtotal * (COMMISSION_RATE / 100);
        $total = $subtotal + $fee;

        $resData = [
            'property_id' => $property_id,
            'client_id' => $_SESSION['user_id'],
            'check_in' => $check_in,
            'check_out' => $check_out,
            'guests' => $guests,
            'nights' => $nights,
            'price_per_night' => $ppn,
            'subtotal' => $subtotal,
            'service_fee' => $fee,
            'total_price' => $total,
            'special_requests' => $special_requests
        ];

        $reservation_id = $resModel->create($resData);
        if ($reservation_id) {
            require_once APPROOT . '/models/Notification.php';
            $notif = new Notification();
            $notif->create($property->owner_id, 'booking_new', 'Nouvelle réservation',
                $_SESSION['user_name'] . ' a réservé "' . $property->title . '". Veuillez valider la demande.', 'owner/reservations');
            
            $_SESSION['flash_msg'] = 'Réservation demandée avec succès ! En attente de validation par l\'hôte.';
            $this->redirect('client/reservations');
        } else {
            $_SESSION['flash_err'] = 'Erreur. Réessayez.';
            $this->redirect('residences/show/' . $property_id);
        }
    }

    public function checkAvailability() {
        header('Content-Type: application/json');
        $property_id = intval($_GET['property_id'] ?? 0);
        $check_in = $_GET['check_in'] ?? '';
        $check_out = $_GET['check_out'] ?? '';

        if ($property_id <= 0 || empty($check_in) || empty($check_out)) {
            echo json_encode(['available' => false, 'error' => 'Paramètres manquants']);
            exit;
        }

        $resModel = $this->model('Reservation');
        $propModel = $this->model('Property');
        $available = $resModel->checkAvailability($property_id, $check_in, $check_out);
        $property = $propModel->getById($property_id);

        if ($available && $property && $property->status == 'active') {
            $di = new DateTime($check_in);
            $do = new DateTime($check_out);
            $nights = $di->diff($do)->days;
            $sub = $property->price_per_night * $nights;
            $fee = $sub * (COMMISSION_RATE / 100);
            echo json_encode(['available' => true, 'nights' => $nights, 'price_per_night' => $property->price_per_night, 'subtotal' => $sub, 'service_fee' => $fee, 'total' => $sub + $fee]);
        } else {
            echo json_encode(['available' => false, 'error' => 'Dates non disponibles']);
        }
        exit;
    }
}
