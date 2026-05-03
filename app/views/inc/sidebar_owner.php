<?php
// Sidebar Owner
$currentPage = explode('/', $_GET['url'] ?? '');
$page = $currentPage[1] ?? 'dashboard';
$initials = substr($_SESSION['user_name'], 0, 1);
?>
<aside class="sidebar">
    <div class="sidebar-profile">
        <div class="sidebar-avatar"><?= htmlspecialchars($initials); ?></div>
        <h4><?= htmlspecialchars($_SESSION['user_name']); ?></h4>
        <?php if(($_SESSION['user_status'] ?? '') == 'active'): ?>
            <span class="badge badge-success"><i class="fa-solid fa-check-circle"></i> Vérifié</span>
        <?php else: ?>
            <span class="badge badge-pending"><i class="fa-solid fa-clock"></i> En attente</span>
        <?php endif; ?>
    </div>
    <ul class="sidebar-nav">
        <li><a href="<?= URLROOT; ?>/owner/dashboard" class="<?= $page=='dashboard'?'active':''; ?>"><i class="fa-solid fa-gauge-high"></i> Tableau de bord</a></li>
        <li><a href="<?= URLROOT; ?>/owner/residences" class="<?= $page=='residences'||$page=='addResidence'||$page=='editResidence'?'active':''; ?>"><i class="fa-solid fa-building"></i> Mes Biens</a></li>
        <li><a href="<?= URLROOT; ?>/owner/reservations" class="<?= $page=='reservations'?'active':''; ?>"><i class="fa-solid fa-calendar-check"></i> Réservations</a></li>
        <li><a href="<?= URLROOT; ?>/owner/earnings" class="<?= $page=='earnings'?'active':''; ?>"><i class="fa-solid fa-wallet"></i> Revenus</a></li>
        <li><a href="<?= URLROOT; ?>/owner/messages" class="<?= $page=='messages'?'active':''; ?>"><i class="fa-solid fa-comments"></i> Messages</a></li>
    </ul>
</aside>
