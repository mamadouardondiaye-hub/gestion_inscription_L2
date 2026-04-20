<?php

/**
 * ============================================================
 *  etudiants.php — Toutes les fonctions liées aux étudiants
 * ============================================================
 *  Fonctions disponibles :
 *   A) ajouterEtudiant()
 *   B) modifierEtudiant()
 *   C) supprimerEtudiant()
 *   D) listerEtudiants()
 */

require_once 'database.php';
require_once 'helpers.php';


// ============================================================
//  A) Ajouter un étudiant
// ============================================================
function ajouterEtudiant(): void
{
    afficherTitre("Ajouter un étudiant");

    $data = lireData();

    $nom    = lireSaisie("Nom       : ");
    $prenom = lireSaisie("Prénom    : ");
    $email  = lireSaisie("Email     : ");

    // Vérification : champs obligatoires
    if (empty($nom) || empty($prenom) || empty($email)) {
        afficherErreur("Le nom, le prénom et l'email sont obligatoires.");
        return;
    }

    // Vérification : email unique (insensible à la casse)
    foreach ($data['etudiants'] as $etudiant) {
        if (strtolower($etudiant['email']) === strtolower($email)) {
            afficherErreur("Cet email est déjà utilisé par un autre étudiant.");
            return;
        }
    }

    // Création du nouvel étudiant
    $nouvelEtudiant = [
        'id_etudiant' => genererIdEtudiant($data),
        'nom'         => $nom,
        'prenom'      => $prenom,
        'email'       => $email
    ];

    $data['etudiants'][] = $nouvelEtudiant;
    sauvegarderData($data);

    afficherSucces("Étudiant \"$prenom $nom\" ajouté avec l'ID " . $nouvelEtudiant['id_etudiant'] . ".");
}


// ============================================================
//  B) Modifier un étudiant
// ============================================================
function modifierEtudiant(): void
{
    afficherTitre("Modifier un étudiant");

    $data = lireData();

    if (empty($data['etudiants'])) {
        afficherErreur("Aucun étudiant enregistré.");
        return;
    }

    listerEtudiants();

    $id = (int) lireSaisie("\nEntrez l'ID de l'étudiant à modifier : ");

    // On cherche l'INDEX de l'étudiant (évite les bugs de référence &)
    $index = -1;
    foreach ($data['etudiants'] as $i => $etudiant) {
        if ($etudiant['id_etudiant'] === $id) {
            $index = $i;
            break;
        }
    }

    if ($index === -1) {
        afficherErreur("Aucun étudiant avec l'ID $id.");
        return;
    }

    echo "\n(Laissez vide pour garder la valeur actuelle)\n";

    // Modification directe via l'index dans le tableau
    $data['etudiants'][$index]['nom']    = lireSaisie("Nom       [" . $data['etudiants'][$index]['nom'] . "] : ",    $data['etudiants'][$index]['nom']);
    $data['etudiants'][$index]['prenom'] = lireSaisie("Prénom    [" . $data['etudiants'][$index]['prenom'] . "] : ", $data['etudiants'][$index]['prenom']);
    $data['etudiants'][$index]['email']  = lireSaisie("Email     [" . $data['etudiants'][$index]['email'] . "] : ",  $data['etudiants'][$index]['email']);

    sauvegarderData($data);
    afficherSucces("Étudiant modifié avec succès.");
}


// ============================================================
//  C) Supprimer un étudiant
// ============================================================
function supprimerEtudiant(): void
{
    afficherTitre("Supprimer un étudiant");

    $data = lireData();

    if (empty($data['etudiants'])) {
        afficherErreur("Aucun étudiant enregistré.");
        return;
    }

    listerEtudiants();

    $id = (int) lireSaisie("\nEntrez l'ID de l'étudiant à supprimer : ");

    // On vérifie que l'étudiant existe AVANT de demander confirmation
    $etudiantASupprimer = null;
    foreach ($data['etudiants'] as $etudiant) {
        if ($etudiant['id_etudiant'] === $id) {
            $etudiantASupprimer = $etudiant;
            break;
        }
    }

    if ($etudiantASupprimer === null) {
        afficherErreur("Aucun étudiant avec l'ID $id.");
        return;
    }

    echo "Étudiant : " . $etudiantASupprimer['prenom'] . " " . $etudiantASupprimer['nom'] . "\n";

    // Confirmation avant suppression
    $confirmation = lireSaisie("Confirmer la suppression ? (oui/non) : ");
    if (strtolower($confirmation) !== 'oui') {
        echo "Suppression annulée.\n";
        return;
    }

    // Suppression de l'étudiant
    $data['etudiants'] = array_values(array_filter(
        $data['etudiants'],
        fn($e) => $e['id_etudiant'] !== $id
    ));

    // Suppression aussi de ses inscriptions (cohérence des données)
    $data['inscriptions'] = array_values(array_filter(
        $data['inscriptions'],
        fn($i) => $i['id_etudiant'] !== $id
    ));

    sauvegarderData($data);
    afficherSucces("Étudiant supprimé avec succès.");
}


// ============================================================
//  D) Lister les étudiants
// ============================================================
function listerEtudiants(): void
{
    afficherTitre("Liste des étudiants");

    $data = lireData();

    if (empty($data['etudiants'])) {
        echo "Aucun étudiant enregistré.\n";
        return;
    }

    printf("%-5s %-20s %-20s %-30s\n", "ID", "NOM", "PRÉNOM", "EMAIL");
    separateur();

    foreach ($data['etudiants'] as $etudiant) {
        printf(
            "%-5s %-20s %-20s %-30s\n",
            $etudiant['id_etudiant'],
            $etudiant['nom'],
            $etudiant['prenom'],
            $etudiant['email']
        );
    }

    echo "\nTotal : " . count($data['etudiants']) . " étudiant(s).\n";
}
