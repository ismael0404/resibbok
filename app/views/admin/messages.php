<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php 
    if ($_SESSION['user_role'] == 1) require APPROOT . '/views/inc/sidebar_admin.php';
    elseif ($_SESSION['user_role'] == 2) require APPROOT . '/views/inc/sidebar_owner.php';
    else require APPROOT . '/views/inc/sidebar_client.php';
    ?>

    <main class="main-content" style="display: flex; flex-direction: column; height: calc(100vh - 80px); padding: 0;">
        <div style="padding: 25px 40px; border-bottom: 1px solid var(--border); background: var(--bg-light); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.8rem;">Messagerie Interne</h2>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('new-msg-modal').style.display='flex'"><i class="fa-solid fa-pen-to-square"></i> Nouveau message</button>
        </div>

        <div style="display: flex; flex: 1; overflow: hidden; background: var(--white);">
            <!-- Sidebar Contacts -->
            <div style="width: 350px; border-right: 1px solid var(--border); display: flex; flex-direction: column;">
                <div style="padding: 15px; border-bottom: 1px solid var(--border);">
                    <div class="search-bar" style="width: 100%;">
                        <input type="text" placeholder="Rechercher une conversation..." style="width: 100%;">
                    </div>
                </div>
                <div style="flex: 1; overflow-y: auto;" id="conversations-list">
                    <?php if(empty($data['conversations'])): ?>
                        <div style="padding: 30px; text-align: center; color: var(--text-muted);">Aucune conversation.</div>
                    <?php else: foreach($data['conversations'] as $conv): ?>
                        <div class="conv-item" onclick="loadThread(<?= $conv->other_user_id; ?>, '<?= addslashes(htmlspecialchars($conv->first_name . ' ' . $conv->last_name)); ?>')" data-uid="<?= $conv->other_user_id; ?>" style="padding: 15px; border-bottom: 1px solid var(--border); cursor: pointer; display: flex; gap: 15px; transition: var(--transition);">
                            <div style="position: relative;">
                                <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--primary);">
                                    <?= substr($conv->first_name, 0, 1) . substr($conv->last_name, 0, 1); ?>
                                </div>
                                <?php if($conv->unread_count > 0): ?>
                                    <span style="position: absolute; top: -2px; right: -2px; background: var(--primary); color: white; width: 18px; height: 18px; border-radius: 50%; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid white;"><?= $conv->unread_count; ?></span>
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1; overflow: hidden;">
                                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                    <h4 style="font-weight: 600; font-size: 1rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($conv->first_name . ' ' . $conv->last_name); ?></h4>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);"><?= date('d/m', strtotime($conv->created_at)); ?></span>
                                </div>
                                <p style="margin: 5px 0 0; font-size: 0.85rem; color: <?= $conv->unread_count > 0 ? 'var(--text-main)' : 'var(--text-muted)'; ?>; font-weight: <?= $conv->unread_count > 0 ? '600' : '400'; ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= ($conv->sender_id == $_SESSION['user_id'] ? 'Vous: ' : '') . htmlspecialchars($conv->message); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Thread View -->
            <div style="flex: 1; display: flex; flex-direction: column; background: #f9fafb;">
                <div id="thread-header" style="padding: 20px; border-bottom: 1px solid var(--border); background: var(--white); display: none;">
                    <h3 id="thread-user-name" style="font-size: 1.1rem; margin: 0;">Sélectionnez une conversation</h3>
                </div>
                
                <div id="thread-messages" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px;">
                    <div style="text-align: center; color: var(--text-muted); margin-top: 50px;">
                        <i class="fa-regular fa-comments" style="font-size: 4rem; opacity: 0.5; margin-bottom: 15px;"></i>
                        <p>Sélectionnez une conversation sur la gauche pour commencer à discuter.</p>
                    </div>
                </div>

                <div id="thread-input" style="padding: 20px; border-top: 1px solid var(--border); background: var(--white); display: none;">
                    <form id="msg-form" onsubmit="sendMessage(event)" style="display: flex; gap: 15px;">
                        <input type="hidden" id="receiver-id">
                        <input type="text" id="msg-text" class="form-control" placeholder="Écrivez votre message..." required style="border-radius: 30px;">
                        <button type="submit" class="btn btn-primary" style="width: 50px; height: 50px; border-radius: 50%; padding: 0;"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal New Message -->
<div id="new-msg-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--white); padding: 30px; border-radius: var(--radius-md); width: 100%; max-width: 500px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <h3 style="font-size: 1.3rem;">Nouveau message</h3>
            <button onclick="document.getElementById('new-msg-modal').style.display='none'" style="background:none;border:none;font-size:1.2rem;cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form onsubmit="startNewConversation(event)">
            <div class="form-group">
                <label>Destinataire</label>
                <select id="new-receiver" class="form-control" required>
                    <option value="">Sélectionnez un contact...</option>
                    <?php foreach($data['contacts'] as $contact): ?>
                        <option value="<?= $contact->id; ?>"><?= htmlspecialchars($contact->first_name . ' ' . $contact->last_name); ?> (<?= $contact->role_id == 1 ? 'Admin' : ($contact->role_id == 2 ? 'Propriétaire' : 'Client'); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea id="new-msg-text" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Envoyer le message</button>
        </form>
    </div>
</div>

<style>
.conv-item:hover { background: var(--bg-light); }
.conv-item.active { background: rgba(255,56,92,0.05); border-left: 3px solid var(--primary); padding-left: 12px; }
.msg-bubble { max-width: 70%; padding: 12px 18px; border-radius: 20px; position: relative; word-wrap: break-word; }
.msg-bubble.sent { background: var(--primary); color: white; align-self: flex-end; border-bottom-right-radius: 4px; }
.msg-bubble.received { background: var(--white); color: var(--text-main); align-self: flex-start; border-bottom-left-radius: 4px; border: 1px solid var(--border); }
.msg-time { font-size: 0.7rem; margin-top: 5px; opacity: 0.7; text-align: right; }
</style>

<script>
let currentReceiver = null;
let pollInterval = null;

function loadThread(userId, userName) {
    currentReceiver = userId;
    
    // UI Update
    document.querySelectorAll('.conv-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`.conv-item[data-uid="${userId}"]`).classList.add('active');
    
    document.getElementById('thread-header').style.display = 'block';
    document.getElementById('thread-user-name').textContent = userName;
    document.getElementById('thread-input').style.display = 'block';
    document.getElementById('receiver-id').value = userId;
    
    fetchMessages();
    
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(fetchMessages, 5000); // Poll every 5s
}

function fetchMessages() {
    if (!currentReceiver) return;
    
    fetch(`<?= URLROOT; ?>/messages/thread/${currentReceiver}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const container = document.getElementById('thread-messages');
            const wasScrolledToBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 10;
            
            container.innerHTML = '';
            
            data.messages.forEach(msg => {
                const isSent = msg.sender_id == data.current_user;
                const date = new Date(msg.created_at);
                const timeStr = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                const div = document.createElement('div');
                div.className = `msg-bubble ${isSent ? 'sent' : 'received'}`;
                div.innerHTML = `
                    <div>${msg.message.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                    <div class="msg-time">${timeStr}</div>
                `;
                container.appendChild(div);
            });
            
            if (wasScrolledToBottom || container.children.length > 0) {
                container.scrollTop = container.scrollHeight;
            }
        }
    });
}

function sendMessage(e) {
    e.preventDefault();
    const text = document.getElementById('msg-text').value.trim();
    if (!text || !currentReceiver) return;
    
    document.getElementById('msg-text').value = '';
    
    fetch(`<?= URLROOT; ?>/messages/send`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            receiver_id: currentReceiver,
            message: text
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            fetchMessages(); // Refresh immediately
        } else {
            showToast('Erreur d\'envoi', 'error');
        }
    });
}

function startNewConversation(e) {
    e.preventDefault();
    const receiverId = document.getElementById('new-receiver').value;
    const text = document.getElementById('new-msg-text').value;
    
    fetch(`<?= URLROOT; ?>/messages/send`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            receiver_id: receiverId,
            message: text
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('new-msg-modal').style.display = 'none';
            window.location.reload(); // Reload to show new conversation in sidebar
        }
    });
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
