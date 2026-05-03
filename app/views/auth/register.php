<?php
// app/views/auth/register.php
require APPROOT . '/views/inc/header.php';
?>

<div class="auth-container">
    <div class="auth-card animate-fadeInUp">
        <div class="auth-header">
            <h2>Inscription</h2>
            <p>Rejoignez ResiBook aujourd'hui</p>
        </div>

        <form action="<?= URLROOT; ?>/auth/register" method="POST">
            <div class="form-group role-selector">
                <label class="role-option">
                    <input type="radio" name="role_id" value="client" checked>
                    <div class="role-box">
                        <i class="fa-solid fa-suitcase-rolling"></i>
                        <span>Client / Voyageur</span>
                    </div>
                </label>
                <label class="role-option">
                    <input type="radio" name="role_id" value="owner">
                    <div class="role-box">
                        <i class="fa-solid fa-house-chimney-user"></i>
                        <span>Propriétaire</span>
                    </div>
                </label>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="first_name" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="last_name" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['last_name']); ?>" required>
                </div>
            </div>
            <?php if(!empty($data['name_err'])): ?><span class="invalid-feedback"><?= $data['name_err']; ?></span><?php endif; ?>

            <div class="form-group">
                <label>Adresse Email *</label>
                <input type="email" name="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['email']); ?>" required>
                <span class="invalid-feedback"><?= $data['email_err']; ?></span>
            </div>

            <div class="form-group">
                <label>Téléphone *</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($data['phone']); ?>" required>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" name="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" required>
                    <span class="invalid-feedback"><?= $data['password_err']; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" name="confirm_password" class="form-control <?= (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" required>
                    <span class="invalid-feedback"><?= $data['confirm_password_err']; ?></span>
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label class="custom-checkbox">
                    <input type="checkbox" required>
                    <span style="font-size: 0.9rem;">J'accepte les conditions d'utilisation et la politique de confidentialité de ResiBook.</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%; margin-top: 15px;">
                S'inscrire <i class="fa-solid fa-user-plus"></i>
            </button>
        </form>

        <div class="auth-footer">
            <p>Déjà un compte ? <a href="<?= URLROOT; ?>/auth/login">Connectez-vous</a></p>
        </div>
    </div>
</div>

<style>
.role-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
.role-option input { display: none; }
.role-box { border: 2px solid var(--border); border-radius: var(--radius-md); padding: 15px; text-align: center; cursor: pointer; transition: var(--transition); color: var(--text-muted); }
.role-box i { font-size: 2rem; margin-bottom: 10px; display: block; }
.role-option input:checked + .role-box { border-color: var(--primary); background: rgba(255,56,92,0.05); color: var(--primary); }
</style>

<?php require APPROOT . '/views/inc/footer.php'; ?>
