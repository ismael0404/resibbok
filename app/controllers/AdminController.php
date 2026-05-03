<?php
// app/controllers/AdminController.php — Panneau Admin complet

class AdminController extends Controller {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
            $_SESSION['flash_err'] = 'Accès non autorisé';
            $this->redirect('auth/login');
        }
        $this->db = new Database;
    }

    public function dashboard() {
        $userModel = $this->model('User');
        $payModel = $this->model('Payment');
        $resModel = $this->model('Reservation');

        $userStats = $userModel->getStats();
        $payStats = $payModel->getAdminStats();
        $resStats = $resModel->getAdminStats();
        $pendingOwners = $userModel->getPendingOwners();

        // Propriétés
        $this->db->query('SELECT COUNT(*) as total FROM properties');
        $totalProperties = $this->db->single()->total;

        $data = [
            'title' => 'Dashboard Administrateur',
            'total_users' => $userStats['total_users'],
            'total_owners' => $userStats['total_owners'],
            'total_clients' => $userStats['total_clients'],
            'pending_owners' => $userStats['pending_owners'],
            'total_properties' => $totalProperties,
            'total_reservations' => $resStats['total'],
            'pending_reservations' => $resStats['pending'],
            'total_revenue' => $payStats['total_revenue'],
            'total_commission' => $payStats['total_commission'],
            'total_payments' => $payStats['total_payments'],
            'pending_owners_list' => $pendingOwners
        ];
        $this->view('admin/dashboard', $data);
    }

    public function users() {
        $userModel = $this->model('User');
        $role = $_GET['role'] ?? null;
        $users = $userModel->getAllUsers($role ? intval($role) : null);
        $data = ['title' => 'Gestion des Utilisateurs', 'users' => $users, 'filter_role' => $role];
        $this->view('admin/users', $data);
    }

    public function owners() {
        $userModel = $this->model('User');
        $pending = $userModel->getPendingOwners();
        $this->db->query('SELECT * FROM users WHERE role_id = 2 ORDER BY created_at DESC');
        $allOwners = $this->db->resultSet();
        $data = ['title' => 'Gestion des Propriétaires', 'pending_owners' => $pending, 'all_owners' => $allOwners];
        $this->view('admin/owners', $data);
    }

    public function approveOwner($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            if ($userModel->updateStatus($id, 'active')) {
                // Notifier le propriétaire
                require_once APPROOT . '/models/Notification.php';
                $notif = new Notification();
                $notif->create($id, 'account_approved', 'Compte validé', 'Votre compte propriétaire a été approuvé. Vous pouvez maintenant ajouter vos biens.', 'owner/dashboard');
                $_SESSION['flash_msg'] = 'Propriétaire approuvé avec succès !';
            }
            $this->redirect('admin/owners');
        }
    }

    public function rejectOwner($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $reason = trim($_POST['reason'] ?? 'Documents insuffisants');
            $userModel = $this->model('User');
            if ($userModel->updateStatus($id, 'rejected', $reason)) {
                $_SESSION['flash_msg'] = 'Propriétaire rejeté.';
            }
            $this->redirect('admin/owners');
        }
    }

    public function suspendUser($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $userModel->updateStatus($id, 'suspended');
            $_SESSION['flash_msg'] = 'Utilisateur suspendu.';
            $this->redirect('admin/users');
        }
    }

    public function activateUser($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $userModel->updateStatus($id, 'active');
            $_SESSION['flash_msg'] = 'Utilisateur activé.';
            $this->redirect('admin/users');
        }
    }

    public function deleteUser($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = $this->model('User');
            $userModel->deleteUser($id);
            $_SESSION['flash_msg'] = 'Utilisateur supprimé.';
            $this->redirect('admin/users');
        }
    }

    public function residences() {
        $propModel = $this->model('Property');
        $properties = $propModel->getAllAdmin();
        $data = ['title' => 'Gestion des Biens', 'properties' => $properties];
        $this->view('admin/residences', $data);
    }

    public function activateProperty($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $propModel = $this->model('Property');
            $propModel->updateStatus($id, 'active');
            $_SESSION['flash_msg'] = 'Bien activé.';
            $this->redirect('admin/residences');
        }
    }

    public function deactivateProperty($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $propModel = $this->model('Property');
            $propModel->updateStatus($id, 'inactive');
            $_SESSION['flash_msg'] = 'Bien désactivé.';
            $this->redirect('admin/residences');
        }
    }

    public function reservations() {
        $resModel = $this->model('Reservation');
        $reservations = $resModel->getAll();
        $data = ['title' => 'Toutes les Réservations', 'reservations' => $reservations];
        $this->view('admin/reservations', $data);
    }

    public function payments() {
        $payModel = $this->model('Payment');
        $payments = $payModel->getAll();
        $stats = $payModel->getAdminStats();
        $data = ['title' => 'Paiements & Commissions', 'payments' => $payments, 'stats' => $stats];
        $this->view('admin/payments', $data);
    }

    public function messages() {
        $msgModel = $this->model('Message');
        $conversations = $msgModel->getConversations($_SESSION['user_id']);
        $contacts = $msgModel->getContacts($_SESSION['user_id'], 1);
        $data = ['title' => 'Messagerie', 'conversations' => $conversations, 'contacts' => $contacts];
        $this->view('admin/messages', $data);
    }
}
