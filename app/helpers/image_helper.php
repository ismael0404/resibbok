<?php
// app/helpers/image_helper.php

/**
 * Système intelligent d'images pour les propriétés
 * 
 * 1. Utilise les images de la BDD si disponibles
 * 2. Sinon, génère des images uniques basées sur le type
 * 3. Utilise crc32() pour garantir que l'aléatoire soit constant pour chaque bien
 */
function getDynamicPropertyImages($property, $count = 5) {
    $finalImages = [];
    $dbImages = [];

    // Priorité 1: Images depuis la DB (table property_images)
    // On suppose que le modèle a chargé les images s'il y en a (par exemple $property->images_array)
    // Sinon on peut instancier le DB et charger, ou le passer en paramètre. 
    // Pour éviter trop de requêtes, on regarde si on a passé les images.
    
    // Si la propriété a déjà un tableau d'images (pour la page show)
    if (isset($property->gallery_images) && is_array($property->gallery_images) && !empty($property->gallery_images)) {
        foreach ($property->gallery_images as $img) {
            $path = dirname(dirname(dirname(APPROOT))) . '/uploads/' . $img->image_path;
            if (file_exists($path)) {
                $finalImages[] = URLROOT . '/uploads/' . $img->image_path;
            }
        }
    } else {
        // Fallback: vérifier 'primary_image' si pas de galerie chargée
        if (!empty($property->primary_image)) {
            $path = dirname(dirname(dirname(APPROOT))) . '/uploads/' . $property->primary_image;
            if (file_exists($path)) {
                $finalImages[] = URLROOT . '/uploads/' . $property->primary_image;
            }
        }
    }

    // Si on a assez d'images, on retourne
    if (count($finalImages) >= $count) {
        return array_slice($finalImages, 0, $count);
    }

    // Priorité 2: Images locales /public/images
    $imagesDir = dirname(dirname(dirname(APPROOT))) . '/public/images';
    $cat = strtolower($property->category_name ?? '');
    
    $prefix = '';
    if (strpos($cat, 'villa') !== false) $prefix = 'villa-';
    elseif (strpos($cat, 'appartement') !== false) $prefix = 'appartement-';
    elseif (strpos($cat, 'maison') !== false) $prefix = 'maison-';
    elseif (strpos($cat, 'studio') !== false) $prefix = 'studio-';
    elseif (strpos($cat, 'résidence') !== false || strpos($cat, 'residence') !== false) $prefix = 'villa-';

    $localImages = [];
    if (is_dir($imagesDir)) {
        $files = scandir($imagesDir);
        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                if (empty($prefix) || strpos($file, $prefix) === 0) {
                    $localImages[] = URLROOT . '/public/images/' . $file;
                }
            }
        }
        
        // Si aucune image trouvée pour le préfixe, on prend tout
        if (empty($localImages)) {
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $localImages[] = URLROOT . '/public/images/' . $file;
                }
            }
        }
    }

    // Logique anti-répétition avec crc32()
    if (!empty($localImages)) {
        $seed = crc32($property->id . $property->title);
        
        // Fixons la seed pour mt_rand temporairement pour mélanger de manière consistante
        mt_srand($seed);
        $shuffledLocal = $localImages;
        
        // Fisher-Yates shuffle manuel avec mt_rand
        for ($i = count($shuffledLocal) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            $tmp = $shuffledLocal[$i];
            $shuffledLocal[$i] = $shuffledLocal[$j];
            $shuffledLocal[$j] = $tmp;
        }
        
        // Rétablir la seed aléatoire normale
        mt_srand();
        
        // Ajouter les images locales jusqu'à atteindre $count
        foreach ($shuffledLocal as $img) {
            if (count($finalImages) >= $count) break;
            if (!in_array($img, $finalImages)) {
                $finalImages[] = $img;
            }
        }
    }

    // Fallback final s'il n'y a toujours pas assez d'images
    while (count($finalImages) < $count) {
        $finalImages[] = URLROOT . '/public/images/villa-cocody-1.jpg'; // Fallback ultime
    }

    return $finalImages;
}
