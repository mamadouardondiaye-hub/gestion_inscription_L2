<?php

/**
 * ============================================================
 *  formations.php — Toutes les fonctions liées aux formations
 * ============================================================
 *  Fonctions disponibles :
 *   E) creerFormation()
 *   F) modifierFormation()
 *   G) supprimerFormation()
 *   H) listerFormations()
 *   I) trierParFormation()
 */

require_once 'database.php';
require_once 'helpers.php';


// ============================================================
//  E) Créer une formation
// ============================================================
function creerFormation(): void
{
    afficherTitre("Créer une formation");

    $data = lireData();

    $titre       = lireSaisie("Titre       : ");
    $description = lireSaisie("Description : ");

    // Le titre est obligatoire
    if (empty($titre)) {
        afficherErreur("Le titre est obligatoire.");
        return;
    }

    $nouvelleFormation = [
        'id_formation' => genererIdFormation($data),
        'titre'        => $titre,
        'description'  => $description
    ];

    $data['formations'][] = $nouvelleFormation;
    sauvegarderData($data);

    afficherSucces("Formation créée avec l'ID " . $nouvelleFormation['id_formation'] . ".");
}


// ============================================================
//  F) Modifier une formation
// ============================================================
function modifierFormation(): void
{
    afficherTitre("Modifier une formation");

    $data = lireData();

    if (empty($data['formations'])) {
        afficherErreur("Aucune formation enregistrée.");
        return;
    }

    listerFormations();

    $id = (int) lireSaisie("\nEntrez l'ID de la formation à modifier : ");

    // On cherche l'INDEX de la formation (évite les bugs de référence &)
    $index = -1;
    foreach ($data['formations'] as $i => $formation) {
        if ($formation['id_formation'] === $id) {
            $index = $i;
            break;
        }
    }

    if ($index === -1) {
        afficherErreur("Aucune formation avec l'ID $id.");
        return;
    }

    echo "\n(Laissez vide pour garder la valeur actuelle)\n";

    // Modification directe via l'index
    $data['formations'][$index]['titre']       = lireSaisie("Titre       [" . $data['formations'][$index]['titre'] . "] : ",       $data['formations'][$index]['titre']);
    $data['formations'][$index]['description'] = lireSaisie("Description [" . $data['formations'][$index]['description'] . "] : ", $data['formations'][$index]['description']);

    sauvegarderData($data);
    afficherSucces("Formation modifiée avec succès.");
}


// ============================================================
//  G) Supprimer une formation
// ============================================================
function supprimerFormation(): void
{
    afficherTitre("Supprimer une formation");

    $data = lireData();

    if (empty($data['formations'])) {
        afficherErreur("Aucune formation enregistrée.");
        return;
    }

    listerFormations();

    $id = (int) lireSaisie("\nEntrez l'ID de la formation à supprimer : ");

    // On vérifie que la formation existe AVANT de demander confirmation
    $formationASupprimer = null;
    foreach ($data['formations'] as $formation) {
        if ($formation['id_formation'] === $id) {
            $formationASupprimer = $formation;
            break;
        }
    }

    if ($formationASupprimer === null) {
        afficherErreur("Aucune formation avec l'ID $id.");
        return;
    }

    echo "Formation : " . $formationASupprimer['titre'] . "\n";

    $confirmation = lireSaisie("Confirmer la suppression ? (oui/non) : ");
    if (strtolower($confirmation) !== 'oui') {
        echo "Suppression annulée.\n";
        return;
    }

    // Suppression de la formation
    $data['formations'] = array_values(array_filter(
        $data['formations'],
        fn($f) => $f['id_formation'] !== $id
    ));

    // Suppression aussi des inscriptions liées à cette formation
    $data['inscriptions'] = array_values(array_filter(
        $data['inscriptions'],
        fn($i) => $i['id_formation'] !== $id
    ));

    sauvegarderData($data);
    afficherSucces("Formation supprimée avec succès.");
}


// ============================================================
//  H) Lister les formations
// ============================================================
function listerFormations(): void
{
    afficherTitre("Liste des formations");

    $data = lireData();

    if (empty($data['formations'])) {
        echo "Aucune formation enregistrée.\n";
        return;
    }

    printf("%-5s %-30s %-40s\n", "ID", "TITRE", "DESCRIPTION");
    separateur();

    foreach ($data['formations'] as $formation) {
        printf(
            "%-5s %-30s %-40s\n",
            $formation['id_formation'],
            $formation['titre'],
            $formation['description']
        );
    }

    echo "\nTotal : " . count($data['formations']) . " formation(s).\n";
}


// ============================================================
//  I) Trier les formations par titre (ordre alphabétique)
// ============================================================
function trierParFormation(): void
{
    afficherTitre("Formations triées par titre");

    $data = lireData();

    if (empty($data['formations'])) {
        echo "Aucune formation enregistrée.\n";
        return;
    }

    // On copie le tableau pour ne pas modifier l'ordre dans le JSON
    $formations = $data['formations'];

    // Tri alphabétique sur le titre
    usort($formations, fn($a, $b) => strcmp($a['titre'], $b['titre']));

    printf("%-5s %-30s %-40s\n", "ID", "TITRE", "DESCRIPTION");
    separateur();

    foreach ($formations as $formation) {
        printf(
            "%-5s %-30s %-40s\n",
            $formation['id_formation'],
            $formation['titre'],
            $formation['description']
        );
    }
}
