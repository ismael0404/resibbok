<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_client.php'; ?>

    <main class="main-content">
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="font-size: 1.8rem;"><i class="fa-solid fa-lock text-success"></i> Paiement Sécurisé</h2>
                <a href="<?= URLROOT; ?>/client/reservations" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Retour</a>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- Détails Réservation -->
                <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; align-self: start;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Récapitulatif du séjour</h3>
                    
                    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div style="width: 80px; height: 80px; border-radius: var(--radius-sm); background: url('<?= $data['reservation']->primary_image ? URLROOT.'/uploads/'.$data['reservation']->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=150&q=80'; ?>') center/cover;"></div>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 5px;"><?= htmlspecialchars($data['reservation']->title); ?></h4>
                            <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($data['reservation']->city); ?></div>
                        </div>
                    </div>

                    <div style="background: var(--bg-light); border-radius: var(--radius-md); padding: 15px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--text-muted);">Arrivée</span>
                            <strong style="color: var(--text-main);"><?= date('d/m/Y', strtotime($data['reservation']->check_in)); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--text-muted);">Départ</span>
                            <strong style="color: var(--text-main);"><?= date('d/m/Y', strtotime($data['reservation']->check_out)); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Voyageurs</span>
                            <strong style="color: var(--text-main);"><?= $data['reservation']->guests; ?> personne(s)</strong>
                        </div>
                    </div>

                    <div style="border-top: 1px dashed var(--border); padding-top: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span><?= number_format($data['reservation']->price_per_night, 0, ',', ' '); ?> FCFA x <?= $data['reservation']->nights; ?> nuits</span>
                            <span><?= number_format($data['reservation']->subtotal, 0, ',', ' '); ?> FCFA</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Frais de service ResiBook</span>
                            <span><?= number_format($data['reservation']->service_fee, 0, ',', ' '); ?> FCFA</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
                            <span>Total à payer</span>
                            <span style="color: var(--primary);"><?= number_format($data['reservation']->total_price, 0, ',', ' '); ?> FCFA</span>
                        </div>
                    </div>
                </div>

                <!-- Formulaire Paiement -->
                <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 10px;">Méthode de paiement</h3>
                    
                    <form action="<?= URLROOT; ?>/client/payment/<?= $data['reservation']->id; ?>" method="POST" id="payment-form">
                        
                        <!-- Options de paiement simulées -->
                        <div style="display: grid; gap: 15px; margin-bottom: 25px;">
                            <label style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s ease;" class="payment-method-label active">
                                <input type="radio" name="payment_method" value="mobile_money" checked onchange="updatePaymentUI(this)" style="display: none;">
                                <div style="width: 40px; height: 40px; background: rgba(255, 180, 0, 0.1); color: #d39600; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600;">Mobile Money</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Wave, Orange, MTN, Moov</div>
                                </div>
                                <i class="fa-solid fa-circle-check check-icon" style="color: var(--success); display: block;"></i>
                            </label>

                            <label style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid var(--border); border-radius: var(--radius-md); cursor: pointer; transition: all 0.3s ease;" class="payment-method-label">
                                <input type="radio" name="payment_method" value="card" onchange="updatePaymentUI(this)" style="display: none;">
                                <div style="width: 40px; height: 40px; background: rgba(0, 166, 153, 0.1); color: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                    <i class="fa-regular fa-credit-card"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600;">Carte Bancaire</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Visa, Mastercard</div>
                                </div>
                                <i class="fa-solid fa-circle-check check-icon" style="color: var(--success); display: none;"></i>
                            </label>
                        </div>

                        <!-- Champs Mobile Money -->
                        <div id="mobile_money_fields">
                            <div class="form-group">
                                <label>Numéro de téléphone (Mobile Money) *</label>
                                <input type="tel" name="payment_phone" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ? $_SESSION['user_name'] : ''); ?>" placeholder="Ex: 0700000000" id="mm_phone" required>
                            </div>
                        </div>

                        <!-- Champs Carte Bancaire (Simulation visuelle) -->
                        <div id="card_fields" style="display: none;">
                            <div class="form-group">
                                <label>Nom sur la carte *</label>
                                <input type="text" name="payment_name" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']); ?>" id="card_name">
                            </div>
                            <div class="form-group">
                                <label>Numéro de carte</label>
                                <input type="text" class="form-control" placeholder="0000 0000 0000 0000" disabled>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="form-group">
                                    <label>Expiration</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" disabled>
                                </div>
                                <div class="form-group">
                                    <label>CVC</label>
                                    <input type="text" class="form-control" placeholder="123" disabled>
                                </div>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;"><i class="fa-solid fa-shield-halved"></i> Ceci est une simulation. Aucun vrai paiement ne sera débité.</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg" style="width: 100%; margin-top: 20px;">
                            <i class="fa-solid fa-lock"></i> Payer <?= number_format($data['reservation']->total_price, 0, ',', ' '); ?> FCFA
                        </button>
                        
                        <div style="text-align: center; margin-top: 15px; font-size: 0.8rem; color: var(--text-muted);">
                            <i class="fa-solid fa-lock"></i> Paiement sécurisé SSL 256-bit
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.payment-method-label.active { border-color: var(--primary); background: rgba(255,56,92,0.02); }
</style>

<script>
function updatePaymentUI(radio) {
    // Reset styling
    document.querySelectorAll('.payment-method-label').forEach(el => {
        el.classList.remove('active');
        el.querySelector('.check-icon').style.display = 'none';
    });
    
    // Add active styling
    const label = radio.closest('label');
    label.classList.add('active');
    label.querySelector('.check-icon').style.display = 'block';
    
    // Show/hide fields
    if (radio.value === 'mobile_money') {
        document.getElementById('mobile_money_fields').style.display = 'block';
        document.getElementById('mm_phone').setAttribute('required', 'required');
        
        document.getElementById('card_fields').style.display = 'none';
        document.getElementById('card_name').removeAttribute('required');
    } else {
        document.getElementById('mobile_money_fields').style.display = 'none';
        document.getElementById('mm_phone').removeAttribute('required');
        
        document.getElementById('card_fields').style.display = 'block';
        document.getElementById('card_name').setAttribute('required', 'required');
    }
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
