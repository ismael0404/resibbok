<?php
// app/models/Notification.php

class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function create($user_id, $type, $title, $message, $link = null) {
        $this->db->query('INSERT INTO notifications (user_id, type, title, message, link) VALUES (:uid, :type, :title, :msg, :link)');
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':type', $type);
        $this->db->bind(':title', $title);
        $this->db->bind(':msg', $message);
        $this->db->bind(':link', $link);
        return $this->db->execute();
    }

    public function getUserNotifications($user_id, $limit = 10) {
        $this->db->query('SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit');
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getUnreadCount($user_id) {
        $this->db->query('SELECT COUNT(*) as total FROM notifications WHERE user_id = :uid AND is_read = 0');
        $this->db->bind(':uid', $user_id);
        return $this->db->single()->total;
    }

    public function markAsRead($id) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function markAllAsRead($user_id) {
        $this->db->query('UPDATE notifications SET is_read = 1 WHERE user_id = :uid');
        $this->db->bind(':uid', $user_id);
        return $this->db->execute();
    }
}
