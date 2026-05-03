<?php
// Sidebar Admin
$currentPage = explode('/', $_GET['url'] ?? '');
$page = $currentPage[1] ?? 'dashboard';
?>
<aside class="sidebar">
    <div class="sidebar-profile">
        <div class="sidebar-avatar admin-avatar"><i class="fa-solid fa-shield-halved"></i></div>
        <h4>Super Admin</h4>
        <span class="badge badge-admin">Administrateur</span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="<?= URLROOT; ?>/admin/dashboard" class="<?= $page=='dashboard'?'active':''; ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a></li>
        <li><a href="<?= URLROOT; ?>/admin/owners" class="<?= $page=='owners'?'active':''; ?>"><i class="fa-solid fa-user-check"></i> Propriétaires</a></li>
        <li><a href="<?= URLROOT; ?>/admin/users" class="<?= $page=='users'?'active':''; ?>"><i class="fa-solid fa-users"></i> Utilisateurs</a></li>
        <li><a href="<?= URLROOT; ?>/admin/residences" class="<?= $page=='residences'?'active':''; ?>"><i class="fa-solid fa-building"></i> Biens</a></li>
        <li><a href="<?= URLROOT; ?>/admin/reservations" class="<?= $page=='reservations'?'active':''; ?>"><i class="fa-solid fa-calendar-check"></i> Réservations</a></li>
        <li><a href="<?= URLROOT; ?>/admin/payments" class="<?= $page=='payments'?'active':''; ?>"><i class="fa-solid fa-credit-card"></i> Paiements</a></li>
        <li><a href="<?= URLROOT; ?>/admin/messages" class="<?= $page=='messages'?'active':''; ?>"><i class="fa-solid fa-comments"></i> Messages</a></li>
    </ul>
</aside>
