<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', 'liste');


  // Verifie les pre-action
    switch ($action) {
      case 'transaction_deplacer':
        // Obtient quelques valeurs
          $type_transaction_id = ObtenirValeur('type_transaction_id', '');
          $ordre = ObtenirValeur('ordre', '');
          $valide = true;
        // Valide quelques valeurs
          if ( !is_numeric($type_transaction_id) ||
               !is_numeric($ordre) ) {
            $erreur_msg .= 'Valeur non num&eacute;rique.<BR>';
            $valide = false;
          }
        // Met a jour de l'ordre du type de transaction
          if ($valide) {
            $sql  = ' UPDATE type_transaction SET ordre=' . $ordre . ' WHERE id=' . $type_transaction_id . ' ';
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
            $sql  = ' UPDATE type_transaction SET ordre=@rank:=(@rank+2) ';
            $sql .= ' ORDER BY ordre ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $errerreur_erreur_msgmsg_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre.<BR>";
            } else {
              $ajout_msg .= 'Ordre mise &agrave; jour.<BR>';
            }
          }
        // Actualise les listes
          ObtenirListeTypeTransaction();
        break;
    } // Fin des pre-action


  // Enleve quelques valeurs dans le URL
    $url_actuelle = FunctionURLChange($url_actuelle, 'action', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'type_transaction_id', '' );
    $url_actuelle = FunctionURLChange($url_actuelle, 'ordre', '' );


  // Defini le fichier template
    $template_fichier = 'type_transaction.tpl';
?>
