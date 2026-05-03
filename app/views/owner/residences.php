<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_owner.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mes Biens Immobiliers</h2>
            <a href="<?= URLROOT; ?>/owner/addResidence" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ajouter un bien</a>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Titre & Localisation</th>
                            <th>Type / Prix</th>
                            <th>Stats</th>
                            <th>Statut</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['properties'])): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 30px;">Vous n'avez pas encore ajouté de biens.</td></tr>
                        <?php else: foreach($data['properties'] as $prop) : ?>
                        <tr>
                            <td>
                                <div style="width: 70px; height: 50px; border-radius: var(--radius-sm); background: url('<?= $prop->primary_image ? URLROOT.'/uploads/'.$prop->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=150&q=80'; ?>') center/cover;"></div>
                            </td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($prop->title); ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($prop->city); ?> - <?= htmlspecialchars($prop->category_name); ?></div>
                            </td>
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
                                <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-eye"></i> <?= $prop->views_count; ?> vues</div>
                                <?php if($prop->score > 0): ?>
                                    <div style="font-size: 0.85rem; color: #ffb400; margin-top: 5px;"><i class="fa-solid fa-star"></i> <?= number_format($prop->score, 1); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($prop->status == 'active'): ?><span class="badge badge-success">Actif (Disponible)</span>
                                <?php elseif($prop->status == 'inactive'): ?><span class="badge badge-danger">Inactif</span>
                                <?php elseif($prop->status == 'reserved'): ?><span class="badge" style="background: rgba(255,180,0,0.1); color: #FFB400;">Réservé</span>
                                <?php elseif($prop->status == 'rented'): ?><span class="badge" style="background: rgba(225,44,50,0.1); color: #E12C32;">Loué</span>
                                <?php elseif($prop->status == 'sold'): ?><span class="badge" style="background: rgba(225,44,50,0.1); color: #E12C32;">Vendu</span>
                                <?php else: ?><span class="badge badge-pending"><?= ucfirst($prop->status); ?></span><?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <a href="<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>" target="_blank" class="btn btn-sm btn-outline" title="Voir public"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    <a href="<?= URLROOT; ?>/owner/editResidence/<?= $prop->id; ?>" class="btn btn-sm btn-outline" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                                    <form action="<?= URLROOT; ?>/owner/deleteResidence/<?= $prop->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce bien ?');">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: var(--danger);" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
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
