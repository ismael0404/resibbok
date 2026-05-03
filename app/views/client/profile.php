<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_client.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Mon Profil client</h2>
            <a href="<?= URLROOT; ?>/client/settings" class="btn btn-outline"><i class="fa-solid fa-pen"></i> Modifier profil</a>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 40px;">
            <!-- Profil Info -->
            <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; text-align: center;">
                <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--bg-light); margin: 0 auto 20px; position: relative; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--primary); font-weight: bold; overflow: hidden;">
                    <?php if($data['user']->avatar): ?>
                        <img src="<?= URLROOT.'/uploads/'.$data['user']->avatar; ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <?= substr($data['user']->first_name, 0, 1) . substr($data['user']->last_name, 0, 1); ?>
                    <?php endif; ?>
                </div>
                
                <h3 style="font-size: 1.5rem; margin-bottom: 5px;"><?= htmlspecialchars($data['user']->first_name . ' ' . $data['user']->last_name); ?></h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($data['user']->city ?: 'Non renseigné'); ?></p>
                
                <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 25px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 15px 0;">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);"><?= $data['total_bookings']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Réservations</div>
                    </div>
                    <div style="width: 1px; background: var(--border);"></div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);"><?= $data['total_favorites']; ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Favoris</div>
                    </div>
                </div>
                
                <div style="text-align: left; font-size: 0.95rem;">
                    <p style="margin-bottom: 10px;"><i class="fa-solid fa-envelope text-muted" style="width: 25px;"></i> <?= htmlspecialchars($data['user']->email); ?></p>
                    <p style="margin-bottom: 10px;"><i class="fa-solid fa-phone text-muted" style="width: 25px;"></i> <?= htmlspecialchars($data['user']->phone); ?></p>
                    <p style="margin-bottom: 10px;"><i class="fa-solid fa-calendar text-muted" style="width: 25px;"></i> Inscrit depuis <?= date('M Y', strtotime($data['user']->created_at)); ?></p>
                </div>
            </div>

            <!-- Stats & Activités -->
            <div>
                <?php if($data['user']->bio): ?>
                <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; margin-bottom: 30px;">
                    <h4 style="font-size: 1.2rem; margin-bottom: 15px;"><i class="fa-solid fa-address-card text-primary"></i> À propos de moi</h4>
                    <p style="color: var(--text-muted); line-height: 1.6;"><?= nl2br(htmlspecialchars($data['user']->bio)); ?></p>
                </div>
                <?php endif; ?>

                <div class="table-container">
                    <div style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <h4 style="font-size: 1.2rem; font-weight: 600;"><i class="fa-solid fa-suitcase-rolling text-primary"></i> Mes prochains voyages</h4>
                        <a href="<?= URLROOT; ?>/client/reservations" class="btn btn-outline btn-sm">Gérer</a>
                    </div>
                    
                    <?php if(empty($data['recent_bookings'])): ?>
                        <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-plane-departure" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                            Aucun voyage prévu. <a href="<?= URLROOT; ?>/residences" style="color: var(--primary); text-decoration: none; font-weight: 600;">Explorez nos biens</a>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <tbody>
                                    <?php foreach($data['recent_bookings'] as $res): ?>
                                    <tr>
                                        <td style="width: 60px;">
                                            <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); background: url('<?= $res->primary_image ? URLROOT.'/uploads/'.$res->primary_image : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=100&q=80'; ?>') center/cover;"></div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($res->title); ?></div>
                                            <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($res->city); ?></div>
                                        </td>
                                        <td style="font-size: 0.9rem;">
                                            <?= date('d/m/Y', strtotime($res->check_in)); ?> au <?= date('d/m/Y', strtotime($res->check_out)); ?>
                                        </td>
                                        <td>
                                            <?php if($res->status == 'pending'): ?><span class="badge badge-pending">En attente</span>
                                            <?php elseif($res->status == 'confirmed'): ?><span class="badge" style="background: rgba(0, 166, 153, 0.15); color: var(--secondary);">Confirmée</span>
                                            <?php elseif($res->status == 'completed'): ?><span class="badge badge-success">Terminée</span>
                                            <?php else: ?><span class="badge badge-danger">Annulée</span><?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
