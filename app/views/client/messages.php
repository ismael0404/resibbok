<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="dashboard-layout">
    <?php require APPROOT . '/views/inc/sidebar_client.php'; ?>

    <main class="main-content" style="display: flex; flex-direction: column; height: 100vh; overflow: hidden; padding: 20px;">
        <h2 style="font-size: 1.8rem; margin-bottom: 20px;">Ma Messagerie</h2>

        <div style="display: flex; flex: 1; background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
            
            <!-- Sidebar Contacts/Conversations -->
            <div style="width: 320px; border-right: 1px solid var(--border); display: flex; flex-direction: column; background: var(--bg-light);">
                <div style="padding: 20px; border-bottom: 1px solid var(--border);">
                    <input type="text" class="form-control" placeholder="Rechercher un contact..." style="border-radius: 50px;">
                </div>
                
                <div style="flex: 1; overflow-y: auto;">
                    <?php if(empty($data['conversations']) && empty($data['contacts'])): ?>
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                            Aucun contact pour le moment.
                        </div>
                    <?php else: ?>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <!-- Conversations existantes -->
                            <?php foreach($data['conversations'] as $conv): ?>
                                <li class="contact-item" onclick="loadThread(<?= $conv->other_user_id; ?>, '<?= addslashes($conv->first_name . ' ' . $conv->last_name); ?>')" style="padding: 15px 20px; border-bottom: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; gap: 15px; transition: var(--transition);">
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; overflow: hidden;">
                                        <?php if($conv->avatar): ?>
                                            <img src="<?= URLROOT.'/uploads/'.$conv->avatar; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?= substr($conv->first_name, 0, 1); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 0.95rem; display: flex; justify-content: space-between;">
                                            <?= htmlspecialchars($conv->first_name . ' ' . $conv->last_name); ?>
                                            <?php if($conv->unread_count > 0): ?>
                                                <span style="background: var(--primary); color: white; border-radius: 50px; padding: 2px 6px; font-size: 0.7rem;"><?= $conv->unread_count; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">
                                            <?= htmlspecialchars($conv->message); ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            
                            <!-- Autres contacts -->
                            <?php if(!empty($data['contacts'])): ?>
                                <li style="padding: 10px 20px; background: var(--bg-main); font-weight: 700; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Autres Contacts</li>
                                <?php foreach($data['contacts'] as $contact): 
                                    // Ignorer si déjà dans conversations
                                    $inConv = false;
                                    foreach($data['conversations'] as $c) { if($c->other_user_id == $contact->id) $inConv = true; }
                                    if($inConv) continue;
                                ?>
                                    <li class="contact-item" onclick="loadThread(<?= $contact->id; ?>, '<?= addslashes($contact->first_name . ' ' . $contact->last_name); ?>')" style="padding: 15px 20px; border-bottom: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; gap: 15px; transition: var(--transition);">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--secondary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; overflow: hidden;">
                                            <?php if($contact->avatar): ?>
                                                <img src="<?= URLROOT.'/uploads/'.$contact->avatar; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <?= substr($contact->first_name, 0, 1); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div style="flex: 1; font-weight: 600; font-size: 0.95rem;">
                                            <?= htmlspecialchars($contact->first_name . ' ' . $contact->last_name); ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chat Area -->
            <div style="flex: 1; display: flex; flex-direction: column; background: var(--bg-main);">
                <div id="chat-header" style="padding: 15px 20px; border-bottom: 1px solid var(--border); background: var(--white); display: flex; align-items: center; gap: 15px; font-weight: 700; font-size: 1.1rem; visibility: hidden;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;"><i class="fa-solid fa-user"></i></div>
                    <span id="chat-user-name">Sélectionnez un contact</span>
                    <div id="chat-property-preview" class="msg-property-preview" style="display: none;"></div>
                </div>

                <div id="chat-messages" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px;">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); opacity: 0.5;">
                        <i class="fa-regular fa-comments" style="font-size: 5rem; margin-bottom: 15px;"></i>
                        <p style="font-size: 1.2rem;">Sélectionnez une conversation pour commencer à discuter.</p>
                    </div>
                </div>

                <div id="chat-input-area" style="padding: 15px 20px; background: var(--white); border-top: 1px solid var(--border); visibility: hidden;">
                    <form id="chat-form" style="display: flex; gap: 10px;" onsubmit="sendMessage(event)">
                        <input type="hidden" id="receiver_id" value="">
                        <input type="text" id="message_text" class="form-control" placeholder="Écrivez votre message..." required style="border-radius: 50px;">
                        <button type="submit" class="btn btn-primary" style="border-radius: 50px; padding: 0 25px;"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .contact-item:hover { background: var(--white); }
    .contact-item.active-contact { background: var(--white); border-left: 3px solid var(--primary); }
    .msg-bubble { max-width: 70%; padding: 12px 18px; border-radius: 20px; font-size: 0.95rem; line-height: 1.4; position: relative; animation: slideInUp 0.3s ease; }
    .msg-sent { align-self: flex-end; background: var(--primary); color: white; border-bottom-right-radius: 5px; }
    .msg-received { align-self: flex-start; background: var(--white); border: 1px solid var(--border); color: var(--text-main); border-bottom-left-radius: 5px; }
    .msg-time { font-size: 0.7rem; opacity: 0.7; margin-top: 5px; text-align: right; }
    @keyframes slideInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
let currentReceiver = null;
let currentUserName = '';

function loadThread(userId, userName) {
    currentReceiver = userId;
    currentUserName = userName;
    
    document.getElementById('chat-header').style.visibility = 'visible';
    document.getElementById('chat-input-area').style.visibility = 'visible';
    document.getElementById('chat-user-name').textContent = userName;
    document.getElementById('receiver_id').value = userId;
    
    // Highlight active contact
    document.querySelectorAll('.contact-item').forEach(el => el.classList.remove('active-contact'));
    event && event.currentTarget && event.currentTarget.classList.add('active-contact');
    
    fetchMessages();
    loadPropertyPreview(userId);
}

function loadPropertyPreview(userId) {
    fetch('<?= URLROOT; ?>/messages/propertyInfo/' + userId)
    .then(r => r.json())
    .then(data => {
        const preview = document.getElementById('chat-property-preview');
        if(data.success && data.property) {
            const p = data.property;
            const imgSrc = p.property_image ? '<?= URLROOT; ?>/uploads/' + p.property_image : '<?= URLROOT; ?>/public/images/villa-cocody-1.jpg';
            let priceText = '';
            if(p.listing_type === 'reservation') priceText = new Intl.NumberFormat('fr-FR').format(p.price_per_night) + ' FCFA/nuit';
            else if(p.listing_type === 'rental') priceText = new Intl.NumberFormat('fr-FR').format(p.price_monthly) + ' FCFA/mois';
            else priceText = new Intl.NumberFormat('fr-FR').format(p.price_sale) + ' FCFA';
            preview.innerHTML = `<img src="${imgSrc}" alt=""><div class="preview-info"><div class="preview-title">${p.title}</div><div class="preview-price">${priceText}</div></div>`;
            preview.style.display = 'flex';
        } else {
            preview.style.display = 'none';
        }
    }).catch(() => { document.getElementById('chat-property-preview').style.display = 'none'; });
}

function fetchMessages() {
    if (!currentReceiver) return;
    
    fetch('<?= URLROOT; ?>/messages/thread/' + currentReceiver)
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const container = document.getElementById('chat-messages');
            container.innerHTML = '';
            
            if(data.messages.length === 0) {
                container.innerHTML = '<div style="text-align:center; color:var(--text-muted); margin-top:20px;">Aucun message. Envoyez le premier !</div>';
                return;
            }

            data.messages.forEach(msg => {
                const isSent = msg.sender_id == data.current_user;
                const div = document.createElement('div');
                div.className = 'msg-bubble ' + (isSent ? 'msg-sent' : 'msg-received');
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                div.innerHTML = `<div>${msg.message.replace(/\n/g, '<br>')}</div><div class="msg-time">${time}</div>`;
                container.appendChild(div);
            });
            
            container.scrollTop = container.scrollHeight;
        }
    });
}

function sendMessage(e) {
    e.preventDefault();
    const text = document.getElementById('message_text').value.trim();
    if(!text || !currentReceiver) return;

    fetch('<?= URLROOT; ?>/messages/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ receiver_id: currentReceiver, message: text })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('message_text').value = '';
            fetchMessages();
        } else {
            showToast(data.error || 'Erreur lors de l\'envoi', 'error');
        }
    });
}

// Auto-open conversation from URL params
<?php if(!empty($data['open_user'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const openUserId = <?= intval($data['open_user']); ?>;
    // Find contact name from conversations or contacts
    let userName = 'Propriétaire';
    <?php foreach($data['conversations'] as $conv): ?>
        if(<?= $conv->other_user_id; ?> === openUserId) userName = '<?= addslashes($conv->first_name . ' ' . $conv->last_name); ?>';
    <?php endforeach; ?>
    <?php if(!empty($data['contacts'])): foreach($data['contacts'] as $c): ?>
        if(<?= $c->id; ?> === openUserId) userName = '<?= addslashes($c->first_name . ' ' . $c->last_name); ?>';
    <?php endforeach; endif; ?>
    loadThread(openUserId, userName);
});
<?php endif; ?>

// Polling every 5s
setInterval(() => { if(currentReceiver) fetchMessages(); }, 5000);
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>

