<?php
// app/views/auth/login.php
require APPROOT . '/views/inc/header.php';
?>

<div class="auth-container">
    <div class="auth-card animate-fadeInUp">
        <div class="auth-header">
            <h2>Connexion</h2>
            <p>Bienvenue sur ResiBook</p>
        </div>

        <form action="<?= URLROOT; ?>/auth/login" method="POST">
            <div class="form-group">
                <label for="email">Adresse Email</label>
                <div class="input-icon-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" id="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['email']); ?>" placeholder="votre@email.com" required>
                </div>
                <span class="invalid-feedback"><?= $data['email_err']; ?></span>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-icon-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" placeholder="••••••••" required>
                </div>
                <span class="invalid-feedback"><?= $data['password_err']; ?></span>
            </div>

            <div class="form-options">
                <label class="custom-checkbox">
                    <input type="checkbox" name="remember">
                    <span>Se souvenir de moi</span>
                </label>
                <a href="#" class="forgot-password">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%; margin-top: 15px;">
                Se connecter <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            <p>Vous n'avez pas de compte ? <a href="<?= URLROOT; ?>/auth/register">Inscrivez-vous</a></p>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
