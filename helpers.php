<?php

/**
 * ============================================================
 *  helpers.php — Fonctions utilitaires pour la console
 * ============================================================
 *  Ce fichier contient des petites fonctions réutilisables
 *  pour afficher des messages et lire les saisies de l'utilisateur.
 */

/**
 * Affiche une ligne de séparation dans la console.
 */
function separateur(): void
{
    echo str_repeat("-", 50) . "\n";
}

/**
 * Affiche un titre encadré.
 */
function afficherTitre(string $titre): void
{
    echo "\n";
    separateur();
    echo "  " . strtoupper($titre) . "\n";
    separateur();
}

/**
 * Demande une saisie à l'utilisateur et retourne ce qu'il a tapé.
 * Si la saisie est vide, retourne la valeur par défaut.
 */
function lireSaisie(string $message, string $defaut = ""): string
{
    echo $message;
    $saisie = trim(fgets(STDIN));

    // Si l'utilisateur n'a rien tapé, on garde la valeur actuelle
    if ($saisie === "" && $defaut !== "") {
        return $defaut;
    }

    return $saisie;
}

/**
 * Demande à l'utilisateur de choisir une option dans un menu.
 * Retourne le choix sous forme de chaîne (ex: "1", "A", "B"...)
 */
function lireChoix(string $message = "Votre choix : "): string
{
    echo "\n" . $message;
    return strtoupper(trim(fgets(STDIN)));
}

/**
 * Affiche un message de succès (avec un [OK] devant).
 */
function afficherSucces(string $message): void
{
    echo "\n[OK] " . $message . "\n";
}

/**
 * Affiche un message d'erreur (avec un [ERREUR] devant).
 */
function afficherErreur(string $message): void
{
    echo "\n[ERREUR] " . $message . "\n";
}

/**
 * Génère un identifiant unique simple basé sur le timestamp.
 * Exemple : retourne 1, 2, 3... en fonction des données existantes.
 */
function genererIdEtudiant(array $data): int
{
    if (empty($data['etudiants'])) {
        return 1;
    }
    // On prend le plus grand ID existant et on ajoute 1
    $ids = array_column($data['etudiants'], 'id_etudiant');
    return max($ids) + 1;
}

function genererIdFormation(array $data): int
{
    if (empty($data['formations'])) {
        return 1;
    }
    $ids = array_column($data['formations'], 'id_formation');
    return max($ids) + 1;
}

function genererIdInscription(array $data): int
{
    if (empty($data['inscriptions'])) {
        return 1;
    }
    $ids = array_column($data['inscriptions'], 'id_inscription');
    return max($ids) + 1;
}

/**
 * Appuyer sur Entrée pour continuer.
 */
function appuyerPourContinuer(): void
{
    echo "\nAppuyez sur Entrée pour continuer...";
    fgets(STDIN);
}
