<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_client.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Paramètres du profil</h2>
        </div>
        
        <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px;">
            <form action="<?= URLROOT; ?>/client/settings" method="POST" enctype="multipart/form-data">
                
                <h3 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Informations Personnelles</h3>
                
                <div style="display: flex; gap: 30px; margin-bottom: 30px; align-items: center;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--primary); font-weight: bold; overflow: hidden;">
                        <?php if($data['user']->avatar): ?>
                            <img src="<?= URLROOT.'/uploads/'.$data['user']->avatar; ?>" style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <?= substr($data['user']->first_name, 0, 1) . substr($data['user']->last_name, 0, 1); ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="btn btn-outline btn-sm" style="cursor: pointer;">
                            <i class="fa-solid fa-camera"></i> Changer la photo
                            <input type="file" name="avatar" style="display: none;" accept="image/jpeg,image/png,image/webp">
                        </label>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">Format JPEG, PNG ou WEBP. Max 2Mo.</p>
                    </div>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Prénom *</label>
                        <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($data['user']->first_name); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($data['user']->last_name); ?>">
                    </div>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Adresse Email (Non modifiable)</label>
                        <input type="email" class="form-control" disabled value="<?= htmlspecialchars($data['user']->email); ?>">
                    </div>
                    <div class="form-group">
                        <label>Téléphone *</label>
                        <input type="tel" name="phone" class="form-control" required value="<?= htmlspecialchars($data['user']->phone); ?>">
                    </div>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                    <div class="form-group">
                        <label>Ville</label>
                        <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($data['user']->city); ?>">
                    </div>
                    <div class="form-group">
                        <label>Adresse postale</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($data['user']->address); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>À propos de moi (Bio)</label>
                    <textarea name="bio" class="form-control" rows="4" placeholder="Décrivez-vous en quelques mots..."><?= htmlspecialchars($data['user']->bio); ?></textarea>
                </div>

                <div style="margin-top: 40px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-save"></i> Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
