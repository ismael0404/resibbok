<?php
// app/controllers/OwnerController.php — Dashboard Propriétaire complet

class OwnerController extends Controller {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
            $_SESSION['flash_err'] = 'Accès non autorisé';
            $this->redirect('auth/login');
        }
        $this->db = new Database;
    }

    // Vérifier si le propriétaire est approuvé
    private function isApproved() {
        $this->db->query('SELECT status FROM users WHERE id = :id');
        $this->db->bind(':id', $_SESSION['user_id']);
        $user = $this->db->single();
        return $user && $user->status === 'active';
    }

    public function dashboard() {
        if (!$this->isApproved()) {
            $this->view('owner/pending', ['title' => 'Compte en attente']);
            return;
        }

        $propModel = $this->model('Property');
        $resModel = $this->model('Reservation');
        $payModel = $this->model('Payment');
        $oid = $_SESSION['user_id'];

        $propStats = $propModel->getOwnerStats($oid);
        $activeBookings = $resModel->getOwnerActiveCount($oid);
        $earnings = $payModel->getOwnerEarnings($oid);
        $monthlyEarnings = $payModel->getOwnerMonthlyEarnings($oid);
        $recentBookings = $resModel->getByOwner($oid);

        $data = [
            'title' => 'Tableau de bord',
            'total_properties' => $propStats['total_properties'],
            'active_properties' => $propStats['active_properties'],
            'active_bookings' => $activeBookings,
            'total_earnings' => $earnings,
            'monthly_earnings' => $monthlyEarnings,
            'recent_bookings' => array_slice($recentBookings, 0, 5)
        ];
        $this->view('owner/dashboard', $data);
    }

    public function residences() {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }
        $propModel = $this->model('Property');
        $properties = $propModel->getByOwner($_SESSION['user_id']);
        $data = ['title' => 'Mes Biens', 'properties' => $properties];
        $this->view('owner/residences', $data);
    }

    public function addResidence() {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $propModel = $this->model('Property');

            $title = trim($_POST['title']);
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $title)) . '-' . time();

            $propData = [
                'owner_id' => $_SESSION['user_id'],
                'category_id' => intval($_POST['category_id']),
                'title' => $title,
                'slug' => $slug,
                'description' => trim($_POST['description']),
                'listing_type' => trim($_POST['listing_type'] ?? 'reservation'),
                'price_per_night' => floatval($_POST['price_per_night'] ?? 0),
                'price_monthly' => floatval($_POST['price_monthly'] ?? 0),
                'price_sale' => floatval($_POST['price_sale'] ?? 0),
                'address' => trim($_POST['address']),
                'city' => trim($_POST['city']),
                'max_guests' => intval($_POST['max_guests'] ?? 2),
                'bedrooms' => intval($_POST['bedrooms'] ?? 1),
                'bathrooms' => intval($_POST['bathrooms'] ?? 1),
                'area_sqm' => intval($_POST['area_sqm'] ?? 0),
                'rules' => trim($_POST['rules'] ?? ''),
                'cancellation_policy' => trim($_POST['cancellation_policy'] ?? 'moderate'),
            ];

            $property_id = $propModel->create($propData);

            if ($property_id) {
                // Upload images
                if (isset($_FILES['images']) && $_FILES['images']['error'][0] != 4) {
                    if (!file_exists(UPLOADS_PATH)) mkdir(UPLOADS_PATH, 0777, true);
                    $files = $_FILES['images'];
                    for ($i = 0; $i < count($files['name']); $i++) {
                        if ($files['error'][$i] == 0 && $files['size'][$i] <= 5 * 1024 * 1024) {
                            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                                $newName = 'prop_' . $property_id . '_' . $i . '_' . time() . '.' . $ext;
                                if (move_uploaded_file($files['tmp_name'][$i], UPLOADS_PATH . $newName)) {
                                    $propModel->addImage($property_id, $newName, $i == 0 ? 1 : 0, $i);
                                }
                            }
                        }
                    }
                }

                // Équipements
                if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
                    $propModel->setAmenities($property_id, $_POST['amenities']);
                }

                $_SESSION['flash_msg'] = 'Bien ajouté avec succès !';
                $this->redirect('owner/residences');
            } else {
                $_SESSION['flash_err'] = 'Erreur lors de l\'ajout.';
                $this->redirect('owner/addResidence');
            }
        } else {
            $propModel = $this->model('Property');
            $data = [
                'title' => 'Ajouter un bien',
                'categories' => $propModel->getCategories(),
                'amenities' => $propModel->getAllAmenities()
            ];
            $this->view('owner/add_residence', $data);
        }
    }

    public function editResidence($id) {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }
        $propModel = $this->model('Property');
        $property = $propModel->getById($id);

        if (!$property || $property->owner_id != $_SESSION['user_id']) {
            $_SESSION['flash_err'] = 'Bien non trouvé.';
            $this->redirect('owner/residences');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $propData = [
                'owner_id' => $_SESSION['user_id'],
                'category_id' => intval($_POST['category_id']),
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'listing_type' => trim($_POST['listing_type'] ?? 'reservation'),
                'price_per_night' => floatval($_POST['price_per_night'] ?? 0),
                'price_monthly' => floatval($_POST['price_monthly'] ?? 0),
                'price_sale' => floatval($_POST['price_sale'] ?? 0),
                'address' => trim($_POST['address']),
                'city' => trim($_POST['city']),
                'max_guests' => intval($_POST['max_guests'] ?? 2),
                'bedrooms' => intval($_POST['bedrooms'] ?? 1),
                'bathrooms' => intval($_POST['bathrooms'] ?? 1),
                'area_sqm' => intval($_POST['area_sqm'] ?? 0),
                'rules' => trim($_POST['rules'] ?? ''),
                'cancellation_policy' => trim($_POST['cancellation_policy'] ?? 'moderate'),
            ];

            $propModel->update($id, $propData);

            // Nouvelles images
            if (isset($_FILES['images']) && $_FILES['images']['error'][0] != 4) {
                if (!file_exists(UPLOADS_PATH)) mkdir(UPLOADS_PATH, 0777, true);
                $files = $_FILES['images'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] == 0 && $files['size'][$i] <= 5 * 1024 * 1024) {
                        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                            $newName = 'prop_' . $id . '_' . $i . '_' . time() . '.' . $ext;
                            if (move_uploaded_file($files['tmp_name'][$i], UPLOADS_PATH . $newName)) {
                                $propModel->addImage($id, $newName, 0, 10 + $i);
                            }
                        }
                    }
                }
            }

            // Équipements
            if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
                $propModel->setAmenities($id, $_POST['amenities']);
            }

            $_SESSION['flash_msg'] = 'Bien modifié avec succès !';
            $this->redirect('owner/residences');
        } else {
            $images = $propModel->getImages($id);
            $amenities = $propModel->getAmenities($id);
            $amenityIds = array_map(function($a) { return $a->id; }, $amenities);

            $data = [
                'title' => 'Modifier le bien',
                'property' => $property,
                'images' => $images,
                'property_amenities' => $amenityIds,
                'categories' => $propModel->getCategories(),
                'amenities' => $propModel->getAllAmenities()
            ];
            $this->view('owner/edit_residence', $data);
        }
    }

    public function deleteResidence($id) {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }
        $propModel = $this->model('Property');
        if ($propModel->delete($id, $_SESSION['user_id'])) {
            $_SESSION['flash_msg'] = 'Bien supprimé.';
        } else {
            $_SESSION['flash_err'] = 'Erreur lors de la suppression.';
        }
        $this->redirect('owner/residences');
    }

    public function reservations() {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }
        $resModel = $this->model('Reservation');
        $reservations = $resModel->getByOwner($_SESSION['user_id']);
        $data = ['title' => 'Réservations', 'reservations' => $reservations];
        $this->view('owner/reservations', $data);
    }

    public function acceptReservation($id) {
        $resModel = $this->model('Reservation');
        $propModel = $this->model('Property');
        
        $res = $resModel->getById($id);
        if ($res && $res->owner_id == $_SESSION['user_id']) {
            $resModel->updateStatus($id, 'approved'); // Changed to approved for workflow
            
            // Changer le statut de la résidence
            $property = $propModel->getById($res->property_id);
            if ($property) {
                if ($property->listing_type == 'reservation') $newStatus = 'reserved';
                elseif ($property->listing_type == 'rental') $newStatus = 'rented';
                elseif ($property->listing_type == 'sale') $newStatus = 'sold';
                else $newStatus = 'reserved';
                
                $propModel->updateStatus($property->id, $newStatus);
            }

            // Notification client
            require_once APPROOT . '/models/Notification.php';
            $notif = new Notification();
            $notif->create($res->client_id, 'booking_confirmed', 'Réservation approuvée', 'Votre réservation pour "' . $res->title . '" a été approuvée ! Veuillez procéder au paiement.', 'client/payment/' . $id);
            
            $_SESSION['flash_msg'] = 'Réservation approuvée ! Le bien est maintenant indisponible pour d\'autres clients.';
        }
        $this->redirect('owner/reservations');
    }

    public function rejectReservation($id) {
        $resModel = $this->model('Reservation');
        $res = $resModel->getById($id);
        if ($res && $res->owner_id == $_SESSION['user_id']) {
            $resModel->updateStatus($id, 'rejected');
            $_SESSION['flash_msg'] = 'Réservation refusée.';
        }
        $this->redirect('owner/reservations');
    }

    public function earnings() {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }
        $payModel = $this->model('Payment');
        $payments = $payModel->getOwnerPayments($_SESSION['user_id']);
        $totalEarnings = $payModel->getOwnerEarnings($_SESSION['user_id']);
        $monthlyEarnings = $payModel->getOwnerMonthlyEarnings($_SESSION['user_id']);

        $totalCommission = 0;
        foreach ($payments as $p) $totalCommission += $p->commission;

        $data = [
            'title' => 'Mes Revenus',
            'payments' => $payments,
            'total_earnings' => $totalEarnings,
            'monthly_earnings' => $monthlyEarnings,
            'total_commission' => $totalCommission
        ];
        $this->view('owner/earnings', $data);
    }

    public function messages() {
        if (!$this->isApproved()) { $this->redirect('owner/dashboard'); return; }
        $msgModel = $this->model('Message');
        $conversations = $msgModel->getConversations($_SESSION['user_id']);
        $contacts = $msgModel->getContacts($_SESSION['user_id'], 2);
        $data = ['title' => 'Messages', 'conversations' => $conversations, 'contacts' => $contacts];
        $this->view('owner/messages', $data);
    }
}
