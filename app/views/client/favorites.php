<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_client.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mes Favoris</h2>
            <span style="color: var(--text-muted);"><?= count($data['favorites']); ?> bien(s) sauvegardé(s)</span>
        </div>

        <?php if(empty($data['favorites'])): ?>
            <div style="text-align: center; padding: 60px 20px; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border);">
                <i class="fa-regular fa-heart" style="font-size: 4rem; color: var(--text-muted); opacity: 0.3; margin-bottom: 20px;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 10px;">Votre liste de favoris est vide</h3>
                <p style="color: var(--text-muted); margin-bottom: 30px;">Parcourez nos annonces et cliquez sur l'icône cœur pour sauvegarder les biens qui vous plaisent.</p>
                <a href="<?= URLROOT; ?>/residences" class="btn btn-primary">Explorer les biens</a>
            </div>
        <?php else: ?>
            <div class="property-grid">
                <?php foreach($data['favorites'] as $prop): ?>
                    <div class="property-card" id="fav-card-<?= $prop->id; ?>" onclick="if(event.target.closest('.favorite-btn')) return; window.location.href='<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>'">
                        <div class="property-image">
                            <img src="<?= $prop->primary_image ? URLROOT.'/uploads/'.$prop->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=600&q=80'; ?>" alt="<?= htmlspecialchars($prop->title); ?>">
                            
                            <button class="favorite-btn active" onclick="toggleFavorite(<?= $prop->id; ?>)" style="color: var(--primary);">
                                <i class="fa-solid fa-heart"></i>
                            </button>

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
                                
                                <!-- Dynamic Status -->
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
    </main>
</div>

<script>
function toggleFavorite(id) {
    fetch(window.URLROOT + '/api/toggleFavorite', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: id })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success && data.action === 'removed') {
            document.getElementById('fav-card-' + id).remove();
            
            // Check if empty
            if(document.querySelectorAll('.property-card').length === 0) {
                location.reload();
            }
        }
    });
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
