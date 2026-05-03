<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_admin.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Gestion des Propriétaires</h2>
        </div>

        <div class="table-container" style="margin-bottom: 40px;">
            <div style="padding: 20px; border-bottom: 1px solid var(--border); background: rgba(255,180,0,0.05);">
                <h3 style="font-size: 1.2rem; font-weight: 600; color: var(--warning);"><i class="fa-solid fa-clock"></i> En attente de validation (<?= count($data['pending_owners']); ?>)</h3>
            </div>
            
            <?php if(empty($data['pending_owners'])) : ?>
                <div style="padding: 30px; text-align: center; color: var(--text-muted);">Aucun propriétaire en attente.</div>
            <?php else : ?>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>Ville</th>
                            <th>Inscription</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['pending_owners'] as $owner) : ?>
                        <tr>
                            <td style="font-weight: 600;"><?= htmlspecialchars($owner->first_name . ' ' . $owner->last_name); ?></td>
                            <td><?= htmlspecialchars($owner->email); ?><br><small><?= htmlspecialchars($owner->phone); ?></small></td>
                            <td><?= htmlspecialchars($owner->city); ?></td>
                            <td><?= date('d/m/Y', strtotime($owner->created_at)); ?></td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <form action="<?= URLROOT; ?>/admin/approveOwner/<?= $owner->id; ?>" method="POST">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: var(--success); border-color: var(--success);"><i class="fa-solid fa-check"></i> Valider</button>
                                    </form>
                                    <form action="<?= URLROOT; ?>/admin/rejectOwner/<?= $owner->id; ?>" method="POST" onsubmit="return confirm('Refuser ce compte ?');">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: var(--danger);"><i class="fa-solid fa-xmark"></i></button>
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

        <div class="table-container">
            <div style="padding: 20px; border-bottom: 1px solid var(--border);">
                <h3 style="font-size: 1.2rem; font-weight: 600;"><i class="fa-solid fa-users"></i> Tous les propriétaires</h3>
            </div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Contact</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['all_owners'] as $owner) : ?>
                        <tr>
                            <td>#<?= $owner->id; ?></td>
                            <td style="font-weight: 600;">
                                <?= htmlspecialchars($owner->first_name . ' ' . $owner->last_name); ?>
                                <?php if($owner->is_verified): ?> <i class="fa-solid fa-circle-check text-secondary" title="Vérifié"></i> <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($owner->email); ?><br><small><?= htmlspecialchars($owner->phone); ?></small></td>
                            <td>
                                <?php if($owner->status == 'active'): ?><span class="badge badge-success">Actif</span>
                                <?php elseif($owner->status == 'pending'): ?><span class="badge badge-pending">En attente</span>
                                <?php elseif($owner->status == 'suspended'): ?><span class="badge badge-warning">Suspendu</span>
                                <?php else: ?><span class="badge badge-danger">Rejeté</span><?php endif; ?>
                            </td>
                            <td>
                                <?php if($owner->status == 'active'): ?>
                                    <form action="<?= URLROOT; ?>/admin/suspendUser/<?= $owner->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Suspendre ce compte ?');">
                                        <button type="submit" class="btn btn-sm btn-outline" title="Suspendre"><i class="fa-solid fa-ban"></i></button>
                                    </form>
                                <?php elseif($owner->status == 'suspended' || $owner->status == 'rejected'): ?>
                                    <form action="<?= URLROOT; ?>/admin/activateUser/<?= $owner->id; ?>" method="POST" style="display:inline;">
                                        <button type="submit" class="btn btn-sm btn-outline" title="Activer"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
