<?php

/**
 * ============================================================
 *  index.php — Point d'entrée de l'application
 * ============================================================
 *  C'est le fichier à lancer avec : php index.php
 *
 *  Architecture du projet :
 *   index.php         → menus principaux (ce fichier)
 *   database.php      → lecture/écriture du fichier JSON
 *   helpers.php       → fonctions utilitaires (affichage, saisie)
 *   etudiants.php     → gestion des étudiants (CRUD)
 *   formations.php    → gestion des formations (CRUD)
 *   inscriptions.php  → gestion des inscriptions
 *   data/data.json    → notre base de données
 */

require_once 'helpers.php';
require_once 'etudiants.php';
require_once 'formations.php';
require_once 'inscriptions.php';


// ============================================================
//  MENU PRINCIPAL
// ============================================================
function menuPrincipal(): void
{
    while (true) {
        afficherTitre("École 221 — Gestion des Inscriptions");

        echo "  1) Espace Étudiant\n";
        echo "  2) Espace Gestionnaire\n";
        echo "  0) Quitter\n";

        $choix = lireChoix();

        switch ($choix) {
            case '1':
                menuEtudiant();
                break;
            case '2':
                menuGestionnaire();
                break;
            case '0':
                echo "\nAu revoir !\n\n";
                exit(0); // On quitte le programme
            default:
                afficherErreur("Choix invalide. Tapez 1, 2 ou 0.");
        }
    }
}


// ============================================================
//  MENU ÉTUDIANT
// ============================================================
function menuEtudiant(): void
{
    while (true) {
        afficherTitre("Espace Étudiant");

        echo "  A) Consulter les formations\n";
        echo "  B) Faire une demande d'inscription\n";
        echo "  0) Retour au menu principal\n";

        $choix = lireChoix();

        switch ($choix) {
            case 'A':
                consulterFormations();
                appuyerPourContinuer();
                break;
            case 'B':
                faireDemandeInscription();
                appuyerPourContinuer();
                break;
            case '0':
                return; // On revient au menu précédent
            default:
                afficherErreur("Choix invalide. Tapez A, B ou 0.");
        }
    }
}


// ============================================================
//  MENU GESTIONNAIRE
// ============================================================
function menuGestionnaire(): void
{
    while (true) {
        afficherTitre("Espace Gestionnaire");

        echo "  --- Gestion des étudiants ---\n";
        echo "  A) Ajouter un étudiant\n";
        echo "  B) Modifier un étudiant\n";
        echo "  C) Supprimer un étudiant\n";
        echo "  D) Lister les étudiants\n";
        echo "\n";
        echo "  --- Gestion des formations ---\n";
        echo "  E) Créer une formation\n";
        echo "  F) Modifier une formation\n";
        echo "  G) Supprimer une formation\n";
        echo "  H) Lister les formations\n";
        echo "  I) Trier par formation\n";
        echo "\n";
        echo "  --- Gestion des inscriptions ---\n";
        echo "  J) Lister les demandes d'inscription\n";
        echo "  K) Valider une demande d'inscription\n";
        echo "\n";
        echo "  0) Retour au menu principal\n";

        $choix = lireChoix();

        switch ($choix) {
            // --- Étudiants ---
            case 'A': ajouterEtudiant();     appuyerPourContinuer(); break;
            case 'B': modifierEtudiant();    appuyerPourContinuer(); break;
            case 'C': supprimerEtudiant();   appuyerPourContinuer(); break;
            case 'D': listerEtudiants();     appuyerPourContinuer(); break;

            // --- Formations ---
            case 'E': creerFormation();      appuyerPourContinuer(); break;
            case 'F': modifierFormation();   appuyerPourContinuer(); break;
            case 'G': supprimerFormation();  appuyerPourContinuer(); break;
            case 'H': listerFormations();    appuyerPourContinuer(); break;
            case 'I': trierParFormation();   appuyerPourContinuer(); break;

            // --- Inscriptions ---
            case 'J': listerDemandes();      appuyerPourContinuer(); break;
            case 'K': validerDemande();      appuyerPourContinuer(); break;

            case '0': return;
            default:  afficherErreur("Choix invalide.");
        }
    }
}


// ============================================================
//  LANCEMENT DE L'APPLICATION
// ============================================================
menuPrincipal();
