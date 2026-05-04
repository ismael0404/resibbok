<?php
// app/controllers/MessagesController.php — Messagerie AJAX

class MessagesController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Non connecté']);
                exit;
            }
            $this->redirect('auth/login');
        }
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    // API: Obtenir les messages d'une conversation
    public function thread($other_user_id) {
        header('Content-Type: application/json');
        $msgModel = $this->model('Message');
        $msgModel->markThreadAsRead($_SESSION['user_id'], $other_user_id);
        $messages = $msgModel->getThread($_SESSION['user_id'], $other_user_id);
        
        // Récupérer infos du bien lié à cette conversation
        $propertyInfo = $msgModel->getThreadPropertyInfo($_SESSION['user_id'], $other_user_id);
        
        echo json_encode([
            'success' => true, 
            'messages' => $messages, 
            'current_user' => $_SESSION['user_id'],
            'property_info' => $propertyInfo
        ]);
        exit;
    }

    // API: Démarrer ou ouvrir une conversation (appelé depuis show.php)
    public function startConversation() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $receiver_id = intval($input['receiver_id'] ?? 0);
        $message = trim($input['message'] ?? '');
        $property_id = intval($input['property_id'] ?? 0) ?: null;

        if ($receiver_id <= 0 || empty($message)) {
            echo json_encode(['error' => 'Données manquantes']);
            exit;
        }

        // Empêcher de s'envoyer un message à soi-même
        if ($receiver_id == $_SESSION['user_id']) {
            echo json_encode(['error' => 'Impossible de vous envoyer un message']);
            exit;
        }

        $msgModel = $this->model('Message');

        // Envoyer le message (créer la conversation)
        $id = $msgModel->send($_SESSION['user_id'], $receiver_id, $message, $property_id);

        if ($id) {
            // Notification
            require_once APPROOT . '/models/Notification.php';
            $notif = new Notification();
            $notif->create($receiver_id, 'new_message', 'Nouveau message', $_SESSION['user_name'] . ' vous a envoyé un message.', 'messages');
            
            echo json_encode([
                'success' => true, 
                'message_id' => $id, 
                'receiver_id' => $receiver_id
            ]);
        } else {
            echo json_encode(['error' => 'Erreur d\'envoi']);
        }
        exit;
    }

    // API: Envoyer un message
    public function send() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $receiver_id = intval($input['receiver_id'] ?? 0);
        $message = trim($input['message'] ?? '');
        $property_id = intval($input['property_id'] ?? 0) ?: null;

        if ($receiver_id <= 0 || empty($message)) {
            echo json_encode(['error' => 'Données manquantes']);
            exit;
        }

        $msgModel = $this->model('Message');
        $id = $msgModel->send($_SESSION['user_id'], $receiver_id, $message, $property_id);

        if ($id) {
            // Notification
            require_once APPROOT . '/models/Notification.php';
            $notif = new Notification();
            $notif->create($receiver_id, 'new_message', 'Nouveau message', $_SESSION['user_name'] . ' vous a envoyé un message.', 'messages');
            echo json_encode(['success' => true, 'message_id' => $id]);
        } else {
            echo json_encode(['error' => 'Erreur d\'envoi']);
        }
        exit;
    }

    // API: Nombre de non-lus
    public function unread() {
        header('Content-Type: application/json');
        $msgModel = $this->model('Message');
        $count = $msgModel->getUnreadCount($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
        exit;
    }

    // API: Infos du bien lié à une conversation
    public function propertyInfo($other_user_id) {
        header('Content-Type: application/json');
        $msgModel = $this->model('Message');
        $info = $msgModel->getThreadPropertyInfo($_SESSION['user_id'], $other_user_id);
        echo json_encode(['success' => true, 'property' => $info]);
        exit;
    }
}
