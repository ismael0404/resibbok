<?php
// app/models/Reservation.php

class Reservation {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function create($data) {
        $this->db->query('INSERT INTO reservations (property_id, client_id, check_in, check_out, guests, nights, price_per_night, subtotal, service_fee, total_price, special_requests, status)
            VALUES (:pid, :cid, :ci, :co, :guests, :nights, :ppn, :sub, :fee, :total, :req, "pending")');
        $this->db->bind(':pid', $data['property_id']);
        $this->db->bind(':cid', $data['client_id']);
        $this->db->bind(':ci', $data['check_in']);
        $this->db->bind(':co', $data['check_out']);
        $this->db->bind(':guests', $data['guests']);
        $this->db->bind(':nights', $data['nights']);
        $this->db->bind(':ppn', $data['price_per_night']);
        $this->db->bind(':sub', $data['subtotal']);
        $this->db->bind(':fee', $data['service_fee']);
        $this->db->bind(':total', $data['total_price']);
        $this->db->bind(':req', $data['special_requests'] ?? '');
        if ($this->db->execute()) return $this->db->lastInsertId();
        return false;
    }

    public function getById($id) {
        $this->db->query('SELECT r.*, p.title, p.city, p.owner_id, p.listing_type, u.first_name, u.last_name, u.email, u.phone,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM reservations r JOIN properties p ON r.property_id = p.id JOIN users u ON r.client_id = u.id WHERE r.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function checkAvailability($property_id, $check_in, $check_out) {
        $this->db->query('SELECT COUNT(*) as conflicts FROM reservations
            WHERE property_id = :pid AND status IN ("pending","confirmed")
            AND ((check_in < :co AND check_out > :ci))');
        $this->db->bind(':pid', $property_id);
        $this->db->bind(':ci', $check_in);
        $this->db->bind(':co', $check_out);
        return $this->db->single()->conflicts == 0;
    }

    public function getByClient($client_id) {
        $this->db->query('SELECT r.*, p.title, p.city, p.price_per_night as prop_price,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM reservations r JOIN properties p ON r.property_id = p.id WHERE r.client_id = :cid ORDER BY r.created_at DESC');
        $this->db->bind(':cid', $client_id);
        return $this->db->resultSet();
    }

    public function getByOwner($owner_id) {
        $this->db->query('SELECT r.*, p.title, u.first_name, u.last_name, u.email, u.phone
            FROM reservations r JOIN properties p ON r.property_id = p.id JOIN users u ON r.client_id = u.id
            WHERE p.owner_id = :oid ORDER BY r.created_at DESC');
        $this->db->bind(':oid', $owner_id);
        return $this->db->resultSet();
    }

    public function getAll() {
        $this->db->query('SELECT r.*, p.title, p.city, uc.first_name as client_fn, uc.last_name as client_ln, uo.first_name as owner_fn, uo.last_name as owner_ln
            FROM reservations r JOIN properties p ON r.property_id = p.id JOIN users uc ON r.client_id = uc.id JOIN users uo ON p.owner_id = uo.id ORDER BY r.created_at DESC');
        return $this->db->resultSet();
    }

    public function updateStatus($id, $status) {
        $this->db->query('UPDATE reservations SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function cancel($id, $reason = '') {
        $this->db->query('UPDATE reservations SET status = "cancelled", cancellation_reason = :reason WHERE id = :id');
        $this->db->bind(':reason', $reason);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getOwnerActiveCount($owner_id) {
        $this->db->query('SELECT COUNT(*) as total FROM reservations r JOIN properties p ON r.property_id = p.id WHERE p.owner_id = :oid AND r.status IN ("pending","confirmed")');
        $this->db->bind(':oid', $owner_id);
        return $this->db->single()->total;
    }

    public function getAdminStats() {
        $stats = [];
        $this->db->query('SELECT COUNT(*) as total FROM reservations');
        $stats['total'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM reservations WHERE status = "pending"');
        $stats['pending'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM reservations WHERE status = "confirmed"');
        $stats['confirmed'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM reservations WHERE status = "completed"');
        $stats['completed'] = $this->db->single()->total;
        return $stats;
    }
}
