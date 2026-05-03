<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_admin.php'; ?>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-size: 1.8rem;">Gestion des Utilisateurs</h2>
            <div>
                <a href="<?= URLROOT; ?>/admin/users" class="btn btn-sm <?= empty($data['filter_role']) ? 'btn-primary' : 'btn-outline'; ?>">Tous</a>
                <a href="<?= URLROOT; ?>/admin/users?role=3" class="btn btn-sm <?= ($data['filter_role']??'')=='3' ? 'btn-primary' : 'btn-outline'; ?>">Clients</a>
            </div>
        </div>

        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Email / Tél</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['users'])): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 30px;">Aucun utilisateur.</td></tr>
                        <?php else: foreach($data['users'] as $user) : ?>
                        <tr>
                            <td>#<?= $user->id; ?></td>
                            <td style="font-weight: 600;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold; color: var(--primary);">
                                        <?= substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1); ?>
                                    </div>
                                    <?= htmlspecialchars($user->first_name . ' ' . $user->last_name); ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user->email); ?><br><small class="text-muted"><?= htmlspecialchars($user->phone); ?></small></td>
                            <td><span class="badge" style="background: var(--bg-light); color: var(--text-main);"><?= ucfirst($user->role_name); ?></span></td>
                            <td>
                                <?php if($user->status == 'active'): ?><span class="badge badge-success">Actif</span>
                                <?php elseif($user->status == 'suspended'): ?><span class="badge badge-warning">Suspendu</span>
                                <?php elseif($user->status == 'pending'): ?><span class="badge badge-pending">En attente</span>
                                <?php else: ?><span class="badge badge-danger">Rejeté</span><?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                    <?php if($user->status == 'active'): ?>
                                        <form action="<?= URLROOT; ?>/admin/suspendUser/<?= $user->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Suspendre cet utilisateur ?');">
                                            <button type="submit" class="btn btn-sm btn-outline" title="Suspendre"><i class="fa-solid fa-ban"></i></button>
                                        </form>
                                    <?php elseif($user->status == 'suspended'): ?>
                                        <form action="<?= URLROOT; ?>/admin/activateUser/<?= $user->id; ?>" method="POST" style="display:inline;">
                                            <button type="submit" class="btn btn-sm btn-outline" style="color: var(--success); border-color: var(--success);" title="Activer"><i class="fa-solid fa-check"></i></button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?= URLROOT; ?>/admin/deleteUser/<?= $user->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer définitivement cet utilisateur ? Cette action est irréversible.');">
                                        <button type="submit" class="btn btn-sm btn-outline" style="color: var(--danger); border-color: var(--danger);" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
