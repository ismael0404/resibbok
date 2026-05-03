<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_client.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mes Réservations & Voyages</h2>
            <a href="<?= URLROOT; ?>/residences" class="btn btn-outline"><i class="fa-solid fa-magnifying-glass"></i> Trouver un bien</a>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th>Localisation</th>
                            <th>Dates / Séjour</th>
                            <th>Montant Total</th>
                            <th>Statut</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['reservations'])): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fa-solid fa-plane-slash" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                                    Vous n'avez aucune réservation pour le moment.
                                </td>
                            </tr>
                        <?php else: foreach($data['reservations'] as $res) : ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); background: url('<?= $res->primary_image ? URLROOT.'/uploads/'.$res->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=100&q=80'; ?>') center/cover;"></div>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($res->title); ?></div>
                                </div>
                            </td>
                            <td><i class="fa-solid fa-location-dot text-muted"></i> <?= htmlspecialchars($res->city); ?></td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($res->check_in)); ?> <i class="fa-solid fa-arrow-right text-muted" style="font-size: 0.7rem; margin: 0 5px;"></i> <?= date('d/m/Y', strtotime($res->check_out)); ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;"><?= $res->nights; ?> nuit(s) - <?= $res->guests; ?> pers.</div>
                            </td>
                            <td style="font-weight: 600;"><?= number_format($res->total_price, 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <?php if($res->status == 'pending'): ?><span class="badge" style="background: rgba(113, 113, 113, 0.1); color: var(--text-muted);">En attente de validation</span>
                                <?php elseif($res->status == 'approved'): ?><span class="badge badge-pending">En attente de paiement</span>
                                <?php elseif($res->status == 'paid' || $res->status == 'confirmed'): ?><span class="badge" style="background: rgba(0, 166, 153, 0.15); color: var(--secondary);">Payée / Confirmée</span>
                                <?php elseif($res->status == 'completed'): ?><span class="badge badge-success">Terminée</span>
                                <?php else: ?><span class="badge badge-danger">Annulée</span><?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <?php if($res->status == 'approved'): ?>
                                        <a href="<?= URLROOT; ?>/client/payment/<?= $res->id; ?>" class="btn btn-sm" style="background: rgba(255, 180, 0, 0.1); color: var(--warning); border: 1px solid rgba(255, 180, 0, 0.3);">
                                            <i class="fa-solid fa-credit-card"></i> Payer
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if(in_array($res->status, ['pending', 'approved'])): ?>
                                        <form action="<?= URLROOT; ?>/client/cancelReservation/<?= $res->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: var(--danger);" title="Annuler">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?= URLROOT; ?>/residences/show/<?= $res->property_id; ?>" class="btn btn-sm btn-outline" title="Voir l'annonce"><i class="fa-solid fa-eye"></i></a>
                                    <?php endif; ?>
                                </div>
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
