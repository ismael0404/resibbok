<?php
// app/models/Property.php — Modèle propriété (résidences, locations, ventes)

class Property {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Toutes les propriétés actives (publiques)
    public function getAll($filters = []) {
        $sql = 'SELECT p.*, c.name as category_name, u.first_name, u.last_name,
                (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
                FROM properties p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.owner_id = u.id
                WHERE p.status = "active"';

        if (!empty($filters['city'])) $sql .= ' AND p.city LIKE :city';
        if (!empty($filters['category'])) $sql .= ' AND p.category_id = :category';
        if (!empty($filters['type'])) $sql .= ' AND p.listing_type = :type';
        if (!empty($filters['min_price'])) {
            $sql .= ' AND (CASE p.listing_type WHEN "reservation" THEN p.price_per_night WHEN "rental" THEN p.price_monthly WHEN "sale" THEN p.price_sale END) >= :min_price';
        }
        if (!empty($filters['max_price'])) {
            $sql .= ' AND (CASE p.listing_type WHEN "reservation" THEN p.price_per_night WHEN "rental" THEN p.price_monthly WHEN "sale" THEN p.price_sale END) <= :max_price';
        }
        if (!empty($filters['bedrooms'])) $sql .= ' AND p.bedrooms >= :bedrooms';
        if (!empty($filters['search'])) $sql .= ' AND (p.title LIKE :search OR p.description LIKE :search OR p.city LIKE :search)';

        $sql .= ' ORDER BY p.is_featured DESC, p.created_at DESC';

        $this->db->query($sql);
        if (!empty($filters['city'])) $this->db->bind(':city', '%' . $filters['city'] . '%');
        if (!empty($filters['category'])) $this->db->bind(':category', $filters['category']);
        if (!empty($filters['type'])) $this->db->bind(':type', $filters['type']);
        if (!empty($filters['min_price'])) $this->db->bind(':min_price', $filters['min_price']);
        if (!empty($filters['max_price'])) $this->db->bind(':max_price', $filters['max_price']);
        if (!empty($filters['bedrooms'])) $this->db->bind(':bedrooms', $filters['bedrooms']);
        if (!empty($filters['search'])) $this->db->bind(':search', '%' . $filters['search'] . '%');

        return $this->db->resultSet();
    }

    // Propriétés à la une
    public function getFeatured($limit = 6) {
        $this->db->query('SELECT p.*, c.name as category_name, u.first_name,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM properties p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN users u ON p.owner_id = u.id
            WHERE p.status = "active" AND p.is_featured = 1
            ORDER BY p.score DESC LIMIT :limit');
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Par ID — avec infos complètes du propriétaire
    public function getById($id) {
        $this->db->query('SELECT p.*, c.name as category_name, 
            u.first_name, u.last_name, u.avatar, u.phone as owner_phone, u.email as owner_email,
            u.city as owner_city, u.created_at as owner_created_at, 
            u.is_verified as owner_is_verified, u.verification_badge as owner_verification_badge,
            u.bio as owner_bio
            FROM properties p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN users u ON p.owner_id = u.id 
            WHERE p.id = :id');
        $this->db->bind(':id', $id);
        $property = $this->db->single();
        if ($property) {
            // Incrémenter les vues
            $this->db->query('UPDATE properties SET views_count = views_count + 1 WHERE id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
        }
        return $property;
    }

    // Nombre d'annonces actives d'un propriétaire
    public function getOwnerPropertyCount($owner_id) {
        $this->db->query('SELECT COUNT(*) as total FROM properties WHERE owner_id = :oid AND status = "active"');
        $this->db->bind(':oid', $owner_id);
        return $this->db->single()->total;
    }

    // Par propriétaire
    public function getByOwner($owner_id) {
        $this->db->query('SELECT p.*, c.name as category_name,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM properties p LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.owner_id = :oid ORDER BY p.created_at DESC');
        $this->db->bind(':oid', $owner_id);
        return $this->db->resultSet();
    }

    // Créer
    public function create($data) {
        $this->db->query('INSERT INTO properties (owner_id, category_id, title, slug, description, listing_type, price_per_night, price_monthly, price_sale, address, city, max_guests, bedrooms, bathrooms, area_sqm, rules, cancellation_policy, status)
            VALUES (:oid, :cat, :title, :slug, :desc, :type, :ppn, :pm, :ps, :addr, :city, :guests, :beds, :baths, :area, :rules, :cancel, "active")');
        $this->db->bind(':oid', $data['owner_id']);
        $this->db->bind(':cat', $data['category_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':type', $data['listing_type']);
        $this->db->bind(':ppn', $data['price_per_night'] ?? 0);
        $this->db->bind(':pm', $data['price_monthly'] ?? 0);
        $this->db->bind(':ps', $data['price_sale'] ?? 0);
        $this->db->bind(':addr', $data['address']);
        $this->db->bind(':city', $data['city']);
        $this->db->bind(':guests', $data['max_guests'] ?? 2);
        $this->db->bind(':beds', $data['bedrooms'] ?? 1);
        $this->db->bind(':baths', $data['bathrooms'] ?? 1);
        $this->db->bind(':area', $data['area_sqm'] ?? 0);
        $this->db->bind(':rules', $data['rules'] ?? '');
        $this->db->bind(':cancel', $data['cancellation_policy'] ?? 'moderate');
        if ($this->db->execute()) return $this->db->lastInsertId();
        return false;
    }

    // Mettre à jour
    public function update($id, $data) {
        $this->db->query('UPDATE properties SET category_id = :cat, title = :title, description = :desc, listing_type = :type, price_per_night = :ppn, price_monthly = :pm, price_sale = :ps, address = :addr, city = :city, max_guests = :guests, bedrooms = :beds, bathrooms = :baths, area_sqm = :area, rules = :rules, cancellation_policy = :cancel WHERE id = :id AND owner_id = :oid');
        $this->db->bind(':cat', $data['category_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':type', $data['listing_type']);
        $this->db->bind(':ppn', $data['price_per_night'] ?? 0);
        $this->db->bind(':pm', $data['price_monthly'] ?? 0);
        $this->db->bind(':ps', $data['price_sale'] ?? 0);
        $this->db->bind(':addr', $data['address']);
        $this->db->bind(':city', $data['city']);
        $this->db->bind(':guests', $data['max_guests'] ?? 2);
        $this->db->bind(':beds', $data['bedrooms'] ?? 1);
        $this->db->bind(':baths', $data['bathrooms'] ?? 1);
        $this->db->bind(':area', $data['area_sqm'] ?? 0);
        $this->db->bind(':rules', $data['rules'] ?? '');
        $this->db->bind(':cancel', $data['cancellation_policy'] ?? 'moderate');
        $this->db->bind(':id', $id);
        $this->db->bind(':oid', $data['owner_id']);
        return $this->db->execute();
    }

    // Supprimer
    public function delete($id, $owner_id) {
        $this->db->query('DELETE FROM properties WHERE id = :id AND owner_id = :oid');
        $this->db->bind(':id', $id);
        $this->db->bind(':oid', $owner_id);
        return $this->db->execute();
    }

    // Images
    public function getImages($property_id) {
        $this->db->query('SELECT * FROM property_images WHERE property_id = :id ORDER BY is_primary DESC, sort_order ASC');
        $this->db->bind(':id', $property_id);
        return $this->db->resultSet();
    }

    public function addImage($property_id, $path, $is_primary = 0, $sort = 0) {
        $this->db->query('INSERT INTO property_images (property_id, image_path, is_primary, sort_order) VALUES (:pid, :path, :primary, :sort)');
        $this->db->bind(':pid', $property_id);
        $this->db->bind(':path', $path);
        $this->db->bind(':primary', $is_primary);
        $this->db->bind(':sort', $sort);
        return $this->db->execute();
    }

    // Équipements
    public function getAmenities($property_id) {
        $this->db->query('SELECT a.* FROM amenities a JOIN property_amenities pa ON a.id = pa.amenity_id WHERE pa.property_id = :id');
        $this->db->bind(':id', $property_id);
        return $this->db->resultSet();
    }

    public function setAmenities($property_id, $amenity_ids) {
        $this->db->query('DELETE FROM property_amenities WHERE property_id = :pid');
        $this->db->bind(':pid', $property_id);
        $this->db->execute();
        foreach ($amenity_ids as $aid) {
            $this->db->query('INSERT INTO property_amenities (property_id, amenity_id) VALUES (:pid, :aid)');
            $this->db->bind(':pid', $property_id);
            $this->db->bind(':aid', intval($aid));
            $this->db->execute();
        }
    }

    // Catégories
    public function getCategories() {
        $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        return $this->db->resultSet();
    }

    // Tous les équipements
    public function getAllAmenities() {
        $this->db->query('SELECT * FROM amenities ORDER BY category, name');
        return $this->db->resultSet();
    }

    // Admin: toutes les propriétés
    public function getAllAdmin() {
        $this->db->query('SELECT p.*, c.name as category_name, u.first_name, u.last_name,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM properties p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN users u ON p.owner_id = u.id
            ORDER BY p.created_at DESC');
        return $this->db->resultSet();
    }

    // Admin: changer le statut
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE properties SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Stats propriétaire
    public function getOwnerStats($owner_id) {
        $stats = [];
        $this->db->query('SELECT COUNT(*) as total FROM properties WHERE owner_id = :oid');
        $this->db->bind(':oid', $owner_id);
        $stats['total_properties'] = $this->db->single()->total;
        $this->db->query('SELECT COUNT(*) as total FROM properties WHERE owner_id = :oid AND status = "active"');
        $this->db->bind(':oid', $owner_id);
        $stats['active_properties'] = $this->db->single()->total;
        return $stats;
    }

    // Reviews
    public function getReviews($property_id) {
        $this->db->query('SELECT r.*, u.first_name, u.last_name, u.avatar FROM reviews r JOIN users u ON r.client_id = u.id WHERE r.property_id = :pid ORDER BY r.created_at DESC');
        $this->db->bind(':pid', $property_id);
        return $this->db->resultSet();
    }

    // Biens similaires
    public function getSimilarProperties($property_id, $category_id, $city, $limit = 4) {
        $this->db->query('SELECT p.*, c.name as category_name,
            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
            FROM properties p LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = "active" AND p.id != :pid AND (p.category_id = :cat OR p.city = :city)
            ORDER BY (p.category_id = :cat) DESC, (p.city = :city) DESC, p.created_at DESC LIMIT :limit');
        $this->db->bind(':pid', $property_id);
        $this->db->bind(':cat', $category_id);
        $this->db->bind(':city', $city);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}
