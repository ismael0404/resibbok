<?php
// app/models/Message.php

class Message {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function send($sender_id, $receiver_id, $message, $property_id = null, $subject = null) {
        $this->db->query('INSERT INTO messages (sender_id, receiver_id, property_id, subject, message) VALUES (:sid, :rid, :pid, :subj, :msg)');
        $this->db->bind(':sid', $sender_id);
        $this->db->bind(':rid', $receiver_id);
        $this->db->bind(':pid', $property_id);
        $this->db->bind(':subj', $subject);
        $this->db->bind(':msg', $message);
        if ($this->db->execute()) return $this->db->lastInsertId();
        return false;
    }

    // Liste des conversations pour un utilisateur
    public function getConversations($user_id) {
        $this->db->query('SELECT m.*, 
            CASE WHEN m.sender_id = :uid THEN m.receiver_id ELSE m.sender_id END as other_user_id,
            u.first_name, u.last_name, u.avatar, u.role_id,
            (SELECT COUNT(*) FROM messages m2 WHERE m2.sender_id = CASE WHEN m.sender_id = :uid2 THEN m.receiver_id ELSE m.sender_id END AND m2.receiver_id = :uid3 AND m2.is_read = 0) as unread_count
            FROM messages m
            JOIN users u ON u.id = CASE WHEN m.sender_id = :uid4 THEN m.receiver_id ELSE m.sender_id END
            WHERE m.id IN (
                SELECT MAX(id) FROM messages WHERE sender_id = :uid5 OR receiver_id = :uid6 GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
            )
            ORDER BY m.created_at DESC');
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':uid2', $user_id);
        $this->db->bind(':uid3', $user_id);
        $this->db->bind(':uid4', $user_id);
        $this->db->bind(':uid5', $user_id);
        $this->db->bind(':uid6', $user_id);
        return $this->db->resultSet();
    }

    // Messages d'une conversation
    public function getThread($user_id, $other_user_id) {
        $this->db->query('SELECT m.*, u.first_name, u.last_name, u.avatar FROM messages m JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = :uid AND m.receiver_id = :oid) OR (m.sender_id = :oid2 AND m.receiver_id = :uid2)
            ORDER BY m.created_at ASC');
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':oid', $other_user_id);
        $this->db->bind(':oid2', $other_user_id);
        $this->db->bind(':uid2', $user_id);
        return $this->db->resultSet();
    }

    // Marquer comme lu
    public function markThreadAsRead($user_id, $sender_id) {
        $this->db->query('UPDATE messages SET is_read = 1 WHERE receiver_id = :uid AND sender_id = :sid AND is_read = 0');
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':sid', $sender_id);
        return $this->db->execute();
    }

    // Nombre de messages non lus
    public function getUnreadCount($user_id) {
        $this->db->query('SELECT COUNT(*) as total FROM messages WHERE receiver_id = :uid AND is_read = 0');
        $this->db->bind(':uid', $user_id);
        return $this->db->single()->total;
    }

    // Tous les utilisateurs avec qui on peut discuter
    public function getContacts($user_id, $role_id) {
        if ($role_id == 1) {
            // Admin : tous les utilisateurs
            $this->db->query('SELECT id, first_name, last_name, role_id, avatar FROM users WHERE id != :uid ORDER BY first_name');
        } elseif ($role_id == 2) {
            // Propriétaire : admin + clients qui ont réservé
            $this->db->query('SELECT DISTINCT u.id, u.first_name, u.last_name, u.role_id, u.avatar FROM users u
                WHERE u.role_id = 1 OR u.id IN (SELECT r.client_id FROM reservations r JOIN properties p ON r.property_id = p.id WHERE p.owner_id = :uid)
                ORDER BY u.first_name');
        } else {
            // Client : admin + propriétaires des biens réservés
            $this->db->query('SELECT DISTINCT u.id, u.first_name, u.last_name, u.role_id, u.avatar FROM users u
                WHERE u.role_id = 1 OR u.id IN (SELECT p.owner_id FROM reservations r JOIN properties p ON r.property_id = p.id WHERE r.client_id = :uid)
                ORDER BY u.first_name');
        }
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }
}
