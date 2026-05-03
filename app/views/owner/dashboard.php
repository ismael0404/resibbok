<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_owner.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mon Tableau de bord</h2>
            <a href="<?= URLROOT; ?>/owner/addResidence" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ajouter un bien</a>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Mes Biens</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255, 56, 92, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-building"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= $data['total_properties']; ?></p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);"><?= $data['active_properties']; ?> actifs</div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Réservations Actives</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(0, 166, 153, 0.1); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= $data['active_bookings']; ?></p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">En attente ou confirmées</div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Revenus du mois</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(0, 138, 5, 0.1); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= number_format($data['monthly_earnings'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Mois de <?= date('F'); ?></div>
            </div>

            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500;">Revenus Totaux nets</h3>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255, 180, 0, 0.1); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                </div>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= number_format($data['total_earnings'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Depuis l'inscription</div>
            </div>
        </div>

        <div class="table-container">
            <div style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.2rem; font-weight: 600;">Réservations récentes</h3>
                <a href="<?= URLROOT; ?>/owner/reservations" class="btn btn-outline btn-sm">Voir tout</a>
            </div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th>Client</th>
                            <th>Dates</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['recent_bookings'])): ?>
                            <tr><td colspan="5" style="text-align: center; padding: 30px;">Aucune réservation récente.</td></tr>
                        <?php else: foreach($data['recent_bookings'] as $res) : ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($res->title); ?></td>
                            <td><?= htmlspecialchars($res->first_name . ' ' . $res->last_name); ?></td>
                            <td><?= date('d/m/Y', strtotime($res->check_in)); ?> au <?= date('d/m/Y', strtotime($res->check_out)); ?></td>
                            <td style="font-weight: 600;"><?= number_format($res->total_price, 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <?php if($res->status == 'pending'): ?><span class="badge badge-pending">En attente</span>
                                <?php elseif($res->status == 'confirmed'): ?><span class="badge" style="background: rgba(0, 166, 153, 0.15); color: var(--secondary);">Confirmée</span>
                                <?php elseif($res->status == 'completed'): ?><span class="badge badge-success">Terminée</span>
                                <?php else: ?><span class="badge badge-danger">Annulée</span><?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
