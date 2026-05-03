<?php
// Sidebar Client
$currentPage = explode('/', $_GET['url'] ?? '');
$page = $currentPage[1] ?? 'profile';
$initials = substr($_SESSION['user_name'], 0, 1);
?>
<aside class="sidebar">
    <div class="sidebar-profile">
        <div class="sidebar-avatar client-avatar"><?= htmlspecialchars($initials); ?></div>
        <h4><?= htmlspecialchars($_SESSION['user_name']); ?></h4>
        <span class="badge-role">Voyageur</span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="<?= URLROOT; ?>/client/profile" class="<?= $page=='profile'?'active':''; ?>"><i class="fa-solid fa-user"></i> Mon Profil</a></li>
        <li><a href="<?= URLROOT; ?>/client/reservations" class="<?= $page=='reservations'||$page=='payment'?'active':''; ?>"><i class="fa-solid fa-suitcase-rolling"></i> Mes Réservations</a></li>
        <li><a href="<?= URLROOT; ?>/client/favorites" class="<?= $page=='favorites'?'active':''; ?>"><i class="fa-solid fa-heart"></i> Favoris</a></li>
        <li><a href="<?= URLROOT; ?>/client/messages" class="<?= $page=='messages'?'active':''; ?>"><i class="fa-solid fa-comments"></i> Messages</a></li>
        <li><a href="<?= URLROOT; ?>/client/settings" class="<?= $page=='settings'?'active':''; ?>"><i class="fa-solid fa-gear"></i> Paramètres</a></li>
    </ul>
</aside>
