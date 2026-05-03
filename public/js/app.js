// app.js

document.addEventListener('DOMContentLoaded', () => {
    
    // --- DARK MODE TOGGLE ---
    const darkModeBtn = document.getElementById('dark-mode-toggle');
    if (darkModeBtn) {
        // Init
        if (localStorage.getItem('theme') === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
            darkModeBtn.innerHTML = '<i class="fa-solid fa-sun"></i> Mode Clair';
        }

        // Toggle
        darkModeBtn.addEventListener('click', () => {
            if (document.body.getAttribute('data-theme') === 'dark') {
                document.body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                darkModeBtn.innerHTML = '<i class="fa-solid fa-moon"></i> Mode Sombre';
            } else {
                document.body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                darkModeBtn.innerHTML = '<i class="fa-solid fa-sun"></i> Mode Clair';
            }
        });
    }

    // --- SWEETALERT WRAPPER ---
    window.showToast = function(message, type = 'success') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: document.body.getAttribute('data-theme') === 'dark' ? '#1E1E1E' : '#FFF',
            color: document.body.getAttribute('data-theme') === 'dark' ? '#FFF' : '#222'
        });
    };

    // --- FLASH MESSAGES HANDLING (From PHP session) ---
    const flashMsg = document.getElementById('flash-message');
    if (flashMsg) {
        const msg = flashMsg.dataset.message;
        const type = flashMsg.dataset.type;
        showToast(msg, type);
    }

    // --- NOTIFICATIONS DROPDOWN ---
    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    
    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.style.display = notifDropdown.style.display === 'flex' ? 'none' : 'flex';
            
            // Mark as read if opening
            if (notifDropdown.style.display === 'flex') {
                const unreadBadge = document.getElementById('notif-badge');
                if (unreadBadge) {
                    fetch(window.URLROOT + '/api/markNotificationsRead', { method: 'POST' })
                    .then(() => unreadBadge.style.display = 'none');
                }
            }
        });

        document.addEventListener('click', () => {
            notifDropdown.style.display = 'none';
        });
        notifDropdown.addEventListener('click', (e) => e.stopPropagation());
    }

    // --- GLOBAL OMNISEARCH ---
    const searchInput = document.getElementById('global-search-input');
    const searchResults = document.getElementById('global-search-results');
    let searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`${window.URLROOT}/api/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('a');
                            div.href = `${window.URLROOT}/residences/show/${item.id}`;
                            div.style.cssText = 'display: flex; align-items: center; gap: 10px; padding: 10px 15px; border-bottom: 1px solid var(--border); color: var(--text-main); text-decoration: none;';
                            div.innerHTML = `
                                <i class="fa-solid fa-house-chimney text-muted"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem;">${item.title}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">${item.city}</div>
                                </div>
                            `;
                            // Hover effect handled inline or via class
                            div.onmouseover = () => div.style.background = 'var(--bg-light)';
                            div.onmouseout = () => div.style.background = 'transparent';
                            
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div style="padding: 15px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">Aucun résultat</div>';
                        searchResults.style.display = 'block';
                    }
                });
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target !== searchInput && e.target !== searchResults) {
                searchResults.style.display = 'none';
            }
        });
    }

    // --- ANIMATIONS ENTRANCE ---
    const animatedElements = document.querySelectorAll('.property-card, .stat-card, .category-card');
    animatedElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.05}s`;
    });
});
