<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80') center/cover; height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; color: white;">
    <div style="max-width: 800px; padding: 20px;">
        <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Trouvez votre prochain lieu de vie</h1>
        <p style="font-size: 1.2rem; margin-bottom: 40px; text-shadow: 0 2px 5px rgba(0,0,0,0.3);">Des milliers de résidences, appartements et villas pour vos séjours ou pour la vie.</p>
        
        <form action="<?= URLROOT; ?>/residences" method="GET" style="background: white; padding: 10px; border-radius: 50px; display: flex; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div style="flex: 1; padding: 0 20px; text-align: left; border-right: 1px solid var(--border);">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-main); margin-bottom: 5px;">Où allez-vous ?</label>
                <input type="text" name="city" placeholder="Rechercher une destination" style="width: 100%; border: none; outline: none; background: transparent; color: var(--text-main); font-size: 1rem;">
            </div>
            <div style="flex: 1; padding: 0 20px; text-align: left;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-main); margin-bottom: 5px;">Type</label>
                <select name="type" style="width: 100%; border: none; outline: none; background: transparent; color: var(--text-main); font-size: 1rem;">
                    <option value="">Tous</option>
                    <option value="reservation">Réservation</option>
                    <option value="rental">Location au mois</option>
                    <option value="sale">Achat</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 0 30px;"><i class="fa-solid fa-magnifying-glass"></i> Rechercher</button>
        </form>
    </div>
</section>

<!-- Categories -->
<section style="padding: 60px 0; background: var(--bg-light);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-size: 2rem; text-align: center; margin-bottom: 40px;">Explorez par catégorie</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            <?php foreach($data['categories'] as $cat): 
                $icon = 'fa-solid fa-building';
                $n = strtolower($cat->name);
                if(strpos($n, 'maison') !== false) $icon = 'fa-solid fa-house';
                elseif(strpos($n, 'résidence') !== false || strpos($n, 'residence') !== false) $icon = 'fa-solid fa-hotel';
                elseif(strpos($n, 'studio') !== false) $icon = 'fa-solid fa-bed';
                elseif(strpos($n, 'villa') !== false) $icon = 'fa-solid fa-crown';
            ?>
                <a href="<?= URLROOT; ?>/residences?category=<?= $cat->id; ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: var(--white); padding: 30px 20px; border-radius: var(--radius-md); text-decoration: none; color: var(--text-main); transition: var(--transition); border: 1px solid var(--border);" class="category-card hover-lift">
                    <i class="<?= $icon; ?>" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 15px;"></i>
                    <span style="font-weight: 600; text-align: center;"><?= htmlspecialchars($cat->name); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section style="padding: 80px 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
            <div>
                <h2 style="font-size: 2.2rem; margin-bottom: 10px;">Biens à la une</h2>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Les résidences les plus appréciées par notre communauté.</p>
            </div>
            <a href="<?= URLROOT; ?>/residences" class="btn btn-outline">Voir tout <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="property-grid">
            <?php foreach($data['featured'] as $prop): ?>
                <div class="property-card" onclick="window.location.href='<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>'">
                    <div class="property-image">
                        <img src="<?= $prop->primary_image ? URLROOT.'/uploads/'.$prop->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=600&q=80'; ?>" alt="<?= htmlspecialchars($prop->title); ?>">
                        
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
    </div>
</section>

<!-- Call to Action -->
<section style="padding: 80px 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 20px;">
        <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 20px;">Devenez propriétaire partenaire</h2>
        <p style="font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9;">Rentabilisez vos biens immobiliers en les mettant en location ou en vente sur la plateforme numéro 1 en Côte d'Ivoire.</p>
        <a href="<?= URLROOT; ?>/auth/register" class="btn btn-lg" style="background: white; color: var(--primary); font-weight: 700; padding: 15px 40px;">Commencer maintenant</a>
    </div>
</section>

<style>
.category-card:hover { border-color: var(--primary); background: rgba(255,56,92,0.05); }
.hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
</style>

<?php require APPROOT . '/views/inc/footer.php'; ?>
