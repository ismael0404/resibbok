<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_owner.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mes Revenus & Paiements</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary) 0%, #008f84 100%); color: white;">
                <h3 style="font-size: 1rem; font-weight: 500; margin-bottom: 10px; opacity: 0.9;">Total Gains Nets</h3>
                <p style="font-size: 1.8rem; font-weight: 700;"><?= number_format($data['total_earnings'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; opacity: 0.8;">Depuis votre inscription</div>
            </div>
            
            <div class="stat-card">
                <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500; margin-bottom: 10px;">Revenus du mois</h3>
                <p style="font-size: 1.8rem; font-weight: 700; color: var(--text-main);"><?= number_format($data['monthly_earnings'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Mois en cours</div>
            </div>

            <div class="stat-card">
                <h3 style="color: var(--text-muted); font-size: 1rem; font-weight: 500; margin-bottom: 10px;">Commissions payées</h3>
                <p style="font-size: 1.8rem; font-weight: 700; color: var(--primary);"><?= number_format($data['total_commission'], 0, ',', ' '); ?> FCFA</p>
                <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Frais de service ResiBook (<?= COMMISSION_RATE; ?>%)</div>
            </div>
        </div>

        <div class="table-container">
            <div style="padding: 20px; border-bottom: 1px solid var(--border);">
                <h3 style="font-size: 1.2rem; font-weight: 600;"><i class="fa-solid fa-clock-rotate-left text-muted"></i> Historique des encaissements</h3>
            </div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date du paiement</th>
                            <th>Bien réservé</th>
                            <th>Période</th>
                            <th>Montant payé (Client)</th>
                            <th>Frais ResiBook</th>
                            <th>Revenu Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['payments'])): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 30px;">Aucun paiement reçu.</td></tr>
                        <?php else: foreach($data['payments'] as $pay) : ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($pay->transaction_id); ?></td>
                            <td><?= date('d/m/Y', strtotime($pay->paid_at)); ?></td>
                            <td style="font-weight: 600;"><?= htmlspecialchars($pay->title); ?></td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);">
                                <?= date('d/m/y', strtotime($pay->check_in)); ?> - <?= date('d/m/y', strtotime($pay->check_out)); ?>
                            </td>
                            <td style="font-weight: 600;"><?= number_format($pay->amount, 0, ',', ' '); ?></td>
                            <td style="font-weight: 600; color: var(--primary);">-<?= number_format($pay->commission, 0, ',', ' '); ?></td>
                            <td style="font-weight: 700; color: var(--secondary);"><?= number_format($pay->owner_amount, 0, ',', ' '); ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
