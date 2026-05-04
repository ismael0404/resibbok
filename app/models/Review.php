<?php
// app/models/Review.php

class Review {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Ajouter un avis
    public function addReview($data) {
        $this->db->query('INSERT INTO reviews (property_id, client_id, rating, comment, created_at) VALUES (:pid, :cid, :rating, :comment, NOW())');
        $this->db->bind(':pid', $data['property_id']);
        $this->db->bind(':cid', $data['client_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);

        if ($this->db->execute()) {
            return true;
        }
        return false;
    }

    // Vérifier si l'utilisateur a déjà commenté ce bien
    public function hasUserReviewed($property_id, $client_id) {
        $this->db->query('SELECT id FROM reviews WHERE property_id = :pid AND client_id = :cid');
        $this->db->bind(':pid', $property_id);
        $this->db->bind(':cid', $client_id);
        
        $row = $this->db->single();
        return ($row) ? true : false;
    }

    // Mettre à jour la moyenne de la propriété
    public function updatePropertyScore($property_id) {
        // Calculer la moyenne
        $this->db->query('SELECT AVG(rating) as avg_rating FROM reviews WHERE property_id = :pid');
        $this->db->bind(':pid', $property_id);
        $result = $this->db->single();
        
        $avg = $result->avg_rating ? round($result->avg_rating, 2) : 0;

        // Mettre à jour la propriété
        $this->db->query('UPDATE properties SET score = :score WHERE id = :pid');
        $this->db->bind(':score', $avg);
        $this->db->bind(':pid', $property_id);
        
        return $this->db->execute();
    }
}
