</main>

<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <h3 class="footer-title"><i class="fa-solid fa-house-chimney"></i> ResiBook</h3>
            <p class="footer-desc">La plateforme immobilière de référence en Côte d'Ivoire. Réservation, location et vente de biens.</p>
        </div>
        <div>
            <h4>Navigation</h4>
            <ul>
                <li><a href="<?= URLROOT; ?>">Accueil</a></li>
                <li><a href="<?= URLROOT; ?>/residences">Explorer</a></li>
                <li><a href="<?= URLROOT; ?>/residences?type=reservation">Réservation</a></li>
                <li><a href="<?= URLROOT; ?>/residences?type=rental">Location</a></li>
                <li><a href="<?= URLROOT; ?>/residences?type=sale">Vente</a></li>
            </ul>
        </div>
        <div>
            <h4>Propriétaires</h4>
            <ul>
                <li><a href="<?= URLROOT; ?>/auth/register">Mettre un bien</a></li>
                <li><a href="#">Ressources</a></li>
                <li><a href="#">Communauté</a></li>
            </ul>
        </div>
        <div>
            <h4>Contact</h4>
            <ul>
                <li><i class="fa-solid fa-envelope"></i> contact@resibook.com</li>
                <li><i class="fa-solid fa-phone"></i> +225 07 00 00 00 00</li>
                <li><i class="fa-solid fa-location-dot"></i> Abidjan, Côte d'Ivoire</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y'); ?> ResiBook. Tous droits réservés.</p>
        <button onclick="darkModeToggle()" class="dark-mode-btn">
            <i class="fa-solid fa-moon"></i> Mode sombre
        </button>
    </div>
</footer>

<script>window.URLROOT = '<?= URLROOT; ?>';</script>
<script src="<?= URLROOT; ?>/public/js/app.js"></script>

<script>
<?php if(isset($_SESSION['flash_msg'])): ?>
    Swal.fire({icon:'success',title:'Succès !',text:'<?= addslashes(htmlspecialchars($_SESSION['flash_msg'])); ?>',confirmButtonColor:'#FF385C',timer:4000,timerProgressBar:true});
    <?php unset($_SESSION['flash_msg']); ?>
<?php endif; ?>
<?php if(isset($_SESSION['flash_err'])): ?>
    Swal.fire({icon:'error',title:'Erreur',text:'<?= addslashes(htmlspecialchars($_SESSION['flash_err'])); ?>',confirmButtonColor:'#FF385C'});
    <?php unset($_SESSION['flash_err']); ?>
<?php endif; ?>
</script>
</body>
</html>
