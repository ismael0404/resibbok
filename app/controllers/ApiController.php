<?php
// app/controllers/ApiController.php — API interne pour AJAX

class ApiController extends Controller {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Toggle favoris
    public function toggleFavorite() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Connectez-vous']);
            exit;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $property_id = intval($input['property_id'] ?? 0);
        if ($property_id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID invalide']);
            exit;
        }
        $uid = $_SESSION['user_id'];

        $this->db->query('SELECT id FROM favorites WHERE user_id = :uid AND property_id = :pid');
        $this->db->bind(':uid', $uid);
        $this->db->bind(':pid', $property_id);
        $existing = $this->db->single();

        if ($existing) {
            $this->db->query('DELETE FROM favorites WHERE id = :id');
            $this->db->bind(':id', $existing->id);
            $this->db->execute();
            echo json_encode(['success' => true, 'action' => 'removed']);
        } else {
            $this->db->query('INSERT INTO favorites (user_id, property_id) VALUES (:uid, :pid)');
            $this->db->bind(':uid', $uid);
            $this->db->bind(':pid', $property_id);
            $this->db->execute();
            echo json_encode(['success' => true, 'action' => 'added']);
        }
        exit;
    }

    // Notifications
    public function notifications() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }
        require_once APPROOT . '/models/Notification.php';
        $notif = new Notification();
        $notifications = $notif->getUserNotifications($_SESSION['user_id'], 10);
        $unread = $notif->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['success' => true, 'notifications' => $notifications, 'unread' => $unread]);
        exit;
    }

    // Marquer notifications comme lues
    public function markNotificationsRead() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }
        require_once APPROOT . '/models/Notification.php';
        $notif = new Notification();
        $notif->markAllAsRead($_SESSION['user_id']);
        echo json_encode(['success' => true]);
        exit;
    }

    // Recherche globale
    public function search() {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (empty($q)) {
            echo json_encode(['results' => []]);
            exit;
        }
        $this->db->query('SELECT id, title, city, listing_type, price_per_night, price_monthly, price_sale,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = properties.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM properties WHERE status = "active" AND (title LIKE :q OR city LIKE :q OR description LIKE :q) LIMIT 10');
        $this->db->bind(':q', '%' . $q . '%');
        $results = $this->db->resultSet();
        echo json_encode(['results' => $results]);
        exit;
    }
}
