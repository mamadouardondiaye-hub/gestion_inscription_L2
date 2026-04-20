<?php

/**
 * ============================================================
 *  database.php — Gestion du fichier JSON (notre "base de données")
 * ============================================================
 *  Ce fichier contient deux fonctions simples :
 *   - lireData()        : lit le fichier JSON et retourne un tableau PHP
 *   - sauvegarderData() : prend un tableau PHP et de l'écrire dans le JSON
 *
 *  IMPORTANT : __DIR__ désigne toujours le dossier où SE TROUVE ce fichier,
 *  peu importe depuis quel endroit tu lances "php index.php".
 *  C'est pour ça que le chemin sera toujours correct.
 */

// Chemin absolu vers le fichier JSON — toujours correct grâce à __DIR__
define('FICHIER_JSON', __DIR__ . '/data/data.json');

/**
 * Crée le dossier /data/ et le fichier data.json s'ils n'existent pas.
 * Appelée automatiquement au démarrage du programme.
 */
function initialiserFichier(): void
{
    $dossier = __DIR__ . '/data';

    // Créer le dossier /data/ s'il n'existe pas
    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }

    // Créer data.json avec une structure vide s'il n'existe pas
    if (!file_exists(FICHIER_JSON)) {
        $dataVide = [
            'etudiants'    => [],
            'formations'   => [],
            'inscriptions' => []
        ];
        file_put_contents(
            FICHIER_JSON,
            json_encode($dataVide, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}

// Initialisation automatique dès que ce fichier est chargé
initialiserFichier();

/**
 * Lit le fichier JSON et retourne les données sous forme de tableau PHP.
 */
function lireData(): array
{
    $contenu = file_get_contents(FICHIER_JSON);
    $data    = json_decode($contenu, true);

    // Si le JSON est corrompu ou vide, on retourne une structure vide propre
    if (!is_array($data)) {
        return ['etudiants' => [], 'formations' => [], 'inscriptions' => []];
    }

    return $data;
}

/**
 * Sauvegarde le tableau PHP dans le fichier JSON.
 * JSON_PRETTY_PRINT = le JSON sera lisible et claire
 * JSON_UNESCAPED_UNICODE = les accents s'affichent correctement
 */
function sauvegarderData(array $data): void
{
    $contenu = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(FICHIER_JSON, $contenu);
}
