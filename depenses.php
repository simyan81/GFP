<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


  // Obtient quelques valeurs
    $compte_id = ObtenirValeur('compte_id', -1);
    $action = ObtenirValeur('action', 'liste');
    $defilerauid = ObtenirValeur('defilerauid', -1);


  // Initialise quelques valeurs
    $modifier_depense_id = -1;
    $effacer_depense_id = -1;
    $ajouter_type_transaction_id = $type_transaction_normale;
    $ajouter_transfert_compte_id = 0;
    $ajouter_date_depense = $maintenant;
    $ajouter_type_depense_id = 0;
    $ajouter_description = "";
    $ajouter_symbole = 1;
    $ajouter_montant = '';
    $ajouter_notes = '';


  // Afficher jusqu'a quel date
    $afficher = ObtenirValeur('afficher', '');
    if ($afficher == '') {
      if ( isset($_SESSION['afficher']) ) {
        $afficher = $_SESSION['afficher'];
      }
    }

    if (strlen($afficher) > 1) {
      $afficher_int = substr($afficher, 1);
      if ( is_numeric($afficher_int) ) {
        if ($afficher_int < 1) { $afficher_int = 1; }
        if ($afficher_int > 24) { $afficher_int = 24; }

        $premierJourDuMoisSuivant = date("Y-m-01", strtotime("+1 month", $maintenant_unix));
        $premierJourPlusMois = date('Y-m-d', strtotime("+" . $afficher_int . " month", strtotime($premierJourDuMoisSuivant)));
        $dernierJourPlusMois = date('Y-m-d', strtotime("-1 day", strtotime($premierJourPlusMois)));
        $afficherjusquau = $dernierJourPlusMois;
        $_SESSION['afficher'] = $afficher;
      } else {
        // Valeur non numerique
      }
    }


  // Afficher jusqu'a quel date
  $afficher_journee_vide = false;
  $afficher_journee_vide_temp = ObtenirValeur('afficher_journee_vide', '');
  if ($afficher_journee_vide_temp == '') {
    if ( isset($_SESSION['afficher_journee_vide']) ) {
      $afficher_journee_vide = $_SESSION['afficher_journee_vide'];
    }
  } elseif ($afficher_journee_vide_temp == '1') {
    $afficher_journee_vide = true;
    $_SESSION['afficher_journee_vide'] = $afficher_journee_vide;
  } else { // if ($afficher_journee_vide_temp == '0') {
    $afficher_journee_vide = false;
    $_SESSION['afficher_journee_vide'] = $afficher_journee_vide;
  }


  // Verifie que le compte existe
    $compte = array();
    if ( isset ($liste_compte[ $compte_id] ) === false ) {
      $action = '';
      $erreur_msg .= 'Compte invalide.<br />';
      $compte_id == -1;
    } else {
      $compte = $liste_compte[ $compte_id];
    }


  // Cree un nonce pour les formulaires
    $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_depenses', 10), true);


  // Verifie les pre-action
    switch ($action) {
      case "ajouterdepense":
        // Obtient quelques valeurs
          $ajouter = true;
          $nonce = ObtenirValeur('nonce', '');
          $date_depense = ObtenirValeur("ajouter_date_depense", '');
          $type_transaction_id = ObtenirValeur("ajouter_type_transaction_id", 0);
          $type_depense_id = ObtenirValeur("ajouter_type_depense_id", 0);
          $description = ObtenirValeur("ajouter_description", "");
          $montant = ObtenirValeur("ajouter_montant", 0);
          $symbole = ObtenirValeur("ajouter_symbole", 1);
          $transfert_compte_id = ObtenirValeur("transfert_compte_id", 0);
          $notes = ObtenirValeur('ajouter_notes', '');
          $recurrence_id = 0;
        // Verifie le nonce
          $valide = true;
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Ajout la depense dans la DB
          if ($valide) {
            $errmsg = '';
            $ligne_virtuel = false;
            $bRet = ajouterDepense($errmsg, $ligne_virtuel, $compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $transfert_compte_id, $recurrence_id);
            if ($bRet == false) {
              $erreur_msg .= "Impossible d'ajouter. " . $errmsg . '<br />';
              $ajouter_type_transaction_id = $type_transaction_id;
              $ajouter_transfert_compte_id = $transfert_compte_id;
              $ajouter_date_depense = $date_depense;
              $ajouter_type_depense_id = $type_depense_id;
              $ajouter_description = $description;
              $ajouter_symbole = $symbole;
              $ajouter_montant = $montant;
              $ajouter_notes = $notes;
            } else {
              $ajout_msg .= 'D&eacute;pense ajouter.<br />';
              $ajouter_date_depense = $date_depense;
            }
          }
        // Retourne a la liste
          $action = "liste";
        break;

      case 'depense_modifier_confirmer':
        // Obtient quelques valeurs
          //modifier_type_transaction_id
          //modifier_transfert_compte_id
          $nonce = ObtenirValeur('nonce', '');
          $depense_id = ObtenirValeur('modifier_depense_id', 0);
          $date_depense = ObtenirValeur('modifier_date_depense', $maintenant);
          $type_depense_id = ObtenirValeur('modifier_type_depense_id', 0);
          $description = ObtenirValeur('modifier_description', 0);
          $symbole = ObtenirValeur('modifier_symbole', 0);
          $montant = abs(ObtenirValeur('modifier_montant', 0));
          $notes = ObtenirValeur('modifier_notes', '');
          $recurrence_id = null;
          $transfert_compte_id = null;
          $errmsg = '';
          $valide = true;
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Modifie la depense dans la DB
          if ($valide) {
            $bRet = modifierDepense($errmsg, $depense_id, $recurrence_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $transfert_compte_id);
            if ($bRet == false) {
              $erreur_msg .= 'Impossible de mettre &agrave; jour. ' . $errmsg . '<br />';
            } else {
              $ajout_msg .= 'D&eacute;pense modifier.<br />';
            }
          }
        // Reinitialise quelques variables
          $modifier_depense_id = -1;
        // Retourne a la liste
          $action = "liste";
        break;

      case 'depense_effacer_confirmer':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $depense_id = ObtenirValeur('effacer_depense_id', 0);
        // Verifie que la depense existe
          $valide = true;
          if (!obtientEntreeDepense($ajout_msg, $ligne, $depense_id)) {
            $valide = false;
          }
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Requete SQL
          if ($valide) {
            $sql  = 'UPDATE depenses ';
            $sql .= '  SET est_effacer = 1 ';
            $sql .= ' WHERE id = ' . $depense_id;
            if (depense_id_est_valide($ligne['tr_id'])) {
              $sql .= ' OR id = ' . $ligne['tr_id'];
            }
            // Execute requete SQL
              $requete_resultat = mysqli_query($mysql_conn, $sql);
              if (!$requete_resultat) {
                die('Requ&ecirc;te invalide (depense_effacer_confirmer) : ' . mysqli_error($mysql_conn));
              }
              $ajout_msg .= 'D&eacute;pense effacer.<br />';
          }
        // Reinitialise quelques variables
          $effacer_depense_id = -1;
        // Retourne a la liste
          $action = "liste";
        break;





      case "reconcilier":
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $reconcilier = ObtenirValeur('liste_reconcilier_id', '');
        // Valide quelques valeurs
          if ($reconcilier == '') {
            $erreur_msg .= "Il n'y a rien de s&eacute;lectionner.<br />";
            $action = 'liste';
            break;
          }
        // Convertir en array
          $liste_id = explode (",", $reconcilier);
        // Verifie le array
          if (!is_array($liste_id)) {
            $erreur_msg .= "Le format est invalide.<br />";
            $action = 'liste';
            break;
          }
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $action = 'liste';
            break;
          }

        // Verifie que les valeurs sont numerique
         foreach ($liste_id as $valeur) {
           if (!is_numeric($valeur)) {
             $erreur_msg .= "Le format n'est pas num&eacute;rique.<br />";
             break 2;
           }
         }
        // Verifie le compte
          if (count($liste_id) < 2) {
            $erreur_msg .= "Vous devez s&eacute;lectionner plus d'une entr&eacute;e.<br />";
            $action = 'liste';
            break;
          }
        // Requete SQL pour avoir la sommes totale
          $sql = "";
          $sql .= "SELECT (SELECT SUM(montant)";
          $sql .= "          FROM depenses";
          $sql .= "         WHERE id IN (" . $reconcilier . ") ";
          $sql .= "           AND montant >= 0) AS entre,";
          $sql .= "       (SELECT SUM(montant)";
          $sql .= "          FROM depenses";
          $sql .= "         WHERE id IN (" . $reconcilier . ") ";
          $sql .= "           AND montant < 0) AS sortie, ";
          $sql .= "       (SELECT date_depense ";
          $sql .= "          FROM depenses";
          $sql .= "         WHERE id IN (" . $reconcilier . ") ";
          $sql .= "         ORDER BY date_depense DESC ";
          $sql .= "         LIMIT 0, 1) AS last_date ";
          $sql .= "  FROM depenses ";
          $sql .= " WHERE id IN (" . $reconcilier . ") ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (reconcilier:1) : ' . mysqli_error($mysql_conn));
          }
          $ligne = mysqli_fetch_assoc($requete_resultat);
          $entre = $ligne['entre'];
          $sortie = $ligne['sortie'];
          $solde = $entre + $sortie;
          $symbole = ($solde < 0 ? 0 : 1);
          $solde = abs($solde);
          $date_depense = $ligne['last_date'];
          mysqli_free_result($requete_resultat);
          $description = $liste_type_transaction[$type_transaction_reconciliation]['description'];
        // Ajout nouvelle depense de type reconciliation dans la DB
          $errmsg = '';
          $ligne_virtuel = false;
          $bRet = ajouterDepense($errmsg, $ligne_virtuel, $compte_id, $type_transaction_reconciliation, $date_depense, 0, $description, '', $solde, $symbole, 0, 0);
          if ($bRet == false) {
            $erreur_msg .= "Impossible d'ajouter la r&eacute;conciliation. " . $errmsg . '<br />';
          } else {
            $newid =  mysqli_insert_id($mysql_conn);
            // Modifie les depense reconcilier
              $sql = "UPDATE depenses SET est_effacer = 0, reconcilier_depense_id = $newid WHERE id IN (" . $reconcilier . ") ";
              $requete_resultat = mysqli_query($mysql_conn, $sql);
              if (!$requete_resultat) {
                die('Requ&ecirc;te invalide (reconcilier:2) : ' . mysqli_error($mysql_conn));
              }
            // Message
              $ajout_msg .= "Les entr&eacute;es on &eacute;t&eacute; r&eacute;concilier pour un solde de " . formatMonnaie($solde, $symbole) . '<br />';
          }
        // Retourne a la liste
          $action = "liste";
          break;

      case "pointer":
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $pointer = ObtenirValeur('liste_pointer_id', '');
        // Valide quelques valeurs
          if ($pointer == '') {
            $erreur_msg .= "Il n'y a rien de s&eacute;lectionner.<br />";
            $action = 'liste';
            break;
          }
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $action = 'liste';
            break;
          }
        // Convertie en array
          $liste_id = explode (",", $pointer);
        // Verifie le array
          if (!is_array($liste_id)) {
            $erreur_msg .= "Le format est invalide.<br />";
            $action = 'liste';
            break;
          }
        // Verifie que les valeurs sont numerique
         foreach ($liste_id as $valeur) {
           if (!is_numeric($valeur)) {
             $erreur_msg .= "Le format n'est pas num&eacute;rique.<br />";
             break 2;
           }
         }
        // Verifie le compte
          if (count($liste_id) < 1) {
            $erreur_msg .= "Vous devez s&eacute;lectionner au moins une entr&eacute;e.<br />";
            $action = 'liste';
            break;
          }
        // Modifie les depenses
          $sql = "UPDATE depenses SET pointer = CASE WHEN pointer = 0 THEN 1 ELSE 0 END WHERE id IN (" . $pointer . ") ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (pointer:1) : ' . mysqli_error($mysql_conn));
          }
        // Message
          $ajout_msg .= "Les entr&eacute;es on &eacute;t&eacute; pointer.<br />";
        // Retourne a la liste
          $action = "liste";
          break;



      case 'depense_effacer':
      case 'depense_modifier':
        // Obtiens quelques valeurs
          $modifier_depense_id = ObtenirValeur('modifier_depense_id', -1);
          $effacer_depense_id = ObtenirValeur('effacer_depense_id', -1);
        // Valide quelques valeurs
          if (is_numeric($modifier_depense_id) && $action == 'depense_modifier') {
            $depense_id = $modifier_depense_id;
          } elseif (is_numeric($effacer_depense_id) && $action == 'depense_effacer') {
            $depense_id = $effacer_depense_id;
          } else {
            $depense_id = -1;
          }
        // Verifie si la depense existe
          $sql = "SELECT * FROM depenses WHERE id = " . $depense_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (depense_effacer|depense_modifier) : ' . mysqli_error($mysql_conn));
          }
          $c = mysqli_num_rows($requete_resultat);
          if ($c != 1) {
            $depense_id = -1;
          }
        // Initialise quelques valeurs
          if ($action == 'depense_modifier') {
            $defilerauid = $depense_id;
            $modifier_depense_id = $depense_id;
            $effacer_depense_id = -1;
          } elseif ($action == 'depense_effacer') {
            $defilerauid = $depense_id;
            $effacer_depense_id = $depense_id;
            $modifier_depense_id = -1;
          } else {
            $depense_id = -1;
            $modifier_depense_id = $depense_id;
            $effacer_depense_id = $depense_id;
          }
        // Retourne a la liste
          $action = 'liste';
        break;

    } // Fin des pre-action


  // Enleve quelques valeurs dans le URL
    $url_actuelle = FunctionURLChange($url_actuelle, 'action', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'effacer_depense_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'modifier_depense_id', '' );


  // Verifie les actions
    switch ($action) {
      case "vuereconciliationreference":
        // Obtient quelques valeurs
          $valide = true;
          $depense_id = ObtenirValeur('depense_id', -1);
        // Valide quelques valeurs
          if (!is_numeric($depense_id)) {
            $erreur_msg .= 'D&eacute;pense introuvable.<br />';
            $valide = false;
          } elseif ($depense_id < 1) {
            $erreur_msg .= 'D&eacute;pense introuvable.<br />';
            $valide = false;
          }
        // Requete SQL
          if ($valide == true) {
            $sql = "";
            $sql .= "SELECT depenses.*, ";
            $sql .= "       IfNull(comptes.description, '') AS compte_description, ";
            $sql .= "       IfNull(type_depense.description, '') AS type_depense_description, ";
            $sql .= "       IfNull(type_transaction.description, '') AS type_transaction_description, ";
            $sql .= "       IfNull(tr_depense.id, -1) AS 'tr_id', ";
            $sql .= "       IfNull(tr_depense.compte_id, -1) AS 'tr_compte_id', ";
            $sql .= "       IfNull(tr_compte.description, '') AS 'tr_compte_description', ";
            $sql .= "       IfNull(tr_depense.reconcilier_depense_id, -1) AS 'tr_reconcilier_depense_id' ";
            $sql .= "  FROM depenses AS depenses";
            $sql .= "  LEFT JOIN comptes ON comptes.id = depenses.compte_id ";
            $sql .= "  LEFT JOIN type_depense ON type_depense.id = depenses.type_depense_id ";
            $sql .= "  LEFT JOIN type_transaction ON type_transaction.id = depenses.type_transaction_id ";
            $sql .= "  LEFT JOIN depenses AS tr_depense ON tr_depense.id = depenses.transfert_compte_id ";
            $sql .= "  LEFT JOIN comptes AS tr_compte ON tr_compte.id = tr_depense.compte_id ";
            $sql .= " WHERE depenses.compte_id = " . $compte_id . " ";
            $sql .= "   AND depenses.est_effacer = 0 ";
            $sql .= "   AND depenses.reconcilier_depense_id = " . $depense_id;
            $sql .= " ORDER BY depenses.date_depense, CASE WHEN depenses.montant >= 0 THEN 1 ELSE 0 END, depenses.id ";
            // Execute la requete SQL
              $requete_resultat = mysqli_query($mysql_conn, $sql);
              if (!$requete_resultat) {
                die('Requ&ecirc;te invalide (vuereconciliationreference) : ' . mysqli_error($mysql_conn));
              }
            // Initialise quelques valeurs
              $solde_actuelle = 0;
              $lignes = array();
              $derniere_date = $maintenant;
            // Boucle dans les resultats
              while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
                // Calcule le nouveau solde
                  $solde_actuelle += $ligne['montant'];
                // Initialise quelques valeurs
                  $bRet = formatEntreeDepense($ligne);
                  $ligne['solde'] = abs($solde_actuelle);
                  $ligne['solde_symbole'] = ($solde_actuelle < 0 ? 0 : 1);
                  $ligne['soldeformat'] = formatMonnaie(abs($solde_actuelle), ($solde_actuelle < 0 ? 0 : 1));
                  $ligne['montantformat'] =  formatMonnaie($ligne['montant'], $ligne['symbole']);
                // Efface la date si pareil que la precedente
                  if ($derniere_date == DateSeulement($ligne['date_depense'])) {
                    $ligne['dateformat'] = '';
                  }
                // Retient la date de la depense
                  $derniere_date = DateSeulement($ligne['date_depense']);
                // Ajout au array
                  $lignes [ count($lignes) ] = $ligne;
              } // Fin du while
            // Libere la memoire
              mysqli_free_result($requete_resultat);
            // Assigne a Smarty
              $smarty->assign('lignes', $lignes, true);
          }
        break;

      case "liste":
      default:
        // Initialise quelques variables
          $ligne_virtuel = array();
        // Verifie s'il y a des récurrences a créer
          $sql  = " SELECT * FROM recurrence ";
          $sql .= "  WHERE recurrence.compte_id = " . $compte_id . " ";
          $sql .= "     OR (recurrence.type_transaction_id = " . $type_transaction_transfert . " AND recurrence.transfert_compte_id = " . $compte_id . ") ";
          $sql .= "  ORDER by recurrence.ordre ";
          // Execute requete
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (liste:1) : ' . mysqli_error($mysql_conn));
          }
        // Verifie resultat
          if (mysqli_num_rows($requete_resultat) > 0) {
            // Ajoute un saut de ligne
            if ($ajout_msg != '') {
              $ajout_msg .= "<br />";
            }

            // Initialise quelques valeurs
            $ajout_msg_temp = '';
            $erreur_msg_temp = '';

            // Boucle dans les recurrences
            while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
              $skipFirst = false;
              $dateDerniereEntree = DateDerniereEntreeRecurrence($ligne['id'], $skipFirst);
              if ($dateDerniereEntree == null ||
                  $dateDerniereEntree < $afficherjusquau) {
                // Besoin de créer quelque recurrence
                $ligne_virtuel_false = false;
                recurrenceDepenseEntree($ligne['id'], $compte_id, $afficherjusquau, $ligne_virtuel_false, $ajout_msg, $erreur_msg);
                // Créer les lignes virtuelle
                recurrenceDepenseEntree($ligne['id'], $compte_id, $afficherjusquau, $ligne_virtuel, $ajout_msg_temp, $erreur_msg_temp);
              } else {
                // Toute les entree de la recurrence ont été créer dans la DB
              }
            }

            // Pour debug
            //$ajout_msg .= $ajout_msg_temp;
            //$erreur_msg .= $erreur_msg_temp;

            // Initialise quelques valeurs au ligne virtuel
            if ( count($ligne_virtuel) > 0 ) {
              foreach ($ligne_virtuel as $ligne_id => $ligne) {
                $ligne_virtuel[$ligne_id]['compte_description'] = $liste_compte[ $ligne['compte_id'] ]['description'];
                $ligne_virtuel[$ligne_id]['type_depense_description'] = $liste_type_depense[ $ligne['type_depense_id'] ]['description'];
                $ligne_virtuel[$ligne_id]['type_transaction_description'] = $liste_type_transaction[ $ligne['type_transaction_id'] ]['description'];
                $ligne_virtuel[$ligne_id]['tr_id'] = -1;
                $ligne_virtuel[$ligne_id]['tr_reconcilier_depense_id'] = -1;
                if ( !isset( $ligne_virtuel[$ligne_id]['tr_compte_id']) || $ligne_virtuel[$ligne_id]['tr_compte_id'] == "-1" ) {
                  $ligne_virtuel[$ligne_id]['tr_compte_id'] = -1;
                  $ligne_virtuel[$ligne_id]['tr_compte_description'] = '';
                } else { // if ( isset($liste_compte[ $ligne['tr_compte_id'] ]['description']) ) {
                  $ligne_virtuel[$ligne_id]['tr_compte_description'] =  $liste_compte[ $ligne['tr_compte_id'] ]['description'];
                }
              }
            }
          }

        // Affiche les lignes de depenses existantes
        // Requete SQL
          $sql = "";
          $sql .= "SELECT depense.*, ";
          $sql .= "       IfNull(compte.description, '') AS 'compte_description', ";
          $sql .= "       IfNull(type_depense.description, '') AS 'type_depense_description', ";
          $sql .= "       IfNull(type_transaction.description, '') AS 'type_transaction_description', ";

          $sql .= "       IfNull(tr_depense.id, -1) AS 'tr_id', ";
          $sql .= "       IfNull(tr_depense.compte_id, -1) AS 'tr_compte_id', ";
          $sql .= "       IfNull(tr_compte.description, '') AS 'tr_compte_description', ";
          $sql .= "       IfNull(tr_depense.reconcilier_depense_id, -1) AS 'tr_reconcilier_depense_id' ";

          $sql .= "  FROM depenses AS depense";
          $sql .= "  LEFT JOIN comptes AS compte ON compte.id = depense.compte_id ";
          $sql .= "  LEFT JOIN type_depense ON type_depense.id = depense.type_depense_id ";
          $sql .= "  LEFT JOIN type_transaction ON type_transaction.id = depense.type_transaction_id ";
          $sql .= "  LEFT JOIN depenses AS tr_depense ON tr_depense.id = depense.transfert_compte_id ";
          $sql .= "  LEFT JOIN comptes AS tr_compte ON tr_compte.id = tr_depense.compte_id ";

          $sql .= " WHERE depense.compte_id = " . $compte_id . " ";
          $sql .= "   AND depense.est_effacer = 0 ";
          $sql .= "   AND depense.date_depense <= '" . $afficherjusquau . "' ";
          $sql .= "   AND depense.reconcilier_depense_id = 0 ";
          //$sql .= "   AND NOT (depense.type_transaction_id = $type_transaction_reconciliation ";
          //$sql .= "            AND depense.montant = 0) ";

          $sql .= " ORDER BY depense.date_depense, CASE WHEN depense.montant >= 0 THEN 1 ELSE 0 END, depense.id ";
        // Execute requete
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (liste:2) : ' . mysqli_error($mysql_conn));
          }
        // Converti le resultat dans un tableau
          $lignes_temp = array();
          $lignes_date = array(); // Tableau pour contenir les dates de depense existante
          while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
            $lignes_temp[] = $ligne;
            $d = DateSeulement($ligne['date_depense']);
            if ( $ligne['type_transaction_id'] != $type_transaction_reconciliation ) {
              $lignes_date[ $d ] = $d;
            }
          }
        // Ajouter les lignes virtuel
        foreach ($ligne_virtuel as $ligne) {
          $lignes_temp[] = $ligne;
          $d = DateSeulement($ligne['date_depense']);
          $lignes_date[ $d ] = $d;
        }
        // Trie les dates (en gardant les 'named key')
        uasort($lignes_date, function ($a, $b) {
          $comparaisonDate = strtotime($a) - strtotime($b);
          return $comparaisonDate;
        });
        // Si aucune depense, prend la date d'aujourd'hui
        if (count($lignes_date) == 0) {
          $lignes_date[] = DateSeulement($maintenant);
        }
        // Ajoute les journees vides
          // Defini la date ou commencer
          $p = strtotime("first day of previous month", strtotime($maintenant));
          $p = date("Y-m-d", strtotime("-1 day", $p));
          if (reset($lignes_date) < $p) {
            // Utilise la premiere date des depenses
            $p = reset($lignes_date);
          }
          GenereLigneJourneeVide($lignes_temp, $lignes_date, $p,  $afficherjusquau);
                                                                                                     
//echo 'maintenant:' . DateSeulement($maintenant) . "<br>";
//$p =  strtotime("first day of previous month", strtotime($maintenant));
//echo 'p:' . $p . "<br>";

//echo 'afficherjusquau:' . $afficherjusquau . "<br>";
//echo 'reset(lignes_date):' . reset($lignes_date) . "<br>";
//echo 'aaa:' . date("t", strtotime($maintenant)) . "<br>";
//echo 'aaa:' . date("L", strtotime($maintenant)) . "<br>"; // 0(365) ou 1 si bixetille(366)
//echo 'annee : ' .  date("Y", strtotime(  reset($lignes_date)  ));
//echo 'mois : ' .  date("m", strtotime(   reset($lignes_date)  ));
/*
si l'annee et le mois de $maintenant = celui de reset()
  faut partir les journee vide a partir du 1er du mois precedent
cela va permettre de creer la ligne virtuelle pour l'interet et plus tard avoir la possibiliter de la convertir en vrai ligne

*/
                      

                                                                                                    

        // Trie les lignes
        usort($lignes_temp, function ($a, $b) {
          $comparaisonDate = strtotime($a['date_depense']) - strtotime($b['date_depense']);
          if ($comparaisonDate !== 0) {
            return $comparaisonDate;
          }
          return ($a['montant'] < 0 ? 0 : 1) - ($b['montant'] < 0 ? 0 : 1);
        });

        // Initialise quelques valeurs
          $solde_actuelle = 0;
          $aujourdhui_afficher = false;
          $derniere_date = '';
          $derniere_annee = '';
          $dernier_mois = '';
          $dernier_jour = '';
          $derniere_annee_visible = '';
          $dernier_mois_visible = '';
          $interet_trouver_pour_le_mois = false;
          $calcule_interet = false;
          $interet = 0;
          $taux = 0;
          $date_ligne_interet = array();
if ($compte['methode_calcule_interet'] == 1) {
  $taux = $compte['taux_interet'];
  //echo $taux;
}
//          $premiere_depense_afficher = false;
          $lignes = array();
        // Boucle dans les resultat
          foreach ($lignes_temp as $ligne) {
            // Initialise la ligne
              $bRet = formatEntreeDepense($ligne);

                                           
// Calculet des interet
//$ligne['description'] .= "(dj:" . $ligne['date_jour'] . ")";
if ($taux > 0 && $calcule_interet == false && $ligne['date_jour'] == 1) {
  $calcule_interet = true;
  //echo "OOOOO-";
}
if ($calcule_interet == true && $dernier_jour != $ligne['date_jour']) {
  //echo "AAAAAA (" . $interet . ")(" . $dernier_mois . ")(" . $ligne['date_mois'] . ")(" . $ligne['date_jour'] . ")-<br>";
  if ($interet <> 0 && $dernier_mois != $ligne['date_mois'] && $ligne['date_jour'] == 1 ) {
    //echo "BBBBBB-";
    // Affiche separateur de mois
/*
                                                                                                                         
faut ajouter cela ici car sinon si c'est juste des ligne d'interet qui genere elle seront tous coller                    
                                                                                                                         
      $afficherSeparateurAvant = false;
      if ($derniere_date != '' && ($derniere_annee_visible != $ligne['date_annee'] ||
                                   $dernier_mois_visible != $ligne['date_mois'] )) {
        $afficherSeparateurAvant = true;
      }
   $derniere_annee_visible = $ligne['date_annee'];
                $dernier_mois_visible = $ligne['date_mois'];
*/

    // Ajoute l'interet au solde
    $description_interet = 'Intérêt';
    //$description_interet .= ' (' . $interet . ')';
    $interet = round($interet, 2);
    if (!$interet_trouver_pour_le_mois) {
      $solde_actuelle += $interet;
      $solde_actuelle = round($solde_actuelle, 2);
    }
    // Ajouter une ligne virtuelle
    $temp_ligne = creerLigneVirtuelle($compte_id, $derniere_date, $type_transaction_interet, 1 /* Autres ? */, $interet,
                                      $description_interet, $solde_actuelle);
    formatEntreeDepense($temp_ligne);
    // Affiche separateur de mois
    if ($derniere_date != '' && ($derniere_annee_visible != $temp_ligne['date_annee'] ||
                                 $dernier_mois_visible != $temp_ligne['date_mois'] )) {
      $temp_ligne['afficherSeparateurAvant'] = true;
    }
    // Retient la derniere date
    $derniere_date = DateSeulement($temp_ligne['date_depense']);
    $derniere_annee = $temp_ligne['date_annee'];
    $dernier_mois = $temp_ligne['date_mois'];
    $dernier_jour = $temp_ligne['date_jour'];
    $derniere_annee_visible = $temp_ligne['date_annee'];
    $dernier_mois_visible = $temp_ligne['date_mois'];
    // Ajoute la ligne
    if (!$interet_trouver_pour_le_mois) {
      $lignes[] = $temp_ligne;
      $date_ligne_interet[ DateSeulement($temp_ligne['date_depense']) ] = DateSeulement($temp_ligne['date_depense']);
    }
    // Reinitialise l'interet
    $interet = 0;
    $interet_trouver_pour_le_mois = false;
  }
/*
  // Calcule l'interet
  $t = date("t", strtotime($ligne['date_depense']));
  $l =  date("L", strtotime($ligne['date_depense']));
  $y = ($l == 1 ? 366 : 365);
  $interet_v += $solde_actuelle * ($taux / 100) / $y * 1;
  $interet = round($interet_v, 2);
  $ligne['description'] .= "(interet)"
                        . "(t:" . $t . ")"
                        . "(L:" . $l . ")"
                        . "(y:" . $y . ")"
                        . "(iv:" . $interet_v . ")"
                        . "(i:" . $interet . ")"
                        //. "(solde:" . $solde . ")"
            ;
*/
  // . "<br>"; // 0(365) ou 1 si bixetille(366)
} // end if $calcule_interet == true




            // Ceci est la premiere depense ?
//              if ($ligne['type_transaction_id'] != $type_transaction_reconciliation) {
//                $premiere_depense_afficher = true;
//              }
// yannick
/*
   $type_transaction_normale = 1;
    $type_transaction_transfert = 2;
    $type_transaction_reconciliation = 3;
    $type_transaction_ajustement = 4;

*/
//$ligne['description'] .= ' *** id(' . $ligne['id'] . ')' .
//                         '(' . $type_transaction_reconciliation . ')' .
//                         '(' . $ligne['type_transaction_id'] . ')' .
//                         '(' . $premiere_depense_afficher . ')';
            // Calcule le nouveau solde (si ce n'est pas une journee vide)
              if ($ligne['estJourneeVide'] == false) {
//echo '(s:' . $solde_actuelle . ',';
                $solde_actuelle += ($ligne['symbole'] == 1 ? $ligne['montant'] : -($ligne['montant']));
                $solde_actuelle = round($solde_actuelle, 2);
//echo  ' m:' . $ligne['montant'] . ' s:' . $solde_actuelle . ')<br>';
              }
            // Solde
              $ligne['solde'] = abs($solde_actuelle);
              $ligne['solde_symbole'] = ($solde_actuelle < 0 ? 0 : 1);
              $ligne['soldeformat'] = formatMonnaie(abs($solde_actuelle), ($solde_actuelle < 0 ? 0 : 1));
            // Affiche la ligne aujourd'hui
//echo "ici code a revoir<br>";
/*
  si la ligne est en date d'aujourd'hui
  si la ligne est une depense ou une ligne virtualle
    ok
  si la ligne est une journee vide
    ok aussi, si la ligne journee vide a ete creer, ca veux dire qui a pas de ligne de depense ou virtuelle

*/
              if (DateSeulement($ligne['date_depense']) == DateSeulement($maintenant)) {
                $aujourdhui_afficher = true;
                $ligne['estAujourdhui'] = true;
              } // else {
                if ($aujourdhui_afficher == false && DateSeulement($ligne['date_depense']) > $aujourdhui) {
echo "Ajouter today avant<br>";
                  // Afficher ligne aujourd'hui
                  $aujourdhui_afficher = true;
                  $ligne_temp = creerLigneVirtuelle($compte_id, $maintenant, -1, -1, 0, '',  $solde_actuelle + ($ligne['symbole'] == 0 ? $ligne['montant'] : -($ligne['montant']))    );
                  $ligne_temp ['estAujourdhui'] = true;
                  $ligne_temp ['estJourneeVide'] = true;
                  $ligne_temp ['afficherSeparateurAvant'] = false;

                  /*
                    $ligne_temp = array();
                    $ligne_temp ['dateformat'] = AbrvJourSemaine($maintenant) . ' ' . DateSeulement($maintenant);
                    $ligne_temp ['couleur'] = 'couleur_date_aujourdhui';
                    $ligne_temp ['estAujourdhui'] = true;
                    $ligne_temp ['estJourneeVide'] = false;
                    $ligne_temp ['afficherSeparateurAvant'] = false;
                    $ligne_temp ['id'] = 0;
                    $ligne_temp ['solde'] = $solde_actuelle + ($ligne['symbole'] == 0 ? $ligne['montant'] : -($ligne['montant']));
                    $ligne_temp ['soldeformat'] = formatMonnaie(abs($lignes [ $ligne_id ] ['solde']), ($lignes [ $ligne_id ] ['solde'] < 0 ? 0 : 1));
                    //$lignes [ $ligne_id ] ['solde'] = $temp;
                    //$lignes [ $ligne_id ] ['montantforma'] = '';
                  */
                  $bRet = formatEntreeDepense($ligne_temp);
                  //$ligne_id = count($lignes);
                  $lignes [] = $ligne_temp;

                } elseif ( DateSeulement($ligne['date_depense']) == DateSeulement($maintenant) ) {
                  // ligne aujourd'hui deja afficher
//echo "Ligne est today<br>";
//                  $aujourdhui_afficher = true;
//$ligne['estAujourdhui'] = true;
                } else {
                  // Afficher ligne journee vide
                  if ($derniere_date == '' ) {
                    // Aucune ligne afficher
                  } else if ($afficher_journee_vide == true && $premiere_depense_afficher == true) {
                    // Ajoute les journees vide
                    // Le probleme, c'est qu'il commence a la date de la premiere depense afficher
                    // Si c'est des reconciliation, de l'an passer, il va y en avoir beaucoup
// idealement faudrait ajouter cest ligne avant
// car je vais en avoir besoin pour créer les lignes virtuelle d'interet
// par default les journee vide serai cacher
// cette boucle la servirait a les afficher en changant une valeur dans le array
//GenereLigneJourneeVide($lignes, $derniere_date, $ligne['date_depense']);
/*                                 
                    $boucle_infinie = 1000;
                    $prochaine_date_vide = strtotime($derniere_date);
                    $date_arret = strtotime(DateSeulement($ligne['date_depense']));

                    do {
                      $boucle_infinie-=1;
                      $prochaine_date_vide = strtotime(" +1 day", $prochaine_date_vide);

                      if ($prochaine_date_vide >= $date_arret) {
                        break;
                      } else {
                        $ligne_id = count($lignes);
                        $lignes [ $ligne_id ] = array(); // $ligne;
                        $lignes [ $ligne_id ] ['id'] = 0;
                        //$lignes [ $ligne_id ] ['description'] = '(' . $premiere_depense_afficher . ')(' . $prochaine_date_vide . ')(' . $derniere_date . ')';
                        $lignes [ $ligne_id ] ['date_depense'] = date('Y-m-d', $prochaine_date_vide);
                        $d = DateSeulement( $lignes [ $ligne_id ] ['date_depense'] );
                        $lignes [ $ligne_id ] ['dateformat'] = AbrvJourSemaine($lignes [ $ligne_id ] ['date_depense']) . ' ' . $d;
                        $lignes [ $ligne_id ] ['date_annee'] = date("Y", strtotime( $lignes [ $ligne_id ] ['date_depense'] ));
                        $lignes [ $ligne_id ] ['date_mois'] = date("m", strtotime( $lignes [ $ligne_id ] ['date_depense'] ));
                        $lignes [ $ligne_id ] ['couleur'] = obtenirCouleurLigne($d, true);
                        $lignes [ $ligne_id ] ['estAujourdhui'] = false;
                        $lignes [ $ligne_id ] ['estJourneeVide'] = true;
                        $lignes [ $ligne_id ] ['afficherSeparateurAvant'] = false;

                        if ($derniere_date != '' && ($derniere_annee != $lignes [ $ligne_id ]['date_annee'] ||
                                                     $dernier_mois != $lignes [ $ligne_id ] ['date_mois'] )) {
                          $ligne['afficherSeparateurAvant'] = true;
                        }
                        $derniere_date = $lignes [ $ligne_id ] ['date_depense'];
                        $derniere_annee = $lignes [ $ligne_id ] ['date_annee'];
                        $dernier_mois = $lignes [ $ligne_id ] ['date_mois'];

                        $temp = $solde_actuelle; //+ ($ligne['symbole'] == 0 ? $ligne['montant'] : -($ligne['montant']));
                        $temp = formatMonnaie(abs($temp), ($temp < 0 ? 0 : 1));
                        $lignes [ $ligne_id ] ['solde'] = $temp;
                      }
                    } while ($boucle_infinie > 0);
*/                                   
                  } else {
                    // Ignore
                  }
                }
              //}
            // Efface la date si pareil que la precedente
              if ($derniere_date == DateSeulement($ligne['date_depense'])) {
                $ligne['dateformat'] = '';
              }
            // Affiche separateur de mois
              if ($derniere_date != '' && ($derniere_annee_visible != $ligne['date_annee'] ||
                                           $dernier_mois_visible != $ligne['date_mois'] )) {
                $ligne['afficherSeparateurAvant'] = true;
              }



                                                            
// Calcule de l'interet
if ($calcule_interet == true) {
//echo '(' . $dernier_mois . ')';
//echo '(' . $ligne['date_mois'] . ')';
//echo '(' . $ligne['date_jour'] . ')<br>';

  // Recommence l'interet pour le prochain mois
  if ( $dernier_mois != $ligne['date_mois'] && $ligne['date_jour'] == 1 ) {
    // Affiche separateur de mois
    $interet = 0 ;
    //echo 'reset<br>';
  }


  // Calcule l'interet pour la journee
  if ($dernier_jour != $ligne['date_jour']) {
    // Calcule l'interet
    $t = date("t", strtotime($ligne['date_depense']));
    $l =  date("L", strtotime($ligne['date_depense']));
    $y = ($l == 1 ? 366 : 365);
    $interet += $solde_actuelle * ($taux / 100) / $y * 1;
    //$interet = round($interet_v, 2);
/*    $ligne['description'] .= "(interet)"
                          . "(t:" . $t . ")"
                          . "(L:" . $l . ")"
                          . "(y:" . $y . ")"
                          . "(iv:" . $interet_v . ")"
                          . "(i:" . $interet . ")"
                          //. "(solde:" . $solde . ")"
            ;*/
  }
}






            // Retient la derniere date
              $derniere_date = DateSeulement($ligne['date_depense']);
              $derniere_annee = $ligne['date_annee'];
              $dernier_mois = $ligne['date_mois'];
              $dernier_jour = $ligne['date_jour'];
            // Ajout au tableau
                                                                
//$ligne['description'] .= '(ajv : ' . var_export($afficher_journee_vide, true) . ')' .
//'(ejv : ' . var_export($ligne['estJourneeVide'] , true) . ')';

              if ($afficher_journee_vide == false && $ligne['estJourneeVide'] == true && $ligne['estAujourdhui'] == false) {
                // Ne pas afficher la journee vide
              } else {
                $lignes [ count($lignes) ] = $ligne;
                $derniere_annee_visible = $ligne['date_annee'];
                $dernier_mois_visible = $ligne['date_mois'];
              }

              // C'est de l'interet
              if ($ligne['type_transaction_id'] == $type_transaction_interet) {
                $interet_trouver_pour_le_mois = true;
              }
          } // Fin de la boucle











// Enleve les journees vide ou il y a eu de l'interet creer
//echo 'C:' . count($lignes) ."<br>";
foreach ($lignes as $key => $ligne) {
  if ( $ligne['estJourneeVide'] == true) {
    if ( isset( $date_ligne_interet[ $ligne['date_depense'] ]  ) ) {
      // Est une journee vide et une ligne d'interet virtuelle a ete creer
//echo $key . " enlever <br>";
      unset ( $lignes[$key] );
    } else {
      // Est une journee vide, mais aucune ligne d'interet virtuelle a ete creer
    }
  } else {
    // N'est pas une journee vide
  }
}
//echo 'C:' . count($lignes) ."<br>";
                                                                                                                  




        // Ajout les ligne des journee manquante
// GenereLigneJourneeVide($lignes, $derniere_date, $ligne['date_depense']);
//echo $afficherjusquau . ' - ';
//echo $derniere_date . ' - ';
/*        
          $ligne['id'] = 0;
          $ligne['estAujourdhui'] = false;
          $ligne['estJourneeVide'] = true;
          $ligne['couleur'] = '';
          $ligne['dateformat'] = AbrvJourSemaine($maintenant) . ' ' . DateSeulement($maintenant);
          $ligne['solde'] = formatMonnaie(abs($solde_actuelle), ($solde_actuelle < 0 ? 0 : 1));
          $lignes [ count($lignes) ] = $ligne;
*/        

        // Ajout la ligne d'aujourd'hui
          if ($aujourdhui_afficher == false) {
echo "Afficher la ligne pour aujourdhui<br>";
            $ligne['id'] = 0;
            $ligne['estAujourdhui'] = true;
            $ligne['estJourneeVide'] = false;
            $ligne['couleur'] = 'couleur_date_aujourdhui';
            $ligne['dateformat'] = AbrvJourSemaine($maintenant) . ' ' . DateSeulement($maintenant);
            $ligne['solde'] = formatMonnaie(abs($solde_actuelle), ($solde_actuelle < 0 ? 0 : 1));
            $lignes [ count($lignes) ] = $ligne;
          }
        // Libere la memoire
          mysqli_free_result($requete_resultat);
        // Assigne a Smarty
          $smarty->assign('lignes', $lignes, true);
        break;

    } // Fin des action


  // Assigne variables a Smarty
    $smarty->assign('compte_id', $compte_id, true);
    $smarty->assign('defilerauid', $defilerauid, true);
    $smarty->assign('action', $action, true);
    $smarty->assign('modifier_depense_id', $modifier_depense_id, true);
    $smarty->assign('effacer_depense_id', $effacer_depense_id, true);
    $smarty->assign('afficherjusquau', $afficherjusquau, true);
    $smarty->assign('ajouter_type_transaction_id', $ajouter_type_transaction_id, true);
    $smarty->assign('ajouter_transfert_compte_id', $ajouter_transfert_compte_id, true);
    $smarty->assign('ajouter_date_depense', $ajouter_date_depense, true);
    $smarty->assign('ajouter_type_depense_id', $ajouter_type_depense_id, true);
    $smarty->assign('ajouter_description', $ajouter_description, true);
    $smarty->assign('ajouter_symbole', $ajouter_symbole, true);
    $smarty->assign('ajouter_montant', $ajouter_montant, true);
    $smarty->assign('ajouter_notes', $ajouter_notes, true);


  // Affiche le template
    $template_fichier = 'depense.tpl';









// Function qui genere une ligne virtuelle
function creerLigneVirtuelle($compte_id, $date_depense, $type_transaction_id, $type_depense_id, $montant, $description, $solde) {
  return array(
      'virtuel' => true,
      'id' => 0,
      'pointer' => 0,
      'est_effacer' => 0,
      'date_ajouter' => $date_depense,
      'date_depense' => $date_depense,
      // date_annee
      // date_mois
      // date_jour
      'dateformat' => AbrvJourSemaine($date_depense) . ' ' . DateSeulement($date_depense),
      'compte_id' => $compte_id,
    'compte_description' => '', //Definie plus tard
      'type_transaction_id' => $type_transaction_id,
  'type_transaction_description' => '', // Definie plus tard
      'date_depense' => $date_depense,
      'type_depense_id' => 1, /* = Autres */
   'type_depense_description' => '', // Definie plus tard
      'description' => $description,
      'notes' => '',
      'montant' => abs($montant),
      'symbole' => ($montant < 0 ? 0 : 1),
      //'montantformat' => formatMonnaie(abs($montant), ($montant < 0 ? 0 : 1)),
      'recurrence_id' => -1,
      'reconcilier_depense_id' => 0,
      'transfert_compte_id' => -1,
      'tr_compte_id' => -1,
      'tr_id' => -1,
      'tr_compte_description' => '',
      'tr_reconcilier_depense_id' => -1,
      'solde' => $solde,
      //'solde_symbole' => ($solde < 0 ? 0 : 1),
      //'soldeformat' => formatMonnaie(abs($solde), ($solde < 0 ? 0 : 1)),

      'estAujourdhui' => false,
      'estJourneeVide' => false,
      'afficherSeparateurAvant' => false,
    );
}






// Function qui genere des lignes vides
function GenereLigneJourneeVide(&$lignes, $lignes_date, $date_depart, $date_fin) {
                    $boucle_infinie = 1000;
                    $prochaine_date_vide = strtotime($date_depart);
                    $date_arret = strtotime(DateSeulement($date_fin));
                    do {
                      $boucle_infinie-=1;
                      $prochaine_date_vide = strtotime(" +1 day", $prochaine_date_vide);

                      if ($prochaine_date_vide > $date_arret) {
                        break;
                      } elseif ( isset( $lignes_date[ date('Y-m-d', $prochaine_date_vide) ] ) ) {
                        // Ignore cette date
                      } else {
                        $ligne = array();
                        $ligne['id'] = 0;
                        $ligne['description'] = 'ok';
                        $ligne['notes'] = '';
                        $ligne['description'] = '';
                        //$ligne['description'] = '(' . $prochaine_date_vide . ')(' . $derniere_date . ')(' . date('Y-m-d', $prochaine_date_vide) . ')';
                        $ligne['date_depense'] = date('Y-m-d', $prochaine_date_vide);
                        $d = DateSeulement( $ligne['date_depense'] );
                        $ligne['dateformat'] = AbrvJourSemaine($ligne['date_depense']) . ' ' . $d;
                        $ligne['date_annee'] = date("Y", strtotime( $ligne['date_depense'] ));
                        $ligne['date_mois'] = date("m", strtotime( $ligne['date_depense'] ));
                        $ligne['date_mois'] = date("d", strtotime( $ligne['date_depense'] ));
                        $ligne['couleur'] = obtenirCouleurLigne($d, true);
                        $ligne['estAujourdhui'] = false;
                        $ligne['estJourneeVide'] = true;
                        $ligne['tr_compte_id'] = -1;
                        $ligne['compte_description'] = '';
                        $ligne['type_depense_description'] = '';
                        $ligne['type_transaction_description'] = '';
                        $ligne['tr_compte_description'] = '';
                        $ligne['reconcilier_depense_id'] = -1;
                        $ligne['type_transaction_id'] = -1;
                        $ligne['tr_reconcilier_depense_id'] = -1;

                        $ligne['afficherSeparateurAvant'] = false;
                        // sera defini plus tard
                        //if ($derniere_date != '' && ($derniere_annee != $ligne['date_annee'] ||
                        //                             $dernier_mois != $ligne['date_mois'] )) {
                        //  $ligne['afficherSeparateurAvant'] = true;
                        //}
                        $derniere_date = $ligne['date_depense'];
                        $derniere_annee = $ligne['date_annee'];
                        $dernier_mois = $ligne['date_mois'];

                        $temp = $solde_actuelle; //+ ($ligne['symbole'] == 0 ? $ligne['montant'] : -($ligne['montant']));
                        $temp = formatMonnaie(abs($temp), ($temp < 0 ? 0 : 1));
                        $ligne['solde'] = $temp;
                        $ligne['montant'] = 0;

                        $ligne_id = count($lignes);
                        $lignes [ $ligne_id ] = $ligne;
                      }
                    } while ($boucle_infinie > 0);
}

?>
