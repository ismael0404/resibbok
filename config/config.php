<?php
// config/config.php

// Base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'resibook');

// URL & Chemins
define('BASE_URL', 'http://localhost/resibook');
define('APPROOT', dirname(dirname(__FILE__)) . '/app');
define('URLROOT', BASE_URL);
define('SITENAME', 'ResiBook');
define('UPLOADS_PATH', dirname(dirname(__FILE__)) . '/uploads/');
define('UPLOADS_URL', BASE_URL . '/uploads/');

// Plateforme
define('COMMISSION_RATE', 10); // Pourcentage commission admin
define('CURRENCY', 'FCFA');

// Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
