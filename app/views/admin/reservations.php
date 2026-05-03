<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_admin.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Toutes les Réservations</h2>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID / Date</th>
                            <th>Bien & Localisation</th>
                            <th>Client & Propriétaire</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['reservations'])): ?>
                            <tr><td colspan="5" style="text-align: center; padding: 30px;">Aucune réservation.</td></tr>
                        <?php else: foreach($data['reservations'] as $res) : ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600;">#<?= $res->id; ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);"><?= date('d/m/Y', strtotime($res->created_at)); ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($res->title); ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);"><?= date('d/m', strtotime($res->check_in)); ?> - <?= date('d/m/Y', strtotime($res->check_out)); ?> (<?= $res->nights; ?> nuits)</div>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-suitcase-rolling text-muted"></i> <?= htmlspecialchars($res->client_fn . ' ' . $res->client_ln); ?></div>
                                <div><i class="fa-solid fa-house-chimney-user text-muted"></i> <?= htmlspecialchars($res->owner_fn . ' ' . $res->owner_ln); ?></div>
                            </td>
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
