<?php
// app/models/User.php — Modèle utilisateur complet

class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Inscription
    public function register($data) {
        $this->db->query('INSERT INTO users (role_id, first_name, last_name, email, password, phone, status) VALUES(:role_id, :first_name, :last_name, :email, :password, :phone, :status)');
        $this->db->bind(':role_id', $data['role_id']);
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute();
    }

    // Trouver par email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // Connexion
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        if (!$row) return false;
        if (password_verify($password, $row->password)) {
            // Mettre à jour last_login
            $this->db->query('UPDATE users SET last_login = NOW() WHERE id = :id');
            $this->db->bind(':id', $row->id);
            $this->db->execute();
            return $row;
        }
        return false;
    }

    // Obtenir par ID
    public function getUserById($id) {
        $this->db->query('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Tous les utilisateurs (admin)
    public function getAllUsers($role = null) {
        $sql = 'SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.role_id != 1';
        if ($role) {
            $sql .= ' AND u.role_id = :role';
        }
        $sql .= ' ORDER BY u.created_at DESC';
        $this->db->query($sql);
        if ($role) $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }

    // Propriétaires en attente
    public function getPendingOwners() {
        $this->db->query('SELECT * FROM users WHERE role_id = 2 AND status = "pending" ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // Mettre à jour le statut
    public function updateStatus($id, $status, $reason = null) {
        if ($status === 'active') {
            $this->db->query('UPDATE users SET status = :status, is_verified = 1, verification_badge = 1 WHERE id = :id');
        } elseif ($status === 'rejected') {
            $this->db->query('UPDATE users SET status = :status, rejection_reason = :reason WHERE id = :id');
            $this->db->bind(':reason', $reason);
        } else {
            $this->db->query('UPDATE users SET status = :status WHERE id = :id');
        }
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Mettre à jour le profil
    public function updateProfile($id, $data) {
        $this->db->query('UPDATE users SET first_name = :fn, last_name = :ln, phone = :phone, city = :city, bio = :bio, address = :addr WHERE id = :id');
        $this->db->bind(':fn', $data['first_name']);
        $this->db->bind(':ln', $data['last_name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':city', $data['city'] ?? '');
        $this->db->bind(':bio', $data['bio'] ?? '');
        $this->db->bind(':addr', $data['address'] ?? '');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Mettre à jour l'avatar
    public function updateAvatar($id, $avatar) {
        $this->db->query('UPDATE users SET avatar = :avatar WHERE id = :id');
        $this->db->bind(':avatar', $avatar);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Supprimer utilisateur
    public function deleteUser($id) {
        $this->db->query('DELETE FROM users WHERE id = :id AND role_id != 1');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Statistiques admin
    public function getStats() {
        $stats = [];
        $this->db->query('SELECT COUNT(*) as total FROM users WHERE role_id != 1');
        $stats['total_users'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM users WHERE role_id = 2');
        $stats['total_owners'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM users WHERE role_id = 3');
        $stats['total_clients'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM users WHERE role_id = 2 AND status = "pending"');
        $stats['pending_owners'] = $this->db->single()->total;
        return $stats;
    }
}
