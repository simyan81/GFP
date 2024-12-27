<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', 'list');
    $defilerauid = ObtenirValeur('defilerauid', -1);


  // Liste specifique au recurrence
    $type_interval_list = array();
    $type_interval_list[0] = 'Mensuel';
    $type_interval_list[1] = 'À la fin du mois';
    $type_interval_list[2] = 'Hebdomadaire';
    $type_interval_list[3] = 'Quotidien';
    $type_interval_list[4] = 'Annuel';

    $auto_type_interval_list = array();
    $auto_type_interval_list[0] = 'mois';
    //$auto_type_interval_list[1] = 'A interval de "x" jour';
    $auto_type_interval_list[2] = 'semaines';
    $auto_type_interval_list[3] = 'jours';

  // Initialise quelques valeurs
    $modifier_recurrence_id = -1;
    $effacer_recurrence_id = -1;
    $ajouter_recurrence_date_debut = '';
    $ajouter_recurrence_date_fin = '';
    $ajouter_recurrence_auto_type_interval = 0;
    $ajouter_recurrence_auto_interval = 1;
    $ajouter_recurrence_type_interval = 0;
    $ajouter_recurrence_interval = 1;
    $ajouter_recurrence_compte_id = 0;
    $ajouter_recurrence_type_transaction_id = 0;
    $ajouter_recurrence_transfert_compte_id = 0;
    $ajouter_recurrence_type_depense_id = 0;
    $ajouter_recurrence_description = '';
    $ajouter_recurrence_notes = '';
    $ajouter_recurrence_symbole = 0;
    $ajouter_recurrence_montant = '';


  // Cree un nonce pour les formulaires
    $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_recurrence', 10), true);


  // Verifie les pre-action
    switch ($action) {
      case 'ajouter_recurrence':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $recurrence_id = -1;
          $recurrence_date_debut = ObtenirValeur('ajouter_recurrence_date_debut', '');
          $recurrence_date_fin = ObtenirValeur('ajouter_recurrence_date_fin', '');
          $recurrence_auto_type_interval = ObtenirValeur('ajouter_recurrence_auto_type_interval', 0);
          $recurrence_auto_interval = ObtenirValeur('ajouter_recurrence_auto_interval', 0);
          $recurrence_type_interval = ObtenirValeur('ajouter_recurrence_type_interval', 0);
          $recurrence_interval = ObtenirValeur('ajouter_recurrence_interval', 0);
          $recurrence_compte_id = ObtenirValeur('ajouter_recurrence_compte_id', 0);
          $recurrence_type_transaction_id = ObtenirValeur('ajouter_recurrence_type_transaction_id', 0);
          $recurrence_transfert_compte_id = ObtenirValeur('ajouter_recurrence_transfert_compte_id', 0);
          $recurrence_type_depense_id = ObtenirValeur('ajouter_recurrence_type_depense_id', 0);
          $recurrence_description = ObtenirValeur('ajouter_recurrence_description', '');
          $recurrence_notes = ObtenirValeur('ajouter_recurrence_notes', '');
          $recurrence_symbole = ObtenirValeur('ajouter_recurrence_symbole', 0);
          $recurrence_montant = abs(ObtenirValeur('ajouter_recurrence_montant', 0));
          $recurrence_modifierpasser = 0;
        // Defini quelques valeurs par default
          $bRet = false;
          $msg = '';
          $valide = true;
        // Valide quelques valeurs
          if ( ! $cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Ajout
          if ($valide) {
            $bRet = AjouterModifierEffacerRecurrence('ajouter', $msg, $recurrence_id,
                         $recurrence_date_debut, $recurrence_date_fin,
                         $recurrence_auto_type_interval, $recurrence_auto_interval,
                         $recurrence_type_interval, $recurrence_interval,
                         $recurrence_compte_id,
                         $recurrence_type_transaction_id, $recurrence_transfert_compte_id,
                         $recurrence_type_depense_id, $recurrence_description, $recurrence_notes,
                         $recurrence_symbole, $recurrence_montant,
                         $recurrence_modifierpasser);
          } else {
            $bRet = false;
          }
        // Resultat
          if ($bRet == false) {
            $erreur_msg .= $msg;
            $ajouter_recurrence_date_debut = $recurrence_date_debut;
            $ajouter_recurrence_date_fin = $recurrence_date_fin;
            $ajouter_recurrence_auto_type_interval = $recurrence_auto_type_interval;
            $ajouter_recurrence_auto_interval = $recurrence_auto_interval;
            $ajouter_recurrence_type_interval = $recurrence_type_interval;
            $ajouter_recurrence_interval = $recurrence_interval;
            $ajouter_recurrence_compte_id = $recurrence_compte_id;
            $ajouter_recurrence_type_transaction_id = $recurrence_type_transaction_id;
            $ajouter_recurrence_transfert_compte_id = $recurrence_transfert_compte_id;
            $ajouter_recurrence_type_depense_id = $recurrence_type_depense_id;
            $ajouter_recurrence_description = $recurrence_description;
            $ajouter_recurrence_notes = $recurrence_notes;
            $ajouter_recurrence_symbole = $recurrence_symbole;
            $ajouter_recurrence_montant = $recurrence_montant;
          } else {
            $ajout_msg .= $msg;
          }
          $action = 'list';
        break;


      case 'modifier_recurrence':
      case 'effacer_recurrence':
        // Obtient quelques valeurs
          $modifier_recurrence_id = ObtenirValeur('modifier_recurrence_id', -1);
          $effacer_recurrence_id = ObtenirValeur('effacer_recurrence_id', -1);
        // Valide quelques valeurs
          if (is_numeric($modifier_recurrence_id) && $action == 'modifier_recurrence') {
            $recurrence_id = $modifier_recurrence_id;
          } elseif (is_numeric($effacer_recurrence_id) && $action == 'effacer_recurrence') {
            $recurrence_id = $effacer_recurrence_id;
          } else {
            $recurrence_id = -1;
          }
        // Verifie si la recurrence existe
          $sql = "SELECT * FROM recurrence WHERE id = " . $recurrence_id . "";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
        // Obtient les valeurs
          $c = mysqli_num_rows($requete_resultat);
          if ($c != 1) {
            $recurrence_id = -1;
          }
        // Attribut les valeurs
          if ($action == 'modifier_recurrence') {
            $modifier_recurrence_id = $recurrence_id;
            $effacer_recurrence_id = -1;
          } elseif ($action == 'effacer_recurrence') {
            $effacer_recurrence_id = $recurrence_id;
            $modifier_recurrence_id = -1;
          } else {
            $modifier_recurrence_id = -1;
            $effacer_recurrence_id = -1;
          }
        // Affiche la liste des recurrence
          $action = 'list';
        break;

      case 'modifier_recurrence_confirme':
        // Obtiens quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $recurrence_id = ObtenirValeur('recurrence_id', -1);
          $recurrence_date_debut = ObtenirValeur('recurrence_date_debut', '');
          $recurrence_date_fin = ObtenirValeur('recurrence_date_fin', '');
          $recurrence_auto_type_interval = ObtenirValeur('recurrence_auto_type_interval', 0);
          $recurrence_auto_interval = ObtenirValeur('recurrence_auto_interval', 0);
          $recurrence_type_interval = ObtenirValeur('recurrence_type_interval', 0);
          $recurrence_interval = ObtenirValeur('recurrence_interval', 0);
          $recurrence_compte_id = ObtenirValeur('recurrence_compte_id', 0);
          $recurrence_type_transaction_id = ObtenirValeur('recurrence_type_transaction_id', 0);
          $recurrence_transfert_compte_id = ObtenirValeur('recurrence_transfert_compte_id', 0);
          $recurrence_type_depense_id = ObtenirValeur('recurrence_type_depense_id', 0);
          $recurrence_description = ObtenirValeur('recurrence_description', '');
          $recurrence_notes = ObtenirValeur('recurrence_notes', '');
          $recurrence_symbole = ObtenirValeur('recurrence_symbole', 0);
          $recurrence_montant = abs(ObtenirValeur('recurrence_montant', 0));
          $recurrence_modifierpasser = ObtenirValeur('recurrence_modifierpasser', 0);
        // Defini quelques valeurs par default
          $bRet = false;
          $msg = '';
          $valide = true;
        // Valide quelques valeurs
          if ( ! $cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Modifie la recurrence
          if ($valide) {
            $bRet = AjouterModifierEffacerRecurrence('modifier', $msg, $recurrence_id,
                         $recurrence_date_debut, $recurrence_date_fin,
                         $recurrence_auto_type_interval, $recurrence_auto_interval,
                         $recurrence_type_interval, $recurrence_interval,
                         $recurrence_compte_id,
                         $recurrence_type_transaction_id, $recurrence_transfert_compte_id,
                         $recurrence_type_depense_id, $recurrence_description, $recurrence_notes,
                         $recurrence_symbole, $recurrence_montant,
                         $recurrence_modifierpasser);
          } else {
            $bRet = false;
          }
          if ($bRet == false) {
            $erreur_msg .= $msg;
          } else {
            $ajout_msg .= $msg;
          }
        // Affiche la liste des recurrence
          $action = 'list';
        break;
      case 'effacer_recurrence_confirme':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $recurrence_id = ObtenirValeur('recurrence_id', -1);
          $recurrence_date_debut = '';
          $recurrence_date_fin = '';
          $recurrence_auto_type_interval = 0;
          $recurrence_auto_interval = 0;
          $recurrence_type_interval = 0;
          $recurrence_interval = 0;
          $recurrence_compte_id = 0;
          $recurrence_type_transaction_id = 0;
          $recurrence_transfert_compte_id = 0;
          $recurrence_type_depense_id = 0;
          $recurrence_description = '';
          $recurrence_notes = '';
          $recurrence_symbole = 0;
          $recurrence_montant = 0;
          $recurrence_modifierpasser = ObtenirValeur('recurrence_modifierpasser', 0);
        // Defini quelques valeurs par default
          $bRet = false;
          $msg = '';
          $valide = true;
        // Valide quelques valeurs
          if ( ! $cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
        // Efface la recurrence
          if ($valide) {
            $bRet = AjouterModifierEffacerRecurrence('effacer', $msg, $recurrence_id,
                         $recurrence_date_debut, $recurrence_date_fin,
                         $recurrence_auto_type_interval, $recurrence_auto_interval,
                         $recurrence_type_interval, $recurrence_interval,
                         $recurrence_compte_id,
                         $recurrence_type_transaction_id, $recurrence_transfert_compte_id,
                         $recurrence_type_depense_id, $recurrence_description, $recurrence_notes,
                         $recurrence_symbole, $recurrence_montant,
                         $recurrence_modifierpasser);
          } else {
            $bRet = false;
          }
          if ($bRet == false) {
            $erreur_msg .= $msg;
          } else {
            $ajout_msg .= $msg;
          }
        // Affiche la liste des recurrence
          $action = 'list';
        break;



      case 'deplacer_recurrence':
        // Obtient quelques valeurs
          $recurrence_id = ObtenirValeur('recurrence_id', '');
          $ordre = ObtenirValeur('ordre', '');
          $valid = true;
        // Valide quelques valeurs
          if ( !is_numeric($recurrence_id) ||
                !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valid = false;
          }
        // Met a jour la recurrence
          if ($valid) {
            $sql  = ' UPDATE recurrence SET ordre=' . $ordre . ' WHERE id=' . $recurrence_id . ' ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre de la ligne s&eacute;lectionner.<BR>";
            } else {
              $ajout_msg .= 'Ordre de la ligne s&eacute;lectionner mise &agrave; jour.<BR>';
            }
          }
        // Met a jour l'ordre globale
          if ($valid) {
            $sql  = ' SET @rank=0; ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            $sql  = ' UPDATE recurrence SET ordre=@rank:=(@rank+2) ';
            $sql .= ' ORDER BY ordre ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre des r&eacute;currences.<BR>";
            } else {
              $ajout_msg .= 'Ordre des r&eacute;currences mise &agrave; jour.<BR>';
            }
          }
        break;
    } // Fin des pre-action




  // Enleve quelques valeurs dans le URL
    $url_actuelle = FunctionURLChange($url_actuelle, 'action', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'modifier_recurrence_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'effacer_recurrence_id', '' );




  // Affiche la liste des recurrences
    // Requete SQL
    $sql = "SELECT * FROM recurrence ORDER BY ordre";
    // Execute la requete SQL
    $requete_resultat = mysqli_query($mysql_conn, $sql);
    if (!$requete_resultat) {
      die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
    }
    // Initialise quelques variables
    $lignes = array();
    $estPremier = true;
    $dernierID  = 0;
    $numeroLigne = 0;
    $ligne_virtuel = false;
    $afficherjusquau = null;
    // Boucle dans les resultats
    if (mysqli_num_rows($requete_resultat) > 0) {
      if ($ajout_msg != '') {
        $ajout_msg .= "<br />";
      }
      while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
        // Genere les nouvelles entrees de depense
        recurrenceDepenseEntree( $ligne['id'], $ligne['compte_id'], $afficherjusquau, $ligne_virtuel, $ajout_msg, $erreur_msg );

        // Ajoute quelque valeurs au resultat
        $numeroLigne += 1;
        $ligne ['numeroLigne'] = $numeroLigne;
        $ligne ['estPremier'] = $estPremier; $estPremier = false;
        $ligne ['estDernier'] = false;
        $ligne ['date_debut'] = DateSeulement($ligne ['date_debut']);
        $ligne ['date_fin'] = DateSeulement($ligne ['date_fin']);
        $ligne ['auto_type_interval_text'] = $auto_type_interval_list[ $ligne ['auto_type_interval']  ];
        $ligne ['auto_interval'] = $ligne ['auto_interval_valeur'];
        $ligne ['type_interval_text'] = $type_interval_list[ $ligne ['type_interval']  ];
        $ligne ['interval'] = $ligne ['interval_valeur'];
        $ligne ['compte_id_text'] = $liste_compte[ $ligne ['compte_id']  ]['description'];
        $ligne ['type_transaction_id_text'] = $liste_type_transaction[ $ligne ['type_transaction_id']  ]['description'];
        $ligne ['transfert_compte_id_text'] = $liste_compte[ $ligne ['transfert_compte_id']  ]['description'];
        $ligne ['type_depense_id_text'] = $liste_type_depense[ $ligne ['type_depense_id']  ]['description'];
	      $ligne ['symbole'] = ($ligne['montant'] < 0 ? 0 : 1);
        $ligne ['symbole_text'] = $liste_symbole[ $ligne ['symbole']  ];
        $ligne ['montant'] = abs($ligne['montant']);
        $ligne ['montantformat'] = formatMonnaie(abs($ligne ['montant']), $ligne ['symbole']);
        $dernierID = count($lignes);
        $lignes[ count ($lignes) ] = $ligne;
      }
      $lignes[ $dernierID ]['estDernier'] = true;
    }
    $smarty->assign('recurrence_list', $lignes, true);


  // Assigne des variables au templates
    $smarty->assign('modifier_recurrence_id', $modifier_recurrence_id, true);
    $smarty->assign('effacer_recurrence_id', $effacer_recurrence_id, true);
    $smarty->assign('defilerauid', $defilerauid, true);
  // Liste specifique au recurrence
    $smarty->assign('type_interval_list', $type_interval_list, true);
    $smarty->assign('auto_type_interval_list', $auto_type_interval_list, true);
  // Variale specifique au recurrence
    $smarty->assign('ajouter_recurrence_date_debut', $ajouter_recurrence_date_debut, true);
    $smarty->assign('ajouter_recurrence_date_fin', $ajouter_recurrence_date_fin, true);
    $smarty->assign('ajouter_recurrence_auto_type_interval', $ajouter_recurrence_auto_type_interval, true);
    $smarty->assign('ajouter_recurrence_auto_interval', $ajouter_recurrence_auto_interval, true);
    $smarty->assign('ajouter_recurrence_type_interval', $ajouter_recurrence_type_interval, true);
    $smarty->assign('ajouter_recurrence_interval', $ajouter_recurrence_interval, true);
    $smarty->assign('ajouter_recurrence_compte_id', $ajouter_recurrence_compte_id, true);
    $smarty->assign('ajouter_recurrence_type_transaction_id', $ajouter_recurrence_type_transaction_id, true);
    $smarty->assign('ajouter_recurrence_transfert_compte_id', $ajouter_recurrence_transfert_compte_id, true);
    $smarty->assign('ajouter_recurrence_type_depense_id', $ajouter_recurrence_type_depense_id, true);
    $smarty->assign('ajouter_recurrence_description', $ajouter_recurrence_description, true);
    $smarty->assign('ajouter_recurrence_notes', $ajouter_recurrence_notes, true);
    $smarty->assign('ajouter_recurrence_symbole', $ajouter_recurrence_symbole, true);
    $smarty->assign('ajouter_recurrence_montant', $ajouter_recurrence_montant, true);




  // Defini le fichier template
    $template_fichier = 'recurrence.tpl';










  // Function pour ajouter/modifier/effacer une recurrence dans la DB et aussi modifier les depenses associe
  function AjouterModifierEffacerRecurrence($action, &$msg,
                                 $recurrence_id,
                                 $recurrence_date_debut, $recurrence_date_fin,
                                 $recurrence_auto_type_interval, $recurrence_auto_interval,
                                 $recurrence_type_interval, $recurrence_interval,
                                 $recurrence_compte_id, $recurrence_type_transaction_id, $recurrence_transfert_compte_id,
                                 $recurrence_type_depense_id, $recurrence_description, $recurrence_notes,
                                 $recurrence_symbole, $recurrence_montant,
                                 $recurrence_modifierpasser) {
    // Variable globale
      global $mysql_conn;
      global $aujourdhui;
      global $maintenant;
      global $type_transaction_transfert;
    
    // Initialise quelques variables
      $msg = '';
      $valid = true;

    // Valide quelques valeurs
      if (!is_numeric($recurrence_id) ) {
        $valid = false;
        $msg .= 'ID invalide.<BR>';
      }
      if (!is_numeric($recurrence_modifierpasser)) {
        $valid = false;
        $msg .= '"Edit pass" invalide.<BR>';
      }
      if ($action == 'ajouter' || $action == 'modifier') {
        if (!verifierFormatDate($recurrence_date_debut) ) {
          $valid = false;
          $msg .= 'Date de d&eacute;but invalide.<BR>';
        }
        if ($recurrence_date_fin != '' && !verifierFormatDate($recurrence_date_fin) ) {
          $valid = false;
          $msg .= 'Date de fin invalide.<BR>';
        }
        if ($recurrence_date_fin != '' && $recurrence_date_debut > $recurrence_date_fin) {
          $valid = false;
          $msg .= 'Date de fin avant celle de d&eacute;but.<BR>';
        }

        if (!is_numeric($recurrence_auto_type_interval) ) {
          $valid = false;
          $msg .= 'Auto type interval non num&eacute;rique.<BR>';
        }
        if (!is_numeric($recurrence_auto_interval) ) {
          $valid = false;
          $msg .= 'Auto interval non num&eacute;rique.<BR>';
        }

        if (!is_numeric($recurrence_type_interval) ) {
          $valid = false;
          $msg .= 'Type interval non num&eacute;rique.<BR>';
        }
        if (!is_numeric($recurrence_interval) ) {
          $valid = false;
          $msg .= 'Interval non num&eacute;rique.<BR>';
        }

        if (!compte_id_est_valide($recurrence_compte_id) ) {
          $valid = false;
          $msg .= 'Compte invalide.<BR>';
        }
        if (!type_transaction_id_est_valide($recurrence_type_transaction_id) ) {
          $valid = false;
          $msg .= 'Type de transaction invalide.<BR>';
        } else if ($recurrence_type_transaction_id != $type_transaction_transfert) {
          $recurrence_transfert_compte_id = 0;
        }
        if (!compte_id_est_valide($recurrence_transfert_compte_id) ) {
          $valid = false;
          $msg .= 'Compte de transfert invalide.<BR>';
        }
        if (!type_depense_id_est_valide($recurrence_type_depense_id) ) {
          $valid = false;
          $msg .= 'Type de d&eacute;pense invalide.<BR>';
        }

        if ($recurrence_description == '') {
          $valid = false;
          $msg .= 'La description ne peux pas &ecirc;tre vide.<BR>';
        }
        if ($recurrence_notes == '') {
          // La note peut etre vide
        }

        if (!symbole_est_valide($recurrence_symbole) ) {
          $valid = false;
          $msg .= 'symbole invalide.<BR>';
        }
        if (!montant_est_valide($recurrence_montant) ) {
          $valid = false;
          $msg .= 'Montant invalide.<BR>';
        }

        if ($recurrence_type_transaction_id == $type_transaction_transfert && $recurrence_compte_id == $recurrence_transfert_compte_id) {
          $valid = false;
          $msg .= "Le compte de transfert ne peux pas &ecirc;tre le m&ecirc;me que le compte de source.<br>";
        }
      } else { /* if ($action == 'del' ) { */
        $recurrence_date_debut = $aujourdhui;
        $recurrence_date_fin = $aujourdhui;
      }

    // Verifie si la validation a passer
      if (!$valid) {
        return false;
      }

    // Verifie les dates
      // Date de depart
      $recurrence_date_debut_timestamp = strtotime($recurrence_date_debut);
      $recurrence_date_debut_full = date('Y-m-d H:i:s', $recurrence_date_debut_timestamp);
      // Date de fin
      if ($recurrence_date_fin == '') {
        // Si aucune date de fin saisie
        $recurrence_date_fin_full = "null";
      } else {
        $recurrence_date_fin_timestamp = strtotime($recurrence_date_fin);
        $recurrence_date_fin_full = "'" . date('Y-m-d H:i:s', $recurrence_date_fin_timestamp) . "'";
      }

    // Verifie l'action
      if ($action == 'ajouter') {
        // Ajoute la recurrence a la DB
          $sql = "";
          $sql .= "INSERT INTO recurrence (date_ajouter, date_debut, date_fin, auto_type_interval, auto_interval_valeur, type_interval, interval_valeur, ";
          $sql .= "                        compte_id, type_transaction_id, transfert_compte_id, type_depense_id, description, notes, montant, ordre) VALUES (";
          $sql .= " '" . $maintenant . "', '$recurrence_date_debut_full', $recurrence_date_fin_full, ";
          $sql .= " $recurrence_auto_type_interval, $recurrence_auto_interval, ";
          $sql .= " $recurrence_type_interval, $recurrence_interval, ";
          $sql .= " $recurrence_compte_id, $recurrence_type_transaction_id, $recurrence_transfert_compte_id, ";
          $sql .= " $recurrence_type_depense_id, '$recurrence_description', '" . $recurrence_notes . "', " . ($recurrence_symbole == 0 ? -$recurrence_montant: $recurrence_montant) . ", ";
          $sql .= " 0 )";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
          $id =  mysqli_insert_id($mysql_conn);
          $msg .= 'Entr&eacute;e pincipale cr&eacute;er.<BR>';


      } elseif ($action == 'modifier') {
        // Modifie la recurrence dans la DB
          $sql = "";
          $sql .= "UPDATE recurrence SET ";
          //$sql .= "date_debut = '$recurrence_date_debut_full', ";
          $sql .= "date_fin = $recurrence_date_fin_full, ";
          $sql .= "auto_type_interval = $recurrence_auto_type_interval , ";
          $sql .= "auto_interval_valeur = $recurrence_auto_interval , ";
          $sql .= "type_interval = $recurrence_type_interval , ";
          $sql .= "interval_valeur = $recurrence_interval , ";
          //$sql .= "type_transaction_id = $recurrence_type_transaction_id , ";
          $sql .= "compte_id = $recurrence_compte_id , ";
          $sql .= "transfert_compte_id = $recurrence_transfert_compte_id, ";
          $sql .= "type_depense_id = $recurrence_type_depense_id , ";
          $sql .= "description = '$recurrence_description', ";
          $sql .= "montant = " . ($recurrence_symbole == 0 ? -$recurrence_montant: $recurrence_montant) . ", ";
          //if ($recurrence_type_transaction_id != $type_transaction_transfert)  {
          //  $sql .= "symbole = $recurrence_symbole, ";
          //}
          $sql .= "notes = '$recurrence_notes' ";
          $sql .= " WHERE id = $recurrence_id ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
          $msg .= 'Entr&eacute;e pincipale mise &agrave; jour.<BR>';
        // Met a jour les entrees de depenses existante non pointer et non reconcillier
          $montant = ($recurrence_symbole == 0 ? -$recurrence_montant: $recurrence_montant);
          $sql = "";
          $sql .= "UPDATE depenses SET ";
          $sql .= "  type_depense_id = $recurrence_type_depense_id , ";
          $sql .= "  description = '$recurrence_description', ";
          $sql .= "  notes = '$recurrence_notes', ";
          $sql .= "  montant = CASE WHEN compte_id = " . $recurrence_compte_id . " ";
          $sql .= "                 THEN " . $montant . " ";
          $sql .= "                 ELSE " . -$montant . " ";
          $sql .= "                 END ";
          $sql .= " WHERE recurrence_id = $recurrence_id  ";
          $sql .= "   AND reconcilier_depense_id = 0 ";
          $sql .= "   AND pointer = 0 ";
          if ($recurrence_modifierpasser == 0) {
            $sql .= " AND date_depense >= '" . $aujourdhui . "'";
          }
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
          $msg .= 'Les d&eacute;penses ont &eacute;t&eacute; mise &agrave; jour.<BR>';


      } elseif ($action == 'effacer') {
        // Efface la recurrence dans la DB
          $sql = "";
          $sql .= "DELETE FROM recurrence ";
          $sql .= " WHERE id = $recurrence_id ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
          $msg .= 'Entr&eacute;e pincipale effacer.<BR>';
        // Efface les entrees de depense non pointer et non reconcillier
          $sql = "";
          $sql .= "DELETE FROM depenses ";
          $sql .= " WHERE recurrence_id = $recurrence_id ";
          $sql .= "   AND reconcilier_depense_id = 0 ";
          $sql .= "   AND pointer = 0 ";
          if ($recurrence_modifierpasser == 0) {
            $sql .= " AND date_depense >= '" . $aujourdhui . "'";
          }
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
          $msg .= 'Les d&eacute;penses ont &eacute;t&eacute; effacer.<BR>';

      } else {
        $msg .= 'function not found !<BR>';
        return false;
      }

    // Retourne
      return true;
  }

?>
