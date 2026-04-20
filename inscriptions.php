<?php

/**
 * ============================================================
 *  inscriptions.php — Toutes les fonctions liées aux inscriptions
 * ============================================================
 *  Fonctions disponibles :
 *   (Etudiant)  A) consulterFormations()
 *               B) faireDemandeInscription()
 *   (Gestionnaire) J) listerDemandes()
 *                  K) validerDemande()
 */

require_once 'database.php';
require_once 'helpers.php';


// ============================================================
//  A) Consulter les formations disponibles (vue étudiant)
// ============================================================
function consulterFormations(): void
{
    afficherTitre("Formations disponibles");

    $data = lireData();

    if (empty($data['formations'])) {
        echo "Aucune formation disponible pour le moment.\n";
        return;
    }

    foreach ($data['formations'] as $formation) {
        echo "\n[ID: " . $formation['id_formation'] . "] " . $formation['titre'] . "\n";
        if (!empty($formation['description'])) {
            echo "  => " . $formation['description'] . "\n";
        }
    }
}


// ============================================================
//  B) Faire une demande d'inscription (vue étudiant)
// ============================================================
function faireDemandeInscription(): void
{
    afficherTitre("Faire une demande d'inscription");

    $data = lireData();

    if (empty($data['formations'])) {
        afficherErreur("Aucune formation disponible.");
        return;
    }

    // On demande l'email pour identifier l'étudiant
    $email = lireSaisie("Votre email : ");

    // On cherche l'étudiant par son email
    $etudiantTrouve = null;
    foreach ($data['etudiants'] as $etudiant) {
        if ($etudiant['email'] === $email) {
            $etudiantTrouve = $etudiant;
            break;
        }
    }

    if ($etudiantTrouve === null) {
        afficherErreur("Aucun étudiant trouvé avec cet email. Contactez l'administrateur.");
        return;
    }

    echo "\nBonjour " . $etudiantTrouve['prenom'] . " " . $etudiantTrouve['nom'] . " !\n";

    // On affiche les formations disponibles
    consulterFormations();

    $idFormation = (int) lireSaisie("\nEntrez l'ID de la formation souhaitée : ");

    // On vérifie que la formation existe
    $formationTrouvee = null;
    foreach ($data['formations'] as $formation) {
        if ($formation['id_formation'] === $idFormation) {
            $formationTrouvee = $formation;
            break;
        }
    }

    if ($formationTrouvee === null) {
        afficherErreur("Formation introuvable.");
        return;
    }

    // On vérifie qu'il n'y a pas déjà une demande en cours pour cette formation
    foreach ($data['inscriptions'] as $inscription) {
        if (
            $inscription['id_etudiant'] === $etudiantTrouve['id_etudiant'] &&
            $inscription['id_formation'] === $idFormation
        ) {
            afficherErreur("Vous avez déjà une demande pour cette formation (statut : " . $inscription['statut'] . ").");
            return;
        }
    }

    // Création de l'inscription avec le statut EN_ATTENTE
    $nouvelleInscription = [
        'id_inscription'   => genererIdInscription($data),
        'id_etudiant'      => $etudiantTrouve['id_etudiant'],
        'id_formation'     => $idFormation,
        'date_inscription' => date('Y-m-d'),
        'statut'           => 'EN_ATTENTE'
    ];

    $data['inscriptions'][] = $nouvelleInscription;
    sauvegarderData($data);

    afficherSucces(
        "Demande envoyée pour la formation \"" . $formationTrouvee['titre'] . "\". " .
        "Statut : EN ATTENTE de validation."
    );
}


// ============================================================
//  J) Lister toutes les demandes d'inscription (vue gestionnaire)
// ============================================================
function listerDemandes(): void
{
    afficherTitre("Liste des demandes d'inscription");

    $data = lireData();

    if (empty($data['inscriptions'])) {
        echo "Aucune demande d'inscription.\n";
        return;
    }

    printf("%-5s %-25s %-30s %-15s %-12s\n", "ID", "ÉTUDIANT", "FORMATION", "STATUT", "DATE");
    separateur();

    foreach ($data['inscriptions'] as $inscription) {
        // On cherche le nom de l'étudiant
        $nomEtudiant = "Inconnu";
        foreach ($data['etudiants'] as $etudiant) {
            if ($etudiant['id_etudiant'] === $inscription['id_etudiant']) {
                $nomEtudiant = $etudiant['prenom'] . " " . $etudiant['nom'];
                break;
            }
        }

        // On cherche le titre de la formation
        $titreFormation = "Inconnue";
        foreach ($data['formations'] as $formation) {
            if ($formation['id_formation'] === $inscription['id_formation']) {
                $titreFormation = $formation['titre'];
                break;
            }
        }

        printf(
            "%-5s %-25s %-30s %-15s %-12s\n",
            $inscription['id_inscription'],
            $nomEtudiant,
            $titreFormation,
            $inscription['statut'],
            $inscription['date_inscription']
        );
    }

    echo "\nTotal : " . count($data['inscriptions']) . " demande(s).\n";
}


// ============================================================
//  K) Valider ou rejeter une demande (vue gestionnaire)
// ============================================================
function validerDemande(): void
{
    afficherTitre("Valider une demande d'inscription");

    $data = lireData();

    if (empty($data['inscriptions'])) {
        afficherErreur("Aucune demande d'inscription.");
        return;
    }

    // On affiche d'abord toutes les demandes
    listerDemandes();

    $id = (int) lireSaisie("\nEntrez l'ID de la demande à traiter : ");

    // On cherche l'INDEX de l'inscription (évite les bugs de référence &)
    $index = -1;
    foreach ($data['inscriptions'] as $i => $inscription) {
        if ($inscription['id_inscription'] === $id) {
            $index = $i;
            break;
        }
    }

    if ($index === -1) {
        afficherErreur("Aucune demande avec l'ID $id.");
        return;
    }

    echo "Statut actuel : " . $data['inscriptions'][$index]['statut'] . "\n";
    echo "\nQue voulez-vous faire ?\n";
    echo "  1) Accepter\n";
    echo "  2) Rejeter\n";

    $choix = lireChoix();

    if ($choix === '1') {
        $data['inscriptions'][$index]['statut'] = 'ACCEPTE';
        sauvegarderData($data);
        afficherSucces("Demande acceptée.");
    } elseif ($choix === '2') {
        $data['inscriptions'][$index]['statut'] = 'REJETE';
        sauvegarderData($data);
        afficherSucces("Demande rejetée.");
    } else {
        echo "Choix invalide. Aucune modification.\n";
    }
}
