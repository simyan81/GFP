<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', 'liste');


  // Verifie les pre-action
    switch ($action) {
      case 'compte_deplacer':
        // Obtient quelques valeurs
          $type_compte_id = ObtenirValeur('type_compte_id', '');
          $ordre = ObtenirValeur('ordre', '');
          $valide = true;
        // Valide quelques valeurs
          if ( !is_numeric($type_compte_id) ||
               !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
        // Met a jour le compte
          if ($valide) {
            $sql  = ' UPDATE type_compte SET ordre=' . $ordre . ' WHERE id=' . $type_compte_id . ' ';
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
            $sql  = ' UPDATE type_compte SET ordre=@rank:=(@rank+2) ';
            $sql .= ' ORDER BY ordre ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre.<BR>";
            } else {
              $ajout_msg .= 'Ordre mise &agrave; jour.<BR>';
            }
          }
        // Actualise les listes
          ObtenirListeTypeCompte();
        break;
    } // Fin des pre-action

  
  // Enleve quelques valeurs dans le URL
    $url_actuelle = FunctionURLChange($url_actuelle, 'action', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'type_compte_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'ordre', '' );

    
  // Defini le fichier template
    $template_fichier = 'type_compte.tpl';
?>
