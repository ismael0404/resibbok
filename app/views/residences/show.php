<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Image Gallery Header -->
<div style="background: var(--bg-main); padding-top: 20px;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <?php if($data['property']->listing_type == 'reservation'): ?>
                        <span class="badge" style="background: rgba(255,56,92,0.1); color: var(--primary);">Réservation</span>
                    <?php elseif($data['property']->listing_type == 'rental'): ?>
                        <span class="badge" style="background: rgba(0,166,153,0.1); color: #00A699;">Location</span>
                    <?php else: ?>
                        <span class="badge" style="background: rgba(0,138,5,0.1); color: #008A05;">Vente</span>
                    <?php endif; ?>

                    <?php if($data['property']->status == 'active'): ?>
                        <span class="badge" style="background: rgba(0,138,5,0.1); color: #008A05;">Disponible</span>
                    <?php elseif($data['property']->status == 'reserved'): ?>
                        <span class="badge" style="background: rgba(255,180,0,0.1); color: #FFB400;">Réservé</span>
                    <?php elseif($data['property']->status == 'rented'): ?>
                        <span class="badge" style="background: rgba(225,44,50,0.1); color: #E12C32;">Loué</span>
                    <?php elseif($data['property']->status == 'sold'): ?>
                        <span class="badge" style="background: rgba(225,44,50,0.1); color: #E12C32;">Vendu</span>
                    <?php else: ?>
                        <span class="badge badge-outline">Indisponible</span>
                    <?php endif; ?>
                </div>

                <h1 style="font-size: 2.2rem; margin-bottom: 10px; font-weight: 700;"><?= htmlspecialchars($data['property']->title); ?></h1>
                
                <div style="display: flex; gap: 15px; font-size: 0.95rem; color: var(--text-main); font-weight: 500;">
                    <?php if($data['property']->score > 0): ?>
                        <span><i class="fa-solid fa-star" style="color: #FFB400;"></i> <?= number_format($data['property']->score, 1); ?> (<?= count($data['reviews']); ?> avis)</span>
                    <?php endif; ?>
                    <span style="color: var(--text-muted);">•</span>
                    <span style="text-decoration: underline;"><i class="fa-solid fa-location-dot text-primary"></i> <?= htmlspecialchars($data['property']->address); ?>, <?= htmlspecialchars($data['property']->city); ?></span>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-outline" style="border-radius: 50px; padding: 10px 20px;"><i class="fa-solid fa-share"></i> Partager</button>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <button class="btn <?= $data['is_favorite'] ? 'btn-primary' : 'btn-outline'; ?>" id="fav-btn" onclick="toggleFavorite(<?= $data['property']->id; ?>)" style="border-radius: 50px; padding: 10px 20px;">
                        <i class="fa-solid fa-heart"></i> <?= $data['is_favorite'] ? 'Sauvegardé' : 'Sauvegarder'; ?>
                    </button>
                <?php else: ?>
                    <button class="btn btn-outline" onclick="window.location.href='<?= URLROOT; ?>/auth/login'" style="border-radius: 50px; padding: 10px 20px;">
                        <i class="fa-solid fa-heart"></i> Sauvegarder
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php 
            // Obtenir 5 images dynamiques uniques garanties
            $displayImages = getDynamicPropertyImages($data['property'], 5); 
        ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; grid-template-rows: 200px 200px; gap: 10px; border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 40px;">
            <div id="main-gallery-img" style="grid-row: span 2; background: url('<?= $displayImages[0]; ?>') center/cover; cursor: pointer; transition: background-image 0.3s ease;"></div>
            
            <?php for($i=1; $i<=4; $i++): ?>
                <div class="gallery-thumb" onclick="changeMainImage('<?= $displayImages[$i]; ?>')" style="background: url('<?= $displayImages[$i]; ?>') center/cover; cursor: pointer; position: relative; overflow: hidden;">
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.2); transition: 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0)'; this.parentElement.style.transform='scale(1.02)';" onmouseout="this.style.background='rgba(0,0,0,0.2)'; this.parentElement.style.transform='scale(1)';"></div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 50px;">
    
    <!-- Contenu Principal -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 30px; margin-bottom: 30px;">
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 5px;">Hôte : <?= htmlspecialchars($data['property']->first_name); ?></h2>
                <div style="font-size: 1rem; color: var(--text-muted);">
                    <?= htmlspecialchars($data['property']->category_name); ?> • <?= $data['property']->max_guests; ?> voyageurs • <?= $data['property']->bedrooms; ?> chambre(s) • <?= $data['property']->bathrooms; ?> sdb • <?= $data['property']->area_sqm; ?> m²
                </div>
            </div>
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; color: var(--primary); overflow: hidden; border: 2px solid var(--border);">
                <?php if($data['property']->avatar): ?>
                    <img src="<?= URLROOT.'/uploads/'.$data['property']->avatar; ?>" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <?= substr($data['property']->first_name, 0, 1); ?>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.3rem; margin-bottom: 15px;">À propos de ce logement</h3>
            <p style="color: var(--text-muted); line-height: 1.8; font-size: 1.05rem;"><?= nl2br(htmlspecialchars($data['property']->description)); ?></p>
        </div>

        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.3rem; margin-bottom: 20px;">Ce que propose ce logement</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <?php foreach($data['amenities'] as $amenity): ?>
                    <div style="display: flex; align-items: center; gap: 15px; font-size: 1.05rem; color: var(--text-main);">
                        <i class="<?= $amenity->icon; ?>" style="width: 30px; font-size: 1.2rem; color: var(--text-muted);"></i> <?= htmlspecialchars($amenity->name); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.3rem; margin-bottom: 20px;">Localisation</h3>
            <!-- Simulation Google Maps -->
            <div style="width: 100%; height: 350px; background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Abidjan_map_with_lagoon.png/800px-Abidjan_map_with_lagoon.png') center/cover; border-radius: var(--radius-lg); position: relative; border: 1px solid var(--border);">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: var(--primary); font-size: 3rem; text-shadow: 0 2px 5px rgba(0,0,0,0.3);">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div style="position: absolute; bottom: 20px; left: 20px; background: var(--white); padding: 10px 20px; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <?= htmlspecialchars($data['property']->city); ?>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 40px;">
            <h3 style="font-size: 1.3rem; margin-bottom: 20px;">Règles et Politiques</h3>
            <div style="background: var(--bg-light); padding: 30px; border-radius: var(--radius-lg); border: 1px solid var(--border);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <strong style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; font-size: 1.1rem;"><i class="fa-solid fa-book-open text-primary"></i> Règlement intérieur</strong>
                        <?php if($data['property']->rules): ?>
                            <ul style="padding-left: 20px; color: var(--text-muted); line-height: 1.8;">
                                <?php foreach(explode('|', $data['property']->rules) as $rule): ?>
                                    <li><?= htmlspecialchars(trim($rule)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <span style="color: var(--text-muted);">Aucune règle spécifique.</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; font-size: 1.1rem;"><i class="fa-solid fa-money-bill-transfer text-primary"></i> Annulation : <?= ucfirst($data['property']->cancellation_policy); ?></strong>
                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                            <?php 
                                if($data['property']->cancellation_policy == 'flexible') echo "Remboursement intégral jusqu'à 24h avant l'arrivée. Parfait pour les voyages de dernière minute.";
                                elseif($data['property']->cancellation_policy == 'moderate') echo "Remboursement intégral jusqu'à 5 jours avant l'arrivée. Les frais de service ne sont pas remboursés.";
                                else echo "Remboursement de 50% jusqu'à 1 semaine avant l'arrivée. Le premier mois de location n'est pas remboursé.";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Sidebar Widget (Réservation/Contact) -->
    <div style="position: sticky; top: 100px; align-self: start;">
        <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.08);">
            
            <div style="font-size: 1.6rem; font-weight: 700; margin-bottom: 25px;">
                <?php 
                    if($data['property']->listing_type == 'reservation') echo number_format($data['property']->price_per_night, 0, ',', ' ') . ' <span style="font-size:1.1rem;font-weight:400;color:var(--text-muted);">FCFA / nuit</span>';
                    elseif($data['property']->listing_type == 'rental') echo number_format($data['property']->price_monthly, 0, ',', ' ') . ' <span style="font-size:1.1rem;font-weight:400;color:var(--text-muted);">FCFA / mois</span>';
                    else echo number_format($data['property']->price_sale, 0, ',', ' ') . ' <span style="font-size:1.1rem;font-weight:400;color:var(--text-muted);">FCFA</span>';
                ?>
            </div>

            <?php if($data['property']->status != 'active'): ?>
                <!-- Bien Indisponible -->
                <div style="background: rgba(225,44,50,0.1); color: var(--danger); padding: 20px; border-radius: var(--radius-md); text-align: center; margin-bottom: 20px; font-weight: 600;">
                    <i class="fa-solid fa-lock" style="font-size: 1.5rem; display: block; margin-bottom: 10px;"></i>
                    Ce bien est actuellement <?= strtolower($data['property']->status) == 'reserved' ? 'réservé' : (strtolower($data['property']->status) == 'rented' ? 'loué' : 'vendu'); ?>.
                </div>
                <button onclick="contactOwner()" class="btn btn-outline btn-block btn-lg" style="width: 100%;">
                    <i class="fa-solid fa-envelope"></i> Contacter l'hôte pour de futures disponibilités
                </button>

            <?php else: ?>
                <!-- Bien Disponible - Workflow Réservation / Location / Vente -->
                <?php if($data['property']->listing_type == 'reservation'): ?>
                    
                    <form action="<?= URLROOT; ?>/reservations/create" method="POST" id="booking-form">
                        <input type="hidden" name="property_id" value="<?= $data['property']->id; ?>">
                        
                        <div style="border: 1px solid var(--border); border-radius: var(--radius-md); overflow: hidden; margin-bottom: 20px;">
                            <div style="display: flex; border-bottom: 1px solid var(--border);">
                                <div style="flex: 1; padding: 10px 15px; border-right: 1px solid var(--border);">
                                    <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Arrivée</label>
                                    <input type="date" name="check_in" id="check_in" style="width: 100%; border: none; outline: none;" required min="<?= date('Y-m-d'); ?>" onchange="checkAvailability()">
                                </div>
                                <div style="flex: 1; padding: 10px 15px;">
                                    <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Départ</label>
                                    <input type="date" name="check_out" id="check_out" style="width: 100%; border: none; outline: none;" required min="<?= date('Y-m-d', strtotime('+1 day')); ?>" onchange="checkAvailability()">
                                </div>
                            </div>
                            <div style="padding: 10px 15px;">
                                <label style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Voyageurs</label>
                                <input type="number" name="guests" style="width: 100%; border: none; outline: none;" value="1" min="1" max="<?= $data['property']->max_guests; ?>" required>
                            </div>
                        </div>

                        <div id="booking-summary" style="display: none; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--text-muted);">
                                <span id="calc-nights"></span>
                                <span id="calc-subtotal"></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: var(--text-muted);">
                                <span>Frais de service (<?= COMMISSION_RATE; ?>%)</span>
                                <span id="calc-fee"></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border); font-size: 1.1rem;">
                                <span>Total</span>
                                <span id="calc-total" style="color: var(--primary);"></span>
                            </div>
                        </div>

                        <button type="submit" id="book-btn" class="btn btn-primary btn-block btn-lg" style="width: 100%; background: linear-gradient(to right, var(--primary), var(--primary-dark)); border: none; border-radius: var(--radius-md);">
                            Réserver
                        </button>
                        <div id="booking-error" style="color: var(--danger); font-size: 0.9rem; text-align: center; margin-top: 10px; display: none;">Ces dates ne sont pas disponibles.</div>
                        <div style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 15px;">Aucun montant ne sera débité pour le moment</div>
                    </form>

                <?php else: ?>
                    <!-- Formulaire pour location mensuelle ou Vente -->
                    <div style="margin-bottom: 25px;">
                        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">Pour ce type de bien, veuillez contacter directement le propriétaire pour organiser une visite et discuter des modalités.</p>
                    </div>
                    
                    <button onclick="openMessagePrefillModal()" class="btn btn-primary btn-block btn-lg" style="width: 100%; margin-bottom: 15px; background: linear-gradient(to right, var(--primary), var(--primary-dark)); border: none;">
                        <i class="fa-solid fa-<?= $data['property']->listing_type == 'rental' ? 'key' : 'cart-shopping'; ?>"></i>
                        <?= $data['property']->listing_type == 'rental' ? 'Louer maintenant' : 'Acheter'; ?>
                    </button>
                    
                    <div style="text-align: center;">
                        <button onclick="openOwnerModal()" class="btn btn-outline btn-block" style="width: 100%;">
                            <i class="fa-solid fa-user-tie"></i> Voir le profil du propriétaire
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Reviews Section -->
<div style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; border-top: 1px solid var(--border);">
    <h3 style="font-size: 1.6rem; margin-bottom: 30px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-star text-primary"></i> <?= $data['property']->score > 0 ? number_format($data['property']->score, 1) . ' · ' : ''; ?><?= count($data['reviews']); ?> avis
    </h3>

    <?php if(isset($_SESSION['user_id']) && !$data['hasReviewed'] && $_SESSION['user_role'] != 2): ?>
        <div style="background: var(--bg-light); padding: 30px; border-radius: var(--radius-lg); margin-bottom: 40px; border: 1px solid var(--border);">
            <h4 style="margin-bottom: 15px; font-size: 1.2rem;">Laissez un avis</h4>
            <form action="<?= URLROOT; ?>/reviews/add" method="POST">
                <input type="hidden" name="property_id" value="<?= $data['property']->id; ?>">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Votre note</label>
                    <div class="star-rating" style="display: flex; gap: 10px; font-size: 1.5rem; color: #ccc; cursor: pointer;">
                        <i class="fa-solid fa-star star-item" data-val="1"></i>
                        <i class="fa-solid fa-star star-item" data-val="2"></i>
                        <i class="fa-solid fa-star star-item" data-val="3"></i>
                        <i class="fa-solid fa-star star-item" data-val="4"></i>
                        <i class="fa-solid fa-star star-item" data-val="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="rating-val" value="0" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Votre commentaire</label>
                    <textarea name="comment" class="form-control" rows="4" placeholder="Partagez votre expérience..." required style="width: 100%; resize: none;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submit-review" disabled>Publier mon avis</button>
            </form>
        </div>
    <?php endif; ?>
    
    <?php if(empty($data['reviews'])): ?>
        <p style="color: var(--text-muted); text-align: center; padding: 40px; background: var(--bg-light); border-radius: var(--radius-lg);">Aucun avis pour le moment.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 40px;">
            <?php foreach($data['reviews'] as $review): ?>
                <div>
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--bg-main); display: flex; align-items: center; justify-content: center; font-weight: bold; overflow: hidden; border: 1px solid var(--border);">
                            <?php if($review->avatar): ?>
                                <img src="<?= URLROOT.'/uploads/'.$review->avatar; ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <?= substr($review->first_name, 0, 1); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($review->first_name); ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);"><?= date('M Y', strtotime($review->created_at)); ?></div>
                        </div>
                    </div>
                    <div style="color: #ffb400; font-size: 0.9rem; margin-bottom: 10px;">
                        <?php for($i=1; $i<=5; $i++) echo $i <= $review->rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
                    </div>
                    <p style="color: var(--text-main); line-height: 1.7;"><?= nl2br(htmlspecialchars($review->comment)); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Similar Properties Section -->
<?php if(!empty($data['similar'])): ?>
<div style="max-width: 1200px; margin: 0 auto; padding: 40px 20px; border-top: 1px solid var(--border); margin-bottom: 60px;">
    <h3 style="font-size: 1.6rem; margin-bottom: 30px;">Biens similaires</h3>
    
    <div class="property-grid" style="grid-template-columns: repeat(4, 1fr);">
        <?php foreach($data['similar'] as $prop): ?>
            <div class="property-card" onclick="window.location.href='<?= URLROOT; ?>/residences/show/<?= $prop->id; ?>'" style="cursor: pointer;">
                <div class="property-image" style="height: 180px;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 50%); z-index: 1;"></div>
                    <img src="<?= getDynamicPropertyImages($prop, 1)[0]; ?>" alt="<?= htmlspecialchars($prop->title); ?>">
                    
                    <!-- Dynamic Status Badge -->
                    <?php if($prop->listing_type == 'reservation'): ?>
                        <span class="property-badge" style="background: var(--primary); z-index: 2;">Réservation</span>
                    <?php elseif($prop->listing_type == 'rental'): ?>
                        <span class="property-badge" style="background: var(--secondary); z-index: 2;">Location</span>
                    <?php else: ?>
                        <span class="property-badge" style="background: var(--success); z-index: 2;">Vente</span>
                    <?php endif; ?>
                </div>
                <div class="property-content">
                    <div class="property-location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($prop->city); ?></div>
                    <h3 class="property-title" style="font-size: 1rem; margin-bottom: 8px;">
                        <?= htmlspecialchars($prop->title); ?>
                    </h3>
                    <div class="property-price" style="font-size: 1.1rem;">
                        <?php 
                            if($prop->listing_type == 'reservation') echo number_format($prop->price_per_night, 0, ',', ' ') . ' <span>FCFA /nuit</span>';
                            elseif($prop->listing_type == 'rental') echo number_format($prop->price_monthly, 0, ',', ' ') . ' <span>FCFA /mois</span>';
                            else echo number_format($prop->price_sale, 0, ',', ' ') . ' <span>FCFA</span>';
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Owner Modal -->
<div class="owner-modal-overlay" id="ownerModal" onclick="if(event.target===this)closeOwnerModal()">
    <div class="owner-modal">
        <button class="owner-modal-close" onclick="closeOwnerModal()"><i class="fa-solid fa-xmark"></i></button>
        <div style="text-align: center;">
            <div class="owner-avatar-lg">
                <?php if($data['property']->avatar): ?>
                    <img src="<?= URLROOT.'/uploads/'.$data['property']->avatar; ?>">
                <?php else: ?>
                    <?= substr($data['property']->first_name, 0, 1); ?>
                <?php endif; ?>
            </div>
            <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($data['property']->first_name . ' ' . $data['property']->last_name); ?></h3>
            <?php if(!empty($data['property']->owner_verification_badge)): ?>
                <span class="owner-badge-verified"><i class="fa-solid fa-circle-check"></i> Vérifié</span>
            <?php endif; ?>
        </div>
        <div class="owner-stats-grid">
            <div class="owner-stat">
                <div class="stat-value"><?= $data['owner_property_count']; ?></div>
                <div class="stat-label">Annonces actives</div>
            </div>
            <div class="owner-stat">
                <div class="stat-value"><?= date('M Y', strtotime($data['property']->owner_created_at ?? 'now')); ?></div>
                <div class="stat-label">Membre depuis</div>
            </div>
        </div>
        <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
            <?php if(!empty($data['property']->owner_city)): ?>
                <div style="margin-bottom: 8px;"><i class="fa-solid fa-location-dot" style="width:20px; color:var(--primary);"></i> <?= htmlspecialchars($data['property']->owner_city); ?></div>
            <?php endif; ?>
            <div style="margin-bottom: 8px;"><i class="fa-solid fa-envelope" style="width:20px; color:var(--primary);"></i> <?= htmlspecialchars($data['property']->owner_email); ?></div>
            <div><i class="fa-solid fa-phone" style="width:20px; color:var(--primary);"></i> <?= htmlspecialchars($data['property']->owner_phone); ?></div>
        </div>
        <div class="owner-modal-actions">
            <button onclick="closeOwnerModal();openMessagePrefillModal();" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Message</button>
            <a href="tel:<?= $data['property']->owner_phone; ?>" class="btn btn-outline"><i class="fa-solid fa-phone"></i> Appeler</a>
        </div>
    </div>
</div>

<!-- Message Prefill Modal -->
<div class="msg-prefill-overlay" id="msgPrefillModal" onclick="if(event.target===this)closeMsgPrefillModal()">
    <div class="msg-prefill-modal">
        <h3><i class="fa-solid fa-paper-plane text-primary"></i> Contacter le propriétaire</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Personnalisez votre message avant de l'envoyer :</p>
        <textarea id="prefill-message">Bonjour, je suis intéressé par votre bien : <?= htmlspecialchars($data['property']->title); ?>. Pouvez-vous me donner plus d'informations ?</textarea>
        <div class="msg-prefill-actions">
            <button onclick="closeMsgPrefillModal()" class="btn btn-outline">Annuler</button>
            <button onclick="sendPrefillMessage()" class="btn btn-primary" id="send-prefill-btn"><i class="fa-solid fa-paper-plane"></i> Envoyer</button>
        </div>
    </div>
</div>

<script>
function changeMainImage(url) {
    document.getElementById('main-gallery-img').style.backgroundImage = `url('${url}')`;
}

// --- Star Rating Interactions ---
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('.star-item');
    const ratingInput = document.getElementById('rating-val');
    const submitBtn = document.getElementById('submit-review');

    if(stars.length > 0) {
        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                const val = this.getAttribute('data-val');
                highlightStars(val);
            });

            star.addEventListener('mouseout', function() {
                const val = ratingInput.value;
                highlightStars(val);
            });

            star.addEventListener('click', function() {
                const val = this.getAttribute('data-val');
                ratingInput.value = val;
                highlightStars(val);
                submitBtn.disabled = false;
            });
        });

        function highlightStars(val) {
            stars.forEach(s => {
                if (s.getAttribute('data-val') <= val) {
                    s.style.color = '#FFB400';
                } else {
                    s.style.color = '#ccc';
                }
            });
        }
    }
});

// --- Modals ---
function openOwnerModal() { document.getElementById('ownerModal').classList.add('active'); }
function closeOwnerModal() { document.getElementById('ownerModal').classList.remove('active'); }
function openMessagePrefillModal() {
    <?php if(!isset($_SESSION['user_id'])): ?>
        window.location.href = '<?= URLROOT; ?>/auth/login'; return;
    <?php elseif(isset($_SESSION['user_role']) && $_SESSION['user_role'] != 3): ?>
        showToast('Connectez-vous en tant que client pour contacter le propriétaire', 'error'); return;
    <?php endif; ?>
    document.getElementById('msgPrefillModal').classList.add('active');
}
function closeMsgPrefillModal() { document.getElementById('msgPrefillModal').classList.remove('active'); }

function sendPrefillMessage() {
    const msg = document.getElementById('prefill-message').value.trim();
    if(!msg) { showToast('Veuillez écrire un message', 'error'); return; }
    const btn = document.getElementById('send-prefill-btn');
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi...';

    fetch('<?= URLROOT; ?>/messages/startConversation', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({
            receiver_id: <?= $data['property']->owner_id; ?>,
            property_id: <?= $data['property']->id; ?>,
            message: msg
        })
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            window.location.href = '<?= URLROOT; ?>/client/messages?open=' + d.receiver_id;
        } else {
            showToast(d.error || 'Erreur', 'error');
            btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Envoyer';
        }
    }).catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Envoyer'; });
}

<?php if(isset($_SESSION['user_id'])): ?>
function toggleFavorite(id) {
    fetch('<?= URLROOT; ?>/api/toggleFavorite', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: id })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const btn = document.getElementById('fav-btn');
            if(data.action === 'added') {
                btn.classList.remove('btn-outline'); btn.classList.add('btn-primary');
                btn.innerHTML = '<i class="fa-solid fa-heart"></i> Sauvegardé';
                showToast('Ajouté aux favoris !', 'success');
            } else {
                btn.classList.remove('btn-primary'); btn.classList.add('btn-outline');
                btn.innerHTML = '<i class="fa-solid fa-heart"></i> Sauvegarder';
                showToast('Retiré des favoris', 'info');
            }
        }
    });
}
function contactOwner() { openMessagePrefillModal(); }
<?php else: ?>
function toggleFavorite(id) { window.location.href = '<?= URLROOT; ?>/auth/login'; }
function contactOwner() { window.location.href = '<?= URLROOT; ?>/auth/login'; }
<?php endif; ?>

function checkAvailability() {
    const ci = document.getElementById('check_in').value;
    const co = document.getElementById('check_out').value;
    const propId = <?= $data['property']->id; ?>;
    if(!ci || !co) return;
    if(new Date(co) <= new Date(ci)) {
        showToast("La date de départ doit être après l'arrivée", "error");
        document.getElementById('check_out').value = ''; return;
    }
    fetch(`<?= URLROOT; ?>/reservations/checkAvailability?property_id=${propId}&check_in=${ci}&check_out=${co}`)
    .then(res => res.json())
    .then(data => {
        const btn = document.getElementById('book-btn');
        const err = document.getElementById('booking-error');
        const sum = document.getElementById('booking-summary');
        if(data.available) {
            btn.innerHTML = 'Réserver'; btn.disabled = false; err.style.display = 'none'; sum.style.display = 'block';
            document.getElementById('calc-nights').textContent = `${new Intl.NumberFormat('fr-FR').format(data.price_per_night)} FCFA x ${data.nights} nuits`;
            document.getElementById('calc-subtotal').textContent = `${new Intl.NumberFormat('fr-FR').format(data.subtotal)} FCFA`;
            document.getElementById('calc-fee').textContent = `${new Intl.NumberFormat('fr-FR').format(data.service_fee)} FCFA`;
            document.getElementById('calc-total').textContent = `${new Intl.NumberFormat('fr-FR').format(data.total)} FCFA`;
        } else {
            btn.innerHTML = 'Dates non disponibles'; btn.disabled = true; err.style.display = 'block'; sum.style.display = 'none';
        }
    });
}
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>

