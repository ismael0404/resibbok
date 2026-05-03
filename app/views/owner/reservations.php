<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_owner.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mes Réservations</h2>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Bien</th>
                            <th>Client & Contact</th>
                            <th>Dates / Nuits</th>
                            <th>Revenu Net</th>
                            <th>Statut</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['reservations'])): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 30px;">Aucune réservation trouvée.</td></tr>
                        <?php else: foreach($data['reservations'] as $res) : ?>
                        <tr>
                            <td style="font-weight: 600;">
                                #<?= $res->id; ?>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: normal; margin-top: 5px;"><?= date('d/m/Y', strtotime($res->created_at)); ?></div>
                            </td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($res->title); ?></td>
                            <td>
                                <div><i class="fa-solid fa-user text-muted" style="width: 15px;"></i> <?= htmlspecialchars($res->first_name . ' ' . $res->last_name); ?></div>
                                <div style="margin-top: 5px; font-size: 0.85rem;"><i class="fa-solid fa-phone text-muted" style="width: 15px;"></i> <?= htmlspecialchars($res->phone); ?></div>
                            </td>
                            <td>
                                <div><?= date('d/m', strtotime($res->check_in)); ?> <i class="fa-solid fa-arrow-right text-muted" style="font-size: 0.7rem; margin: 0 5px;"></i> <?= date('d/m/Y', strtotime($res->check_out)); ?></div>
                                <div style="margin-top: 5px; font-size: 0.85rem; color: var(--text-muted);"><?= $res->nights; ?> nuit(s) - <?= $res->guests; ?> pers.</div>
                            </td>
                            <td style="font-weight: 600; color: var(--secondary);">
                                <?= number_format($res->subtotal, 0, ',', ' '); ?> FCFA
                            </td>
                            <td>
                                <?php if($res->status == 'pending'): ?><span class="badge" style="background: rgba(255, 180, 0, 0.1); color: var(--warning);">En attente de validation</span>
                                <?php elseif($res->status == 'approved'): ?><span class="badge" style="background: rgba(0, 138, 5, 0.1); color: var(--success);">Validée (Attente paiement)</span>
                                <?php elseif($res->status == 'paid' || $res->status == 'confirmed'): ?><span class="badge" style="background: rgba(0, 166, 153, 0.15); color: var(--secondary);">Payée / Confirmée</span>
                                <?php elseif($res->status == 'completed'): ?><span class="badge badge-success">Terminée</span>
                                <?php else: ?><span class="badge badge-danger">Annulée/Refusée</span><?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if($res->status == 'pending'): ?>
                                    <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                        <form action="<?= URLROOT; ?>/owner/acceptReservation/<?= $res->id; ?>" method="POST" style="display:inline;">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: var(--success); border-color: var(--success);" title="Accepter"><i class="fa-solid fa-check"></i></button>
                                        </form>
                                        <form action="<?= URLROOT; ?>/owner/rejectReservation/<?= $res->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Refuser cette réservation ?');">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: var(--danger);" title="Refuser"><i class="fa-solid fa-xmark"></i></button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">Aucune action</span>
                                <?php endif; ?>
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
