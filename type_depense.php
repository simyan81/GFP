<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', 'liste');
    $modifier_type_depense_id = ObtenirValeur('modifier_type_depense_id', '-1');
    $effacer_type_depense_id = ObtenirValeur('effacer_type_depense_id', '-1');
    if (!is_numeric($modifier_type_depense_id)) {
      $modifier_type_depense_id = -1;
    }
    if (!is_numeric($effacer_type_depense_id) || $modifier_type_depense_id > -1) {
      $effacer_type_depense_id = -1;
    }


  // Cree un nonce pour les formulaires
    $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_depense', 10), true);


  // Verifie les pre-action
    switch ($action) {
      case 'ajouter_type_depense':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $description = ObtenirValeur('ajouter_depense_description', '');
          $symbole = ObtenirValeur('ajouter_depense_symbole', 0);
          $ordre = ObtenirValeur('ajouter_depense_ordre', 0);
        // Valide quelques valeurs
          $valide = true;
          if ( ! $cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
          if ($description == '') {
            $erreur_msg .= 'Description invalide.<BR>';
            $valide = false;
          }
          if (!symbole_est_valide($symbole) ||
              !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
        // Ajouter le type de depense a la DB
          if ($valide) {
            $sql  = " INSERT INTO type_depense (date_ajouter, description, symbole, ordre) VALUES (";
            $sql .= "  '" . $maintenant . "', ";
            $sql .= "  '" . addslashes($description) . "', ";
            $sql .= "   " . $symbole . ", ";
            $sql .= "   " . $ordre . " ) ";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
            } else {
              $ajout_msg .= 'Type de d&eacute;pense ajout&eacute;.<BR>';
            }
          }
        // Met a jour l'ordre globale
          if ($valide) {
            $sql  = ' SET @rank=0; ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            $sql  = ' UPDATE type_depense SET ordre=@rank:=(@rank+2) ';
            $sql .= ' ORDER BY ordre, description ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre des type de d&eacute;pense.<BR>";
            } else {
              $ajout_msg .= "L\'ordre des type de d&eacute;pense mise &agrave; jour.<BR>";
            }
          }
        // Actualise les listes
          ObtenirListeTypeDepense();
        break;

      case 'modifier_type_depense':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $type_depense_id = ObtenirValeur('type_depense_id', '');
          $description = ObtenirValeur('modifier_type_depense_description', '');
          $symbole = ObtenirValeur('modifier_type_depense_symbole', 0);
          $valide = true;
        // Valide quelque valeurs
          if ( ! $cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
          if ( !is_numeric($type_depense_id) ||
               !is_numeric($symbole) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
          if (!symbole_est_valide($symbole) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
        // Type de depense systeme
         if ($type_depense_id  <= 50) {
           $erreur_msg .= "Impossible de modifier un type de dépense système<br>";
           $valide = false;
         }
        // Modifie le type de depense dans la DB
          if ($valide) {
            $sql  = " UPDATE type_depense SET ";
            $sql .= "   description='" . addslashes($description) . "', ";
            $sql .= "   symbole = " . $symbole . " ";
            $sql .= "  WHERE id = " . $type_depense_id . " ";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de la ligne s&eacute;lectionner.<BR>";
            } else {
              $ajout_msg .= 'La ligne s&eacute;lectionner &agrave; &eacute;t&eacute; mise &agrave; jour.<BR>';
            }
            $ajout_msg .= $sql;
          }
        // Actualise les listes
          ObtenirListeTypeDepense();
        break;

      case 'effacer_type_depense':
        // Obtient quelques valeurs
          $nonce = ObtenirValeur('nonce', '');
          $type_depense_id = ObtenirValeur('type_depense_id', '');
          $valide = true;
        // Valide quelques
          if ( ! $cnonce->verifyNonce($nonce) ) {
            $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
            $valide = false;
          }
          if ( !is_numeric($type_depense_id) ) {
            $ajout_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
        // Type de depense systeme
         if ($type_depense_id  <= 50) {
           $erreur_msg .= "Impossible d'effacer un type de dépense système<br>";
           $valide = false;
         }
        // Efface le type de depense dans la DB
          if ($valide) {
            $sql  = " DELETE FROM type_depense WHERE id=" . $type_depense_id . " ";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la suppression de la ligne s&eacute;lectionner.<BR>";
            } else {
              $ajout_msg .= 'La ligne s&eacute;lectionner &agrave; &eacute;t&eacute; supprim&eacute;.<BR>';
            }
          }
        // Actualise les listes
          ObtenirListeTypeDepense();
        break;

      case 'deplacer_type_depense':
        // Obtient quelques valeurs
          $type_depense_id = ObtenirValeur('type_depense_id', '');
          $ordre = ObtenirValeur('ordre', '');
          $valide = true;
        // Valide quelques valeurs
          if ( !is_numeric($type_depense_id) ||
                !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
        // Met a jour le type de depense
          if ($valide) {
            $sql  = ' UPDATE type_depense SET ordre=' . $ordre . ' WHERE id=' . $type_depense_id . ' ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre de la ligne s&eacute;lectionner.<BR>";
            } else {
              $ajout_msg .= 'Ordre de la ligne s&eacute;lectionner mise &agrave; jour.<BR>';
            }
          }
        // Met a jour l'ordre globale
          if ($valide) {
            $sql  = ' SET @rank=0; ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            $sql  = ' UPDATE type_depense SET ordre=@rank:=(@rank+2) ';
            $sql .= ' ORDER BY ordre, description ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre des type de d&eacute;pense.<BR>";
            } else {
              $ajout_msg .= 'Ordre des type de d&eacute;pense mise &agrave; jour.<BR>';
            }
          }
        // Actualise les listes
          ObtenirListeTypeDepense();
        break;

    } // Fin des pre-action


  // Enleve quelques valeurs dans le URL
    $url_actuelle = FunctionURLChange($url_actuelle, 'action', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'type_depense_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'ordre', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'modifier_type_depense_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'effacer_type_depense_id', '' );



  // Affiche la liste des types de depense
    // Requete SQL
    $sql  = 'SELECT * FROM type_depense ORDER BY ordre, description';
    // Execute la requete SQL
    $requete_resultat = mysqli_query($mysql_conn, $sql);
    // Verifie les erreurs
    if (!$requete_resultat) {
      die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
    }
    // Boucle dans les resultats
    $listordre = array();
    $listordre[ 1 ] = 'En premier';
    while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
      $listordre[ $ligne['ordre'] + 1 ] = 'Apr&egrave;s ' . myslashes($ligne['description']);
    }
    mysqli_free_result($requete_resultat);
    $smarty->assign('listordre', $listordre, true);


  // Ajoute quelques valeurs a Smarty
    $smarty->assign('modifier_type_depense_id', $modifier_type_depense_id, true);
    $smarty->assign('effacer_type_depense_id', $effacer_type_depense_id, true);


  // Defini le fichier template
    $template_fichier = 'type_depense.tpl';
?>
