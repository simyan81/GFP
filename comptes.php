<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


  // Initialise quelques valeurs
    $modifier_compte_id = -1;
    $effacer_compte_id = -1;
    $modifier_groupe_id = -1;
    $effacer_groupe_id = -1;


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', 'liste');
    $defilerauid = ObtenirValeur('defilerauid', -1);


  // Cree un nonce pour les formulaires
    $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_comptes', 10), true);


  // Verifie les pre-action
    switch ($action) {
      case 'compte_deplacer':
        // Obtient quelques valeurs
          $compte_id = ObtenirValeur('compte_id', '');
          $ordre = ObtenirValeur('ordre', '');
          $valide = true;
        // Valide quelques valeurs
          if ( !is_numeric($compte_id) ||
               !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<br />';
            $valide = false;
          }
        // Met a jour le compte dans la DB
          if ($valide) {
            $sql  = ' UPDATE comptes SET ordre=' . $ordre . ' WHERE id=' . $compte_id . ' ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre de la ligne s&eacute;lectionner.<br />";
            } else {
              $ajout_msg .= 'Ordre de la ligne s&eacute;lectionner mise &agrave; jour.<br />';
            }
          }
        // Actualise les liste (et met a jour l'ordre )
          ObtenirListeComptes(true);
        break;

      case 'groupe_deplacer':
        // Obtient quelques valeurs
          $groupe_id = ObtenirValeur('groupe_id', '');
          $ordre = ObtenirValeur('ordre', '');
          $valide = true;
        // Valide quelques valeurs
          if ( !is_numeric($groupe_id) ||
               !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<br />';
            $valide = false;
          }
        // Met a jour le groupe dans la DB
          if ($valide) {
            $sql  = ' UPDATE groupes SET ordre=' . $ordre . ' WHERE id=' . $groupe_id . ' ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre de la ligne s&eacute;lectionner.<br />";
            } else {
              $ajout_msg .= 'Ordre de la ligne s&eacute;lectionner mise &agrave; jour.<br />';
            }
          }
        // Actualise les liste ( et met a jour l'ordre )
          ObtenirListeGroupes(true);
        break;


      case 'ajouter_compte':
          // Obtient quelques valeurs
            $nonce = ObtenirValeur('nonce', '');
            $compte_description = ObtenirValeur('ajouter_compte_description', '');
            $compte_groupe = ObtenirValeur('ajouter_compte_groupe', 0);
            $compte_type = ObtenirValeur('ajouter_compte_type', 0);
            $compte_ordre = ObtenirValeur('ajouter_compte_ordre', 0);
            $compte_methode_calcule_interet = ObtenirValeur('ajouter_compte_methode_calcule_interet', 0);
            $compte_taux_interet = ObtenirValeur('ajouter_compte_taux_interet', 0);
          // Valide quelques valeurs
            $valide = true;
            if ( !$cnonce->verifyNonce($nonce) ) {
              $erreur_msg = 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
              $valide = false;
            }
            if ($compte_description == '' ||
                !is_numeric($compte_groupe) ||
                !is_numeric($compte_type) ||
                !is_numeric($compte_ordre) ||
                !is_numeric($compte_methode_calcule_interet) ||
                !is_numeric($compte_taux_interet) ) {
              $erreur_msg .= 'Erreur de saisie.<br />';
              $valide = false;
            }
          // Ajoute le compte dans la DB
            if ($valide) {
              $sql  = " INSERT INTO comptes (date_ajouter, description, groupe_id, type_compte_id, est_effacer, ";
              $sql .= "                      methode_calcule_interet, taux_interet, ";
              $sql .= "                      ordre ";
              $sql .= " ) VALUES ( ";
              $sql .= "  '" . $maintenant . "', ";
              $sql .= "  '" . addslashes($compte_description) . "', ";
              $sql .= "   " . $compte_groupe . ", ";
              $sql .= "   " . $compte_type . ", ";
              $sql .= "   0, ";
              $sql .= "   " . $compte_methode_calcule_interet . ", ";
              $sql .= "   " . $compte_taux_interet . ", ";
              $sql .= "   " . $compte_ordre . " ) ";
              $requete_resultat = mysqli_query($mysql_conn, $sql);
              if (!$requete_resultat) {
                die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
              } else {
                $ajout_msg .= 'Compte ajouter.<br />';
              }
            }
          // Actualise les liste ( et met a jour l'ordre )
            ObtenirListeComptes(true);
          break;

      case 'ajouter_groupe':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $groupe_description = ObtenirValeur('ajouter_groupe_description', '');
          $groupe_ordre = ObtenirValeur('ajouter_groupe_ordre', 0);
        // Valide quelques valeurs
          $valide = true;
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
          if ($groupe_description == '' ||
            !is_numeric($groupe_ordre) ) {
            $erreur_msg .= 'Erreur de saisie.<br />';
            $valide = false;
          }
        // Ajoute le groupe dans la DB
          if ($valide) {
            $sql  = " INSERT INTO groupes (date_ajouter, description, est_effacer, ordre) VALUES (";
            $sql .= "  '" . $maintenant . "', ";
            $sql .= "  '" . addslashes($groupe_description) . "', ";
            $sql .= "  0, ";
            $sql .= "   " . $groupe_ordre . " ) ";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
            } else {
              $ajout_msg .= 'Groupe ajouter.<br />';
            }
          }
        // Actualise les liste ( et met a jour l'ordre )
          ObtenirListeGroupes(true);
        break;



      case 'compte_modifier_confirmer':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $compte_id = ObtenirValeur('modifier_compte_id', 0);
          $description = ObtenirValeur('modifier_description', '');
          $methode_calcule_interet = ObtenirValeur('modifier_methode_calcule_interet', 0);
          $taux_interet = ObtenirValeur('modifier_taux_interet', 0);
          $errmsg = '';
          $valide = true;
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Verifie si le compte existe
          $sql = "SELECT * FROM comptes WHERE id = " . $compte_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (compte_effacer_confirmer) : ' . mysqli_error($mysql_conn));
          }
          $nbr_compte = mysqli_num_rows($requete_resultat);
          if ($nbr_compte != 1) {
            $erreur_msg .= "Le compte est introuvable.<BR>";
            $valide = false;
          }
        // Modifie la depense dans la DB
          if ($valide) {
            $sql  = " UPDATE comptes ";
            $sql .= "    SET description = '" . addslashes($description) . "',  ";
            $sql .= "        methode_calcule_interet = " . $methode_calcule_interet . ", ";
            $sql .= "        taux_interet = " . $taux_interet . " ";
            $sql .= "  WHERE id = " . $compte_id;
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
              $erreur_msg .= 'Impossible de mettre &agrave; jour. ' . $errmsg . '<br />';
            } else {
              $ajout_msg .= 'Compte modifier.<br />';
            }
          }
        // Reinitialise quelques variables
          $modifier_compte_id = -1;
        // Retourne a la liste
          $action = "liste";
        break;

      case 'compte_effacer_confirmer':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $compte_id = ObtenirValeur('effacer_compte_id', 0);
          $errmsg = '';
          $valide = true;
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Verifie si le compte existe
          $sql = "SELECT * FROM comptes WHERE id = " . $compte_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (compte_effacer_confirmer) : ' . mysqli_error($mysql_conn));
          }
          $nbr_compte = mysqli_num_rows($requete_resultat);
          if ($nbr_compte != 1) {
            $erreur_msg .= "Le compte est introuvable.<BR>";
            $valide = false;
          }
          mysqli_free_result($requete_resultat);
        // Verifie si le compte a des depense ou reference a lui
          $nbr_depense = 1;
          $nbr_recurrence = 1;
          if ($valide) {
            $sql  = " SELECT ";
            $sql .= " ( SELECT COUNT(*) FROM depenses WHERE compte_id = " . $compte_id . " OR transfert_compte_id = " . $compte_id . " ) AS 'nbr_depense', ";
            $sql .= " ( SELECT COUNT(*) FROM recurrence WHERE compte_id = " . $compte_id . " OR transfert_compte_id = " . $compte_id . " ) AS 'nbr_recurrence' ";
            $sql .= "";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              die('Requ&ecirc;te invalide (compte_effacer_confirmer) : ' . mysqli_error($mysql_conn));
            }
            $ligne = mysqli_fetch_assoc($requete_resultat);
            $nbr_depense = $ligne['nbr_depense'];
            $nbr_recurrence = $ligne['nbr_recurrence'];
            mysqli_free_result($requete_resultat);
          }
        // Requete SQL
          if ($valide) {
            if ($nbr_depense > 0 || $nbr_recurrence > 0) {
              $sql = ' UPDATE comptes SET est_effacer = 1 WHERE id = ' . $compte_id;
            } else {
              $sql = ' DELETE FROM comptes WHERE id = ' . $compte_id;
            }
            // Execute requete SQL
              $requete_resultat = mysqli_query($mysql_conn, $sql);
              if (!$requete_resultat) {
                die('Requ&ecirc;te invalide (compte_effacer_confirmer) : ' . mysqli_error($mysql_conn));
              }
              $ajout_msg .= 'Compte effacer.<br />';
          }
        // Reinitialise quelques variables
          $effacer_compte_id = -1;
        // Retourne a la liste
          $action = "liste";
        break;


      case 'compte_effacer':
      case 'compte_modifier':
        // Obtiens quelques valeurs
          $modifier_compte_id = ObtenirValeur('modifier_compte_id', -1);
          $effacer_compte_id = ObtenirValeur('effacer_compte_id', -1);
        // Valide quelques valeurs
          if (is_numeric($modifier_compte_id) && $action == 'compte_modifier') {
            $compte_id = $modifier_compte_id;
          } elseif (is_numeric($effacer_compte_id) && $action == 'compte_effacer') {
            $compte_id = $effacer_compte_id;
          } else {
            $compte_id = -1;
          }
        // Verifie si le compte existe
          $sql = "SELECT * FROM comptes WHERE id = " . $compte_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (compte_effacer|compte_modifier) : ' . mysqli_error($mysql_conn));
          }
          $nbr_compte = mysqli_num_rows($requete_resultat);
          if ($nbr_compte != 1) {
            $compte_id = -1;
          }
        // Libere les ressources
          mysqli_free_result($requete_resultat);
       // Initialise quelques valeurs
          if ($action == 'compte_modifier') {
            $defilerauid = "compte_" . $compte_id;
            $modifier_compte_id = $compte_id;
            $effacer_compte_id = -1;
          } elseif ($action == 'compte_effacer') {
            $defilerauid = "compte_" . $compte_id;
            $effacer_compte_id = $compte_id;
            $modifier_compte_id = -1;
          } else {
            $compte_id = -1;
            $modifier_compte_id = $compte_id;
            $effacer_compte_id = $compte_id;
          }
        // Retourne a la liste
          $action = 'liste';
        break;




      case 'groupe_modifier_confirmer':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $groupe_id = ObtenirValeur('modifier_groupe_id', 0);
          $description = ObtenirValeur('modifier_description', '');
          $errmsg = '';
          $valide = true;
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Verifie si le groupe existe
          $sql = "SELECT * FROM groupes WHERE id = " . $groupe_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (groupe_effacer_confirmer) : ' . mysqli_error($mysql_conn));
          }
          $nbr_groupe = mysqli_num_rows($requete_resultat);
          if ($nbr_groupe != 1) {
            $erreur_msg .= "Le groupe est introuvable.<BR>";
            $valide = false;
          }
        // Modifie la depense dans la DB
          if ($valide) {
            $sql  = " UPDATE groupes SET description = '" . addslashes($description) . "'  ";
            $sql .= "  WHERE id = " . $groupe_id;
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
              $erreur_msg .= 'Impossible de mettre &agrave; jour. ' . $errmsg . '<br />';
            } else {
              $ajout_msg .= 'Groupe modifier.<br />';
            }
          }
        // Reinitialise quelques variables
          $modifier_groupe_id = -1;
        // Retourne a la liste
          $action = "liste";
        break;


   case 'groupe_effacer_confirmer':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $groupe_id = ObtenirValeur('effacer_groupe_id', 0);
          $errmsg = '';
          $valide = true;
        // Verifie le nonce
          if ( !$cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Verifie si le groupe existe
          $sql = "SELECT * FROM groupes WHERE id = " . $groupe_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (groupe_effacer_confirmer) : ' . mysqli_error($mysql_conn));
          }
          $nbr_groupe = mysqli_num_rows($requete_resultat);
          if ($nbr_groupe != 1) {
            $erreur_msg .= "Le groupe est introuvable.<BR>";
            $valide = false;
          }
          mysqli_free_result($requete_resultat);
        // Verifie si le groupe est utiliser par un compte
         $nbr_compte_dans_groupe = 1;
         if ($valide) {
           $sql = "SELECT * FROM comptes WHERE groupe_id = " . $groupe_id . "";
           $requete_resultat = mysqli_query($mysql_conn, $sql);
           if (!$requete_resultat) {
             die('Requ&ecirc;te invalide (groupe_effacer|groupe_modifier) : ' . mysqli_error($mysql_conn));
           }
           $nbr_compte_dans_groupe = mysqli_num_rows($requete_resultat);
           mysqli_free_result($requete_resultat);
         }
        // Requete SQL
          if ($valide) {
            if ($nbr_compte_dans_groupe == 0) {
              $sql = ' DELETE FROM groupes WHERE id = ' . $groupe_id . '';
            } else {
              $sql = ' UPDATE groupes SET est_effacer = 1 WHERE id = ' . $groupe_id . '';
            }
            // Execute requete SQL
              $requete_resultat = mysqli_query($mysql_conn, $sql);
              if (!$requete_resultat) {
                die('Requ&ecirc;te invalide (groupe_effacer_confirmer) : ' . mysqli_error($mysql_conn));
              }
              $ajout_msg .= 'Groupe effacer.<br />';
          }
        // Reinitialise quelques variables
          $effacer_groupe_id = -1;
        // Retourne a la liste
          $action = "liste";
        break;

      case 'groupe_effacer':
      case 'groupe_modifier':
        // Obtiens quelques valeurs
          $modifier_groupe_id = ObtenirValeur('modifier_groupe_id', -1);
          $effacer_groupe_id = ObtenirValeur('effacer_groupe_id', -1);
        // Valide quelques valeurs
          if (is_numeric($modifier_groupe_id) && $action == 'groupe_modifier') {
            $groupe_id = $modifier_groupe_id;
          } elseif (is_numeric($effacer_groupe_id) && $action == 'groupe_effacer') {
            $groupe_id = $effacer_groupe_id;
          } else {
            $groupe_id = -1;
          }
        // Verifie si le groupe existe
          $sql = "SELECT * FROM groupes WHERE id = " . $groupe_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (groupe_effacer|groupe_modifier) : ' . mysqli_error($mysql_conn));
          }
          $nbr_groupe = mysqli_num_rows($requete_resultat);
          if ($nbr_groupe != 1) {
            $groupe_id = -1;
          }
          mysqli_free_result($requete_resultat);
        // Initialise quelques valeurs
          if ($action == 'groupe_modifier') {
            $defilerauid = "groupe_" . $groupe_id;
            $modifier_groupe_id = $groupe_id;
            $effacer_groupe_id = -1;
          } elseif ($action == 'groupe_effacer') {
            $defilerauid = "groupe_" . $groupe_id;
            $effacer_groupe_id = $groupe_id;
            $modifier_groupe_id = -1;
          } else {
            $groupe_id = -1;
            $modifier_groupe_id = $groupe_id;
            $effacer_groupe_id = $groupe_id;
          }
        // Retourne a la liste
          $action = 'liste';
        break;

    } // Fin des pre-action


  // Enleve quelques valeurs dans le URL
    $url_actuelle = FunctionURLChange($url_actuelle, 'action', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'compte_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'effacer_compte_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'modifier_compte_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'effacer_groupe_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'modifier_groupe_id', '' );


  // Affiche la liste des groupes/comptes
  // Requete SQL
    $sql = "";
    $sql .= "SELECT c.*, ";
    $sql .= "       IfNull(g.id, -1) AS 'g.id', ";
    $sql .= "       IfNull(g.description, '') AS 'g.description', ";
    $sql .= "       IfNull(g.ordre, -1) AS 'g.ordre', ";
    $sql .= "       IfNull(g.est_effacer, 0) AS 'g.est_effacer', ";
    $sql .= "       IfNull( (SELECT COUNT(*) FROM comptes WHERE IfNull(est_effacer, 0) = 0 AND groupe_id = IfNull(g.id, -1) ), 1) AS 'g.nbr_comptes', ";
    $sql .= "       IFNULL( (SELECT SUM(montant)";
    $sql .= "          FROM depenses";
    $sql .= "         WHERE compte_id = c.id";
    $sql .= "           AND est_effacer = 0 ";
    $sql .= "           AND date_depense < '" . $maintenant . "' ";
    $sql .= "           AND reconcilier_depense_id = 0";
    $sql .= "           AND montant >= 0), 0) AS entre,";
    $sql .= "       IFNULL( (SELECT SUM(montant)";
    $sql .= "          FROM depenses";
    $sql .= "         WHERE compte_id = c.id";
    $sql .= "           AND est_effacer = 0 ";
    $sql .= "           AND date_depense < '" . $maintenant . "' ";
    $sql .= "           AND reconcilier_depense_id = 0";
    $sql .= "           AND montant < 0), 0) AS sortie, ";
    // Nombre de transaction passer pouvant etre reconcilier ou pointer ?
    $sql .= "       (SELECT COUNT(*) ";
    $sql .= "          FROM depenses";
    $sql .= "         WHERE compte_id = c.id";
    $sql .= "           AND est_effacer = 0 ";
    $sql .= "           AND date_depense < '" . date('Y-m-01 00:00:00', $maintenant_unix) . "' ";
    $sql .= "           AND type_transaction_id <> " . $type_transaction_reconciliation . " ";
    $sql .= "           AND (reconcilier_depense_id = 0 OR ";
    $sql .= "                pointer = 0)) as nombreavant";
    //$sql .= "  FROM comptes AS c ";
    //$sql .= " INNER JOIN groupes AS g ON g.id = c.groupe_id ";
    $sql .= "  FROM groupes AS g ";
    $sql .= "  LEFT JOIN comptes AS c on c.groupe_id = g.id AND IfNull(c.est_effacer, 0) = 0 ";
    $sql .= " WHERE IfNull(g.est_effacer, 0) = 0 ";
    $sql .= " ORDER BY g.ordre, c.ordre ";
  // Execute la requete SQL
    $requete_resultat = mysqli_query($mysql_conn, $sql);
    if (!$requete_resultat) {
      die('Requ&ecirc;te invalide (c...php) : ' . mysqli_error($mysql_conn));
    }

  // Boucle dans les resultats
    $g = array();
    $numeroLigne = 0;
    $grandtotal = 0;
    while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
      // Initialise quelques variable
       if (!is_numeric($ligne['id'])) {
         $ligne['id'] = -1;
       }
      // Cree le groupe
        if ( !isset($g[$ligne['g.id']] )) {
          // Defini quelques valeurs si c'est le dernier
            if (count ($g) > 0) {
              $e = end($g);
              if (  count($e['comptes']) > 0 ) {
                $c = end($e['comptes']);
                $g[ $e['id'] ] ['comptes'] [ $c['id'] ] ['estDernier'] = true;
              }
            }
          $g[$ligne['g.id']] ['numeroLigne'] = 0;
          $g[$ligne['g.id']] ['estPremier'] = (count($g) == 1 ? true : false);
          $g[$ligne['g.id']] ['estDernier'] = false;
          $g[$ligne['g.id']] ['id'] = $ligne['g.id'];
          $g[$ligne['g.id']] ['description'] = myslashes($ligne['g.description']);
          $g[$ligne['g.id']] ['ordre'] = $ligne['g.ordre'];
          $g[$ligne['g.id']] ['grandtotal'] = 0;
          $g[$ligne['g.id']] ['peuxEffacer'] = ($ligne['g.nbr_comptes'] > 0 ? false : true);
          $g[$ligne['g.id']] ['comptes'] = array();
          $numeroLigne = 0;
        }
      // Ajouter le compte au groupe actuelle
        if ($ligne['id'] > -1 && !isset($g[$ligne['g.id']]['comptes'][$ligne['id']]) ) {
          $numeroLigne += 1;
          $solde = ($ligne['entre'] + $ligne['sortie']);
          $grandtotal += $solde;
          $c = array();
          $c['numeroLigne'] = $numeroLigne;
          $c['estPremier'] = (count($g[$ligne['g.id']] ['comptes']) == 0 ? true : false);
          $c['estDernier'] = false;
          $c['solde'] = formatMonnaie(abs($solde), ($solde < 0 ? 0 : 1));
          $c['id'] = $ligne['id'];
          $c['description'] = myslashes($ligne['description']);
          $c['methode_calcule_interet'] = $ligne['methode_calcule_interet'];
          $c['taux_interet'] = $ligne['taux_interet'];
          $c['ordre'] = $ligne['ordre'];
          $c['nombreavant'] = $ligne['nombreavant'];
          $c['peuxEffacer'] = true;
          $c['est_effacer'] = $ligne['est_effacer'];
          $g[$ligne['g.id']]['comptes'][$ligne['id']] = $c;
          $g[$ligne['g.id']] ['grandtotal'] += $solde;
          $g[$ligne['g.id']] ['grandtotalformat'] = formatMonnaie(abs($g[$ligne['g.id']] ['grandtotal']), ($g[$ligne['g.id']] ['grandtotal'] < 0 ? 0 : 1));
        }
    }

    // Defini quelques valeurs au dernier groupe/compte
      $e = end($g);
      if ( count($e['comptes']) > 0) {
        $c = end($e['comptes']);
        $g[ $e['id'] ] ['comptes'] [ $c['id'] ] ['estDernier'] = true;
      }
      $g[ $e['id'] ] ['estDernier'] = true;

    // Ajoute la liste a Smarty
      $smarty->assign('groupes', $g, true);


  // Libere les ressources
    mysqli_free_result($requete_resultat);




  // Assigne variables a Smarty
    $smarty->assign('defilerauid', $defilerauid, true);
    $smarty->assign('action', $action, true);
    $smarty->assign('modifier_compte_id', $modifier_compte_id, true);
    $smarty->assign('effacer_compte_id', $effacer_compte_id, true);
    $smarty->assign('modifier_groupe_id', $modifier_groupe_id, true);
    $smarty->assign('effacer_groupe_id', $effacer_groupe_id, true);





  // Cree une liste des groupes
    $sql  = 'SELECT g.* FROM groupes AS g WHERE IfNull(g.est_effacer, 0) = 0 ORDER BY g.ordre, g.description';
    $requete_resultat = mysqli_query($mysql_conn, $sql);
    if (!$requete_resultat) {
      die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
    }
    $groupe_listeordre = array();
    $groupe_listeordre[ 1 ] = 'En premier';
    while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
      $groupe_listeordre[ $ligne['ordre'] + 1 ] = 'Apres ' . myslashes($ligne['description']);
    }
    mysqli_free_result($requete_resultat);
    $smarty->assign('groupe_listeordre', $groupe_listeordre, true);


  // Cree une lsite des comptes
    $sql  = 'SELECT c.* FROM comptes AS c WHERE IfNull(c.est_effacer, 0) = 0 ORDER BY c.ordre, c.description';
    $requete_resultat = mysqli_query($mysql_conn, $sql);
    if (!$requete_resultat) {
      die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
    }
    $compte_listeordre = array();
    $compte_listeordre[ 1 ] = 'En premier';
    while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
      $compte_listeordre[ $ligne['ordre'] + 1 ] = 'Apres ' . myslashes($ligne['description']);
    }
    mysqli_free_result($requete_resultat);
    $smarty->assign('compte_listeordre', $compte_listeordre, true);






  // Defini le fichier template
    $template_fichier = 'comptes.tpl';
?>
