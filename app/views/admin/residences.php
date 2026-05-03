<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_admin.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Gestion des Biens Immobiliers</h2>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Titre & Localisation</th>
                            <th>Propriétaire</th>
                            <th>Type / Prix</th>
                            <th>Statut</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['properties'])): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 30px;">Aucun bien trouvé.</td></tr>
                        <?php else: foreach($data['properties'] as $prop) : ?>
                        <tr>
                            <td>
                                <div style="width: 60px; height: 60px; border-radius: var(--radius-sm); background: url('<?= $prop->primary_image ? URLROOT.'/uploads/'.$prop->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=100&q=80'; ?>') center/cover;"></div>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($prop->title); ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($prop->city); ?> - <?= htmlspecialchars($prop->category_name); ?></div>
                            </td>
                            <td><?= htmlspecialchars($prop->first_name . ' ' . $prop->last_name); ?></td>
                            <td>
                                <div>
                                    <?php if($prop->listing_type == 'reservation'): ?><span class="badge" style="background: rgba(255,56,92,0.1); color: var(--primary);">Réservation</span>
                                    <?php elseif($prop->listing_type == 'rental'): ?><span class="badge" style="background: rgba(0,166,153,0.1); color: var(--secondary);">Location</span>
                                    <?php else: ?><span class="badge" style="background: rgba(0,138,5,0.1); color: var(--success);">Vente</span><?php endif; ?>
                                </div>
                                <div style="font-weight: 600; margin-top: 5px;">
                                    <?php 
                                        if($prop->listing_type == 'reservation') echo number_format($prop->price_per_night, 0, ',', ' ') . ' <small>FCFA/n</small>';
                                        elseif($prop->listing_type == 'rental') echo number_format($prop->price_monthly, 0, ',', ' ') . ' <small>FCFA/m</small>';
                                        else echo number_format($prop->price_sale, 0, ',', ' ') . ' <small>FCFA</small>';
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php if($prop->status == 'active'): ?><span class="badge badge-success">Actif</span>
                                <?php elseif($prop->status == 'inactive'): ?><span class="badge badge-danger">Inactif</span>
                                <?php else: ?><span class="badge badge-pending"><?= ucfirst($prop->status); ?></span><?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <a href="<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>" target="_blank" class="btn btn-sm btn-outline" title="Voir"><i class="fa-solid fa-eye"></i></a>
                                    
                                    <?php if($prop->status == 'active'): ?>
                                        <form action="<?= URLROOT; ?>/admin/deactivateProperty/<?= $prop->id; ?>" method="POST" style="display:inline;">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: var(--warning); border-color: var(--warning);" title="Désactiver"><i class="fa-solid fa-power-off"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= URLROOT; ?>/admin/activateProperty/<?= $prop->id; ?>" method="POST" style="display:inline;">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: var(--success); border-color: var(--success);" title="Activer"><i class="fa-solid fa-check"></i></button>
                                        </form>
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
