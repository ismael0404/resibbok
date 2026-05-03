<?php
// app/views/inc/header.php
$currentUrl = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITENAME; ?> — <?= $data['title'] ?? ''; ?></title>
    <meta name="description" content="ResiBook - Plateforme immobilière de référence. Réservation, location et vente de biens.">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= URLROOT; ?>/public/css/style.css">
</head>
<body>

<nav class="navbar" id="main-navbar">
    <a href="<?= URLROOT; ?>" class="logo">
        <i class="fa-solid fa-house-chimney"></i> ResiBook
    </a>
    <div class="search-bar" id="global-search">
        <input type="text" placeholder="Rechercher un bien, une ville..." id="search-input" autocomplete="off">
        <button onclick="doSearch()"><i class="fa-solid fa-magnifying-glass"></i></button>
        <div class="search-results" id="search-results"></div>
    </div>
    <div class="nav-links">
        <?php if(isset($_SESSION['user_id'])) : ?>
            <div class="notif-bell" id="notif-bell" onclick="toggleNotifications()">
                <i class="fa-solid fa-bell"></i>
                <span class="notif-badge" id="notif-badge" style="display:none;">0</span>
                <div class="notif-dropdown" id="notif-dropdown">
                    <div class="notif-header">Notifications</div>
                    <div class="notif-list" id="notif-list"></div>
                </div>
            </div>
            <span class="nav-user-name">Bonjour, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
            <?php if($_SESSION['user_role'] == 1): ?>
                <a href="<?= URLROOT; ?>/admin/dashboard" class="btn btn-outline btn-sm">Dashboard</a>
            <?php elseif($_SESSION['user_role'] == 2): ?>
                <a href="<?= URLROOT; ?>/owner/dashboard" class="btn btn-outline btn-sm">Mon Espace</a>
            <?php else: ?>
                <a href="<?= URLROOT; ?>/client/profile" class="btn btn-outline btn-sm">Mon Profil</a>
            <?php endif; ?>
            <a href="<?= URLROOT; ?>/auth/logout" class="btn btn-primary btn-sm">Déconnexion</a>
        <?php else : ?>
            <a href="<?= URLROOT; ?>/residences" class="nav-link-text">Explorer</a>
            <a href="<?= URLROOT; ?>/auth/register" class="btn btn-outline btn-sm">Inscription</a>
            <a href="<?= URLROOT; ?>/auth/login" class="btn btn-primary btn-sm">Connexion</a>
        <?php endif; ?>
    </div>
</nav>

<main class="main-wrapper">
