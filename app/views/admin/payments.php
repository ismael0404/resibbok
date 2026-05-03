<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_admin.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Paiements & Commissions</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
                <h3 style="font-size: 1rem; font-weight: 500; margin-bottom: 10px; opacity: 0.9;">Total Volume Traité</h3>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= number_format($data['stats']['total_revenue'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; opacity: 0.8;"><?= $data['stats']['total_payments']; ?> transactions réussies</div>
            </div>
            
            <div class="stat-card">
                <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500; margin-bottom: 10px;">Commissions ResiBook</h3>
                <p style="font-size: 1.8rem; font-weight: 700; color: var(--success);"><?= number_format($data['stats']['total_commission'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Marge de la plateforme (<?= COMMISSION_RATE; ?>%)</div>
            </div>

            <div class="stat-card">
                <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500; margin-bottom: 10px;">Reversé aux Propriétaires</h3>
                <p style="font-size: 1.8rem; font-weight: 700; color: var(--secondary);"><?= number_format($data['stats']['total_owner'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Montant net envoyé aux bailleurs</div>
            </div>
        </div>

        <div class="table-container">
            <div style="padding: 20px; border-bottom: 1px solid var(--border);">
                <h3 style="font-size: 1.2rem; font-weight: 600;">Historique des Transactions</h3>
            </div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Client & Bien</th>
                            <th>Méthode</th>
                            <th>Total Traité</th>
                            <th>Commission</th>
                            <th>Net Prop.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['payments'])): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 30px;">Aucun paiement.</td></tr>
                        <?php else: foreach($data['payments'] as $pay) : ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($pay->transaction_id); ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pay->paid_at)); ?></td>
                            <td>
                                <div style="font-weight: 600;"><?= htmlspecialchars($pay->first_name . ' ' . $pay->last_name); ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($pay->title); ?></div>
                            </td>
                            <td>
                                <?php if($pay->payment_method == 'mobile_money'): ?><span class="badge" style="background: rgba(255,180,0,0.15); color: #d39600;"><i class="fa-solid fa-mobile-screen"></i> Mobile</span>
                                <?php elseif($pay->payment_method == 'card'): ?><span class="badge" style="background: rgba(0,166,153,0.15); color: var(--secondary);"><i class="fa-regular fa-credit-card"></i> Carte</span>
                                <?php else: ?><span class="badge" style="background: rgba(0,138,5,0.15); color: var(--success);"><i class="fa-solid fa-building-columns"></i> Banque</span><?php endif; ?>
                            </td>
                            <td style="font-weight: 700;"><?= number_format($pay->amount, 0, ',', ' '); ?></td>
                            <td style="font-weight: 600; color: var(--success);">+<?= number_format($pay->commission, 0, ',', ' '); ?></td>
                            <td style="font-weight: 600; color: var(--secondary);"><?= number_format($pay->owner_amount, 0, ',', ' '); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
