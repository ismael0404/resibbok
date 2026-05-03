<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_admin.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 2rem;">Vue d'ensemble</h2>
            <div style="color: var(--text-muted); font-weight: 500;"><i class="fa-solid fa-calendar"></i> <?= date('d M Y'); ?></div>
        </div>
        
        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Revenus Totaux (Commissions)</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(0, 166, 153, 0.1); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= number_format($data['total_commission'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Sur <?= number_format($data['total_revenue'], 0, ',', ' '); ?> FCFA générés</div>
            </div>
            
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Propriétaires en attente</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255, 180, 0, 0.1); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700; color: <?= $data['pending_owners'] > 0 ? 'var(--warning)' : 'inherit'; ?>;"><?= $data['pending_owners']; ?></p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Sur <?= $data['total_owners']; ?> propriétaires inscrits</div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Biens Immobiliers</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255, 56, 92, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= $data['total_properties']; ?></p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Publiés sur la plateforme</div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Réservations</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(0, 138, 5, 0.1); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= $data['total_reservations']; ?></p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);"><?= $data['pending_reservations']; ?> en attente</div>
            </div>
        </div>

        <!-- Pending Owners Validation Table -->
        <div class="table-container">
            <div style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.2rem; font-weight: 600;"><i class="fa-solid fa-user-check text-primary"></i> Propriétaires en attente de validation</h3>
                <a href="<?= URLROOT; ?>/admin/owners" class="btn btn-outline btn-sm">Voir tous</a>
            </div>
            
            <?php if(empty($data['pending_owners_list'])) : ?>
                <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                    <i class="fa-solid fa-check-circle" style="font-size: 4rem; color: var(--success); margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                    <p style="font-size: 1.1rem; font-weight: 500;">Aucun propriétaire en attente de validation.</p>
                </div>
            <?php else : ?>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>Inscription</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['pending_owners_list'] as $owner) : ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,56,92,0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        <?= substr($owner->first_name, 0, 1) . substr($owner->last_name, 0, 1); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;"><?= htmlspecialchars($owner->first_name . ' ' . $owner->last_name); ?></div>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($owner->city); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-envelope" style="color: var(--text-muted); width: 15px;"></i> <?= htmlspecialchars($owner->email); ?></div>
                                <div style="margin-top: 5px;"><i class="fa-solid fa-phone" style="color: var(--text-muted); width: 15px;"></i> <?= htmlspecialchars($owner->phone); ?></div>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($owner->created_at)); ?></td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <form action="<?= URLROOT; ?>/admin/approveOwner/<?= $owner->id; ?>" method="POST" style="display: inline;">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(0, 138, 5, 0.1); color: var(--success); border: 1px solid rgba(0, 138, 5, 0.2);" title="Approuver">
                                            <i class="fa-solid fa-check"></i> Approuver
                                        </button>
                                    </form>
                                    <form action="<?= URLROOT; ?>/admin/rejectOwner/<?= $owner->id; ?>" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter ce propriétaire ?');">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(225, 44, 50, 0.1); color: var(--danger); border: 1px solid rgba(225, 44, 50, 0.2);" title="Rejeter">
                                            <i class="fa-solid fa-xmark"></i> Rejeter
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
