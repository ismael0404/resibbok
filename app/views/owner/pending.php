<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_owner.php'; ?>

    <main class="main-content" style="display: flex; justify-content: center; align-items: center; min-height: 70vh;">
        <div style="max-width: 600px; text-align: center; padding: 40px; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div style="width: 80px; height: 80px; background: rgba(255, 180, 0, 0.1); color: var(--warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            
            <h2 style="font-size: 1.8rem; margin-bottom: 15px;">Compte en attente de validation</h2>
            
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px;">
                Bonjour <strong><?= htmlspecialchars($_SESSION['user_name']); ?></strong>,<br>
                Votre compte propriétaire a été créé avec succès, mais il est actuellement en cours de vérification par notre équipe d'administration.
            </p>
            
            <div style="background: rgba(0, 166, 153, 0.05); border: 1px solid rgba(0, 166, 153, 0.2); border-radius: var(--radius-md); padding: 20px; margin-bottom: 30px; text-align: left;">
                <h4 style="color: var(--secondary); margin-bottom: 10px; font-size: 1rem;"><i class="fa-solid fa-circle-info"></i> Pourquoi cette étape ?</h4>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">Afin de garantir la qualité et la sécurité de notre plateforme, nous vérifions manuellement chaque nouveau profil propriétaire avant de l'autoriser à publier des biens.</p>
            </div>
            
            <p style="color: var(--text-muted); font-size: 0.9rem;">Vous recevrez un email ou une notification dès que votre compte sera activé (généralement sous 24 à 48 heures).</p>
            
            <div style="margin-top: 30px;">
                <a href="<?= URLROOT; ?>/client/profile" class="btn btn-outline">Basculer en vue voyageur</a>
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
