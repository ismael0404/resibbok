<?php
// app/controllers/ClientController.php

class ClientController extends Controller {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 3) {
            $_SESSION['flash_err'] = 'Veuillez vous connecter.';
            $this->redirect('auth/login');
        }
        $this->db = new Database;
    }

    public function profile() {
        $cid = $_SESSION['user_id'];
        $userModel = $this->model('User');
        $user = $userModel->getUserById($cid);

        $this->db->query('SELECT COUNT(*) as total FROM reservations WHERE client_id = :cid');
        $this->db->bind(':cid', $cid);
        $totalBookings = $this->db->single()->total;

        $this->db->query('SELECT COUNT(*) as total FROM favorites WHERE user_id = :cid');
        $this->db->bind(':cid', $cid);
        $totalFavorites = $this->db->single()->total;

        $resModel = $this->model('Reservation');
        $recentBookings = array_slice($resModel->getByClient($cid), 0, 5);

        $data = [
            'title' => 'Mon Profil',
            'user' => $user,
            'total_bookings' => $totalBookings,
            'total_favorites' => $totalFavorites,
            'recent_bookings' => $recentBookings
        ];
        $this->view('client/profile', $data);
    }

    public function reservations() {
        $resModel = $this->model('Reservation');
        $reservations = $resModel->getByClient($_SESSION['user_id']);
        $data = ['title' => 'Mes Réservations', 'reservations' => $reservations];
        $this->view('client/reservations', $data);
    }

    public function cancelReservation($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $resModel = $this->model('Reservation');
            $res = $resModel->getById($id);
            if ($res && $res->client_id == $_SESSION['user_id'] && in_array($res->status, ['pending'])) {
                $reason = trim($_POST['reason'] ?? 'Annulé par le client');
                $resModel->cancel($id, $reason);
                $_SESSION['flash_msg'] = 'Réservation annulée.';
            } else {
                $_SESSION['flash_err'] = 'Impossible d\'annuler cette réservation.';
            }
            $this->redirect('client/reservations');
        }
    }

    public function favorites() {
        $this->db->query('SELECT p.*, c.name as category_name,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM favorites f JOIN properties p ON f.property_id = p.id LEFT JOIN categories c ON p.category_id = c.id
            WHERE f.user_id = :uid ORDER BY f.created_at DESC');
        $this->db->bind(':uid', $_SESSION['user_id']);
        $favorites = $this->db->resultSet();
        $data = ['title' => 'Mes Favoris', 'favorites' => $favorites];
        $this->view('client/favorites', $data);
    }

    public function messages() {
        $msgModel = $this->model('Message');
        $conversations = $msgModel->getConversations($_SESSION['user_id']);
        $contacts = $msgModel->getContacts($_SESSION['user_id'], 3);
        $data = ['title' => 'Messages', 'conversations' => $conversations, 'contacts' => $contacts];
        $this->view('client/messages', $data);
    }

    public function settings() {
        $userModel = $this->model('User');
        $user = $userModel->getUserById($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $profileData = [
                'first_name' => trim($_POST['first_name'] ?? $user->first_name),
                'last_name' => trim($_POST['last_name'] ?? $user->last_name),
                'phone' => trim($_POST['phone'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'bio' => trim($_POST['bio'] ?? ''),
                'address' => trim($_POST['address'] ?? '')
            ];
            $userModel->updateProfile($_SESSION['user_id'], $profileData);

            // Avatar
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $avatarName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                    if (!file_exists(UPLOADS_PATH)) mkdir(UPLOADS_PATH, 0777, true);
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], UPLOADS_PATH . $avatarName)) {
                        $userModel->updateAvatar($_SESSION['user_id'], $avatarName);
                        $_SESSION['user_avatar'] = $avatarName;
                    }
                }
            }

            $_SESSION['user_name'] = $profileData['first_name'] . ' ' . $profileData['last_name'];
            $_SESSION['flash_msg'] = 'Profil mis à jour !';
            $this->redirect('client/settings');
        }

        $data = ['title' => 'Paramètres', 'user' => $user];
        $this->view('client/settings', $data);
    }

    // Page de paiement pour une réservation
    public function payment($reservation_id) {
        $resModel = $this->model('Reservation');
        $res = $resModel->getById($reservation_id);

        if (!$res || $res->client_id != $_SESSION['user_id']) {
            $_SESSION['flash_err'] = 'Réservation introuvable.';
            $this->redirect('client/reservations');
            return;
        }

        // Vérifier si déjà payé
        $payModel = $this->model('Payment');
        $existingPayment = $payModel->getByReservation($reservation_id);

        if ($existingPayment && $existingPayment->status == 'completed') {
            $_SESSION['flash_err'] = 'Cette réservation est déjà payée.';
            $this->redirect('client/reservations');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $amount = $res->total_price;
            $commission = $amount * (COMMISSION_RATE / 100);
            $ownerAmount = $amount - $commission;
            $txnId = 'TXN-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $payData = [
                'reservation_id' => $reservation_id,
                'payer_id' => $_SESSION['user_id'],
                'amount' => $amount,
                'commission' => $commission,
                'owner_amount' => $ownerAmount,
                'payment_method' => trim($_POST['payment_method'] ?? 'mobile_money'),
                'payment_phone' => trim($_POST['payment_phone'] ?? ''),
                'payment_name' => trim($_POST['payment_name'] ?? $_SESSION['user_name']),
                'transaction_id' => $txnId
            ];

            $paymentId = $payModel->create($payData);
            if ($paymentId) {
                // Confirmer la réservation (statut paid)
                $resModel->updateStatus($reservation_id, 'paid');

                // Notification propriétaire
                require_once APPROOT . '/models/Notification.php';
                $notif = new Notification();
                $notif->create($res->owner_id, 'payment_received', 'Paiement reçu',
                    'Paiement de ' . number_format($ownerAmount, 0, ',', ' ') . ' FCFA reçu pour "' . $res->title . '"',
                    'owner/earnings');

                $_SESSION['flash_msg'] = 'Paiement effectué avec succès ! Référence : ' . $txnId;
                $this->redirect('client/reservations');
            } else {
                $_SESSION['flash_err'] = 'Erreur lors du paiement.';
                $this->redirect('client/payment/' . $reservation_id);
            }
        }

        $data = ['title' => 'Paiement', 'reservation' => $res];
        $this->view('client/payment', $data);
    }
}
