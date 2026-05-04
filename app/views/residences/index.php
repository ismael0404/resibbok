<?php require APPROOT . '/views/inc/header.php'; 

?>

<div style="background: var(--bg-light); border-bottom: 1px solid var(--border); padding: 20px 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <form action="<?= URLROOT; ?>/residences" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px;">Destination / Mot-clé</label>
                <div class="input-icon-wrapper">
                    <i class="fa-solid fa-location-dot"></i>
                    <input type="text" name="search" class="form-control" placeholder="Abidjan, Cocody..." value="<?= htmlspecialchars($data['filters']['search']); ?>">
                </div>
            </div>
            
            <div style="width: 180px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px;">Type d'offre</label>
                <select name="type" class="form-control">
                    <option value="">Tous les types</option>
                    <option value="reservation" <?= ($data['filters']['type']=='reservation')?'selected':''; ?>>Réservation (Nuit)</option>
                    <option value="rental" <?= ($data['filters']['type']=='rental')?'selected':''; ?>>Location (Mois)</option>
                    <option value="sale" <?= ($data['filters']['type']=='sale')?'selected':''; ?>>Achat / Vente</option>
                </select>
            </div>

            <div style="width: 180px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px;">Catégorie</label>
                <select name="category" class="form-control">
                    <option value="">Toutes</option>
                    <?php foreach($data['categories'] as $cat): ?>
                        <option value="<?= $cat->id; ?>" <?= ($data['filters']['category']==$cat->id)?'selected':''; ?>><?= htmlspecialchars($cat->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="width: 120px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px;">Prix max</label>
                <input type="number" name="max_price" class="form-control" placeholder="Max FCFA" value="<?= htmlspecialchars($data['filters']['max_price']); ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 30px;"><i class="fa-solid fa-filter"></i> Filtrer</button>
            <a href="<?= URLROOT; ?>/residences" class="btn btn-outline" style="height: 48px; padding: 0 20px;" title="Réinitialiser"><i class="fa-solid fa-rotate-right"></i></a>
        </form>
    </div>
</div>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 1.8rem;">Résultats de la recherche</h1>
        <span style="color: var(--text-muted); font-weight: 500;"><?= count($data['properties']); ?> bien(s) trouvé(s)</span>
    </div>

    <?php if(empty($data['properties'])): ?>
        <div style="text-align: center; padding: 80px 20px; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <i class="fa-solid fa-building-circle-xmark" style="font-size: 4rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 20px;"></i>
            <h3 style="font-size: 1.5rem; margin-bottom: 10px;">Aucun bien ne correspond à vos critères</h3>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Essayez de modifier vos filtres ou d'élargir votre zone de recherche.</p>
            <a href="<?= URLROOT; ?>/residences" class="btn btn-outline">Effacer les filtres</a>
        </div>
    <?php else: ?>
        <div class="property-grid">
            <?php foreach($data['properties'] as $prop): ?>
                <div class="property-card" onclick="window.location.href='<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>'">
                    <div class="property-image">
                        <img src="<?= getDynamicPropertyImages($prop, 1)[0]; ?>" alt="<?= htmlspecialchars($prop->title); ?>">
                        
                        <?php if($prop->listing_type == 'reservation'): ?>
                            <span class="property-badge" style="background: var(--primary);">Réservation</span>
                        <?php elseif($prop->listing_type == 'rental'): ?>
                            <span class="property-badge" style="background: var(--secondary);">Location</span>
                        <?php else: ?>
                            <span class="property-badge" style="background: var(--success);">Vente</span>
                        <?php endif; ?>
                    </div>
                    <div class="property-content">
                        <div class="property-location">
                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($prop->city); ?> - <?= htmlspecialchars($prop->category_name); ?>
                            
                            <?php if($prop->status == 'active'): ?>
                                <span style="margin-left:auto; color:var(--success); font-weight:700;">Disponible</span>
                            <?php elseif($prop->status == 'reserved'): ?>
                                <span style="margin-left:auto; color:var(--warning); font-weight:700;">Réservé</span>
                            <?php elseif($prop->status == 'rented'): ?>
                                <span style="margin-left:auto; color:var(--danger); font-weight:700;">Loué</span>
                            <?php elseif($prop->status == 'sold'): ?>
                                <span style="margin-left:auto; color:var(--danger); font-weight:700;">Vendu</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="property-title">
                            <a href="<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>"><?= htmlspecialchars($prop->title); ?></a>
                        </h3>
                        
                        <div class="property-features">
                            <span><i class="fa-solid fa-user-group"></i> <?= $prop->max_guests; ?></span>
                            <span><i class="fa-solid fa-bed"></i> <?= $prop->bedrooms; ?></span>
                            <span><i class="fa-solid fa-bath"></i> <?= $prop->bathrooms; ?></span>
                            <span><i class="fa-solid fa-ruler-combined"></i> <?= $prop->area_sqm; ?> m²</span>
                        </div>

                        <div class="property-footer">
                            <div class="property-price">
                                <?php 
                                    if($prop->listing_type == 'reservation') echo number_format($prop->price_per_night, 0, ',', ' ') . ' <span>FCFA /nuit</span>';
                                    elseif($prop->listing_type == 'rental') echo number_format($prop->price_monthly, 0, ',', ' ') . ' <span>FCFA /mois</span>';
                                    else echo number_format($prop->price_sale, 0, ',', ' ') . ' <span>FCFA</span>';
                                ?>
                            </div>
                            <?php if($prop->score > 0): ?>
                                <div class="property-rating">
                                    <i class="fa-solid fa-star"></i> <?= number_format($prop->score, 1); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic CTA Button -->
                        <button class="btn btn-primary property-cta-btn">
                            <?php 
                                if($prop->listing_type == 'reservation') echo 'Réserver';
                                elseif($prop->listing_type == 'rental') echo 'Louer maintenant';
                                else echo 'Acheter';
                            ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
