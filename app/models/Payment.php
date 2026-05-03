<?php
// app/models/Payment.php

class Payment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function create($data) {
        $this->db->query('INSERT INTO payments (reservation_id, rental_id, sale_id, payer_id, amount, commission, owner_amount, payment_method, payment_phone, payment_name, status, transaction_id, paid_at)
            VALUES (:rid, :rlid, :sid, :pid, :amount, :comm, :owner, :method, :phone, :name, "completed", :txn, NOW())');
        $this->db->bind(':rid', $data['reservation_id'] ?? null);
        $this->db->bind(':rlid', $data['rental_id'] ?? null);
        $this->db->bind(':sid', $data['sale_id'] ?? null);
        $this->db->bind(':pid', $data['payer_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':comm', $data['commission']);
        $this->db->bind(':owner', $data['owner_amount']);
        $this->db->bind(':method', $data['payment_method']);
        $this->db->bind(':phone', $data['payment_phone'] ?? null);
        $this->db->bind(':name', $data['payment_name']);
        $this->db->bind(':txn', $data['transaction_id']);
        if ($this->db->execute()) return $this->db->lastInsertId();
        return false;
    }

    public function getByReservation($reservation_id) {
        $this->db->query('SELECT * FROM payments WHERE reservation_id = :rid ORDER BY created_at DESC');
        $this->db->bind(':rid', $reservation_id);
        return $this->db->single();
    }

    public function getByPayer($payer_id) {
        $this->db->query('SELECT pay.*, r.id as res_id, p.title FROM payments pay
            LEFT JOIN reservations r ON pay.reservation_id = r.id LEFT JOIN properties p ON r.property_id = p.id
            WHERE pay.payer_id = :pid ORDER BY pay.created_at DESC');
        $this->db->bind(':pid', $payer_id);
        return $this->db->resultSet();
    }

    public function getOwnerPayments($owner_id) {
        $this->db->query('SELECT pay.*, r.check_in, r.check_out, p.title, u.first_name, u.last_name
            FROM payments pay JOIN reservations r ON pay.reservation_id = r.id JOIN properties p ON r.property_id = p.id JOIN users u ON pay.payer_id = u.id
            WHERE p.owner_id = :oid AND pay.status = "completed" ORDER BY pay.paid_at DESC');
        $this->db->bind(':oid', $owner_id);
        return $this->db->resultSet();
    }

    public function getAll() {
        $this->db->query('SELECT pay.*, u.first_name, u.last_name, p.title
            FROM payments pay JOIN users u ON pay.payer_id = u.id
            LEFT JOIN reservations r ON pay.reservation_id = r.id LEFT JOIN properties p ON r.property_id = p.id
            ORDER BY pay.created_at DESC');
        return $this->db->resultSet();
    }

    public function getOwnerEarnings($owner_id) {
        $this->db->query('SELECT COALESCE(SUM(pay.owner_amount), 0) as total FROM payments pay
            JOIN reservations r ON pay.reservation_id = r.id JOIN properties p ON r.property_id = p.id
            WHERE p.owner_id = :oid AND pay.status = "completed"');
        $this->db->bind(':oid', $owner_id);
        return $this->db->single()->total;
    }

    public function getOwnerMonthlyEarnings($owner_id) {
        $this->db->query('SELECT COALESCE(SUM(pay.owner_amount), 0) as total FROM payments pay
            JOIN reservations r ON pay.reservation_id = r.id JOIN properties p ON r.property_id = p.id
            WHERE p.owner_id = :oid AND pay.status = "completed" AND MONTH(pay.paid_at) = MONTH(CURDATE()) AND YEAR(pay.paid_at) = YEAR(CURDATE())');
        $this->db->bind(':oid', $owner_id);
        return $this->db->single()->total;
    }

    public function getAdminStats() {
        $stats = [];
        $this->db->query('SELECT COALESCE(SUM(amount), 0) as total_revenue, COALESCE(SUM(commission), 0) as total_commission, COALESCE(SUM(owner_amount), 0) as total_owner, COUNT(*) as total_payments FROM payments WHERE status = "completed"');
        $row = $this->db->single();
        $stats['total_revenue'] = $row->total_revenue;
        $stats['total_commission'] = $row->total_commission;
        $stats['total_owner'] = $row->total_owner;
        $stats['total_payments'] = $row->total_payments;
        return $stats;
    }
}
