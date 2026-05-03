<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_owner.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Modifier le bien</h2>
            <a href="<?= URLROOT; ?>/owner/residences" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Retour</a>
        </div>

        <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px;">
            <form action="<?= URLROOT; ?>/owner/editResidence/<?= $data['property']->id; ?>" method="POST" enctype="multipart/form-data">
                
                <h3 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Informations Générales</h3>
                
                <div class="form-row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Titre de l'annonce *</label>
                        <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($data['property']->title); ?>">
                    </div>
                    <div class="form-group">
                        <label>Catégorie *</label>
                        <select name="category_id" class="form-control" required>
                            <?php foreach($data['categories'] as $cat): ?>
                                <option value="<?= $cat->id; ?>" <?= $data['property']->category_id == $cat->id ? 'selected' : ''; ?>><?= htmlspecialchars($cat->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($data['property']->description); ?></textarea>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Ville *</label>
                        <input type="text" name="city" class="form-control" required value="<?= htmlspecialchars($data['property']->city); ?>">
                    </div>
                    <div class="form-group">
                        <label>Adresse précise *</label>
                        <input type="text" name="address" class="form-control" required value="<?= htmlspecialchars($data['property']->address); ?>">
                    </div>
                </div>

                <h3 style="font-size: 1.2rem; margin: 30px 0 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Type d'Offre et Tarification</h3>
                
                <div class="form-group">
                    <label>Type de proposition *</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="radio" name="listing_type" value="reservation" <?= $data['property']->listing_type == 'reservation' ? 'checked' : ''; ?> onclick="togglePriceFields()"> Réservation
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="radio" name="listing_type" value="rental" <?= $data['property']->listing_type == 'rental' ? 'checked' : ''; ?> onclick="togglePriceFields()"> Location au mois
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="radio" name="listing_type" value="sale" <?= $data['property']->listing_type == 'sale' ? 'checked' : ''; ?> onclick="togglePriceFields()"> Vente
                        </label>
                    </div>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div class="form-group" id="field_price_per_night">
                        <label>Prix par nuit (FCFA) *</label>
                        <input type="number" name="price_per_night" id="input_price_per_night" class="form-control" value="<?= $data['property']->price_per_night; ?>" min="0">
                    </div>
                    <div class="form-group" id="field_price_monthly">
                        <label>Loyer mensuel (FCFA) *</label>
                        <input type="number" name="price_monthly" id="input_price_monthly" class="form-control" value="<?= $data['property']->price_monthly; ?>" min="0">
                    </div>
                    <div class="form-group" id="field_price_sale">
                        <label>Prix de vente (FCFA) *</label>
                        <input type="number" name="price_sale" id="input_price_sale" class="form-control" value="<?= $data['property']->price_sale; ?>" min="0">
                    </div>
                </div>

                <h3 style="font-size: 1.2rem; margin: 30px 0 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Caractéristiques du bien</h3>
                
                <div class="form-row" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                    <div class="form-group">
                        <label>Capacité (Voyageurs)</label>
                        <input type="number" name="max_guests" class="form-control" value="<?= $data['property']->max_guests; ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label>Chambres</label>
                        <input type="number" name="bedrooms" class="form-control" value="<?= $data['property']->bedrooms; ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label>Salles de bain</label>
                        <input type="number" name="bathrooms" class="form-control" value="<?= $data['property']->bathrooms; ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label>Surface (m²)</label>
                        <input type="number" name="area_sqm" class="form-control" value="<?= $data['property']->area_sqm; ?>" min="0">
                    </div>
                </div>

                <h3 style="font-size: 1.2rem; margin: 30px 0 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Équipements</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <?php foreach($data['amenities'] as $amenity): ?>
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="amenities[]" value="<?= $amenity->id; ?>" <?= in_array($amenity->id, $data['property_amenities']) ? 'checked' : ''; ?>>
                            <i class="<?= $amenity->icon; ?>" style="color: var(--text-muted); width: 20px;"></i> <?= htmlspecialchars($amenity->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <h3 style="font-size: 1.2rem; margin: 30px 0 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Conditions</h3>
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Règlement intérieur</label>
                        <textarea name="rules" class="form-control" rows="3"><?= htmlspecialchars($data['property']->rules); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Politique d'annulation</label>
                        <select name="cancellation_policy" class="form-control">
                            <option value="flexible" <?= $data['property']->cancellation_policy == 'flexible' ? 'selected' : ''; ?>>Flexible</option>
                            <option value="moderate" <?= $data['property']->cancellation_policy == 'moderate' ? 'selected' : ''; ?>>Modérée</option>
                            <option value="strict" <?= $data['property']->cancellation_policy == 'strict' ? 'selected' : ''; ?>>Stricte</option>
                        </select>
                    </div>
                </div>

                <h3 style="font-size: 1.2rem; margin: 30px 0 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Photos</h3>
                
                <div style="display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;">
                    <?php foreach($data['images'] as $img): ?>
                        <div style="width: 100px; height: 80px; border-radius: var(--radius-sm); overflow: hidden; position: relative;">
                            <img src="<?= URLROOT . '/uploads/' . $img->image_path; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php if($img->is_primary): ?>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255,56,92,0.8); color: white; font-size: 0.6rem; text-align: center; padding: 2px;">Principale</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group">
                    <label>Ajouter des photos supplémentaires</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp" style="padding: 10px;">
                </div>

                <div style="margin-top: 40px; border-top: 1px solid var(--border); padding-top: 20px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-save"></i> Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function togglePriceFields() {
    const type = document.querySelector('input[name="listing_type"]:checked').value;
    
    document.getElementById('field_price_per_night').style.display = 'none';
    document.getElementById('field_price_monthly').style.display = 'none';
    document.getElementById('field_price_sale').style.display = 'none';
    
    if (type === 'reservation') {
        document.getElementById('field_price_per_night').style.display = 'block';
    } else if (type === 'rental') {
        document.getElementById('field_price_monthly').style.display = 'block';
    } else if (type === 'sale') {
        document.getElementById('field_price_sale').style.display = 'block';
    }
}

// Initial call
togglePriceFields();
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
