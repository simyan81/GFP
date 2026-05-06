<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


$ajout_msg .= "";


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', '');


  // Cree un nonce pour les formulaires
    $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_init', 10), true);


  // Verifie les action
    if ($action == 'init') {
      // Obtiens quelques valeurs
      $nonce = ObtenirValeur('nonce', '');
      // Verifie le nonce
      $valide = true;
      if ( !$cnonce->verifyNonce($nonce) ) {
        $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
        $valide = false;
      }
      // Initialise la DB
      if ($valide) {
        $erreur_msg .= "Initialisation de la base de donn&eacute;<br>";
        $confirm = ObtenirValeur('confirm', '');
        $sauvegarde = urldecode(ObtenirValeur('sauvegarde', ''));
        if ($confirm == 'true') {
          $cmd = " mysql --host=localhost --user=" . $mysql_user . " --password=" . $mysql_pass . " --database " . $_SESSION['db'] . " < init.sql";
          $r = exec ($cmd);
          if ($r === false) {
            $erreur_msg .= "Initialisation &eacute;&eacute;é<br />";
          } elseif ($r != "") {
            $erreur_msg .= "Initialisation r&eacute;ussi<br />";
          } else {
            $erreur_msg .= "Initialisation r&eacute;ussi ($r)<br />";
          }
        } else {
          //$erreur_msg .='<a href="' . $url .'&page=init&action=init&confirm=true">OUI</a>';
          $erreur_msg .= " <a href='" . $url_base . "' data-parametreurl=' {\"action\": \"init\", \"confirm\": \"true\" } ' title='Initialisation'>OUI</a> ";
          $erreur_msg .= '<br />ATTENTION, LA BASE DE DONN&Eacute;E ACTUELLE SERA EFFACER.';
        }
      }


    } elseif ($action == 'sauvegarde') {
      // Obtiens quelques valeurs
      $nonce = ObtenirValeur('nonce', '');
      // Verifie le nonce
      $valide = true;
      if ( !$cnonce->verifyNonce($nonce) ) {
        $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
        $valide = false;
      }
      // Creer une sauvegarde
      if ($valide) {
        $erreur_msg = "Sauvegarde de la base de donn&eacute;<br />";
        $sauvegarde = dirname(__FILE__) . "/backup/backup-" .  $_SESSION['db'] . "-" . date("Y-m-d") . ".sql";
        $db_nom = $_SESSION['db'];
        // --no-create-info
        $cmd = " mysqldump --skip-add-drop-table --no-tablespaces --user=" . $mysql_user . " --password=" . $mysql_pass . " --host=localhost $db_nom ";
        $cmd .= " | sed -r 's/CREATE TABLE (`[^`]+`)/TRUNCATE TABLE \\1; CREATE TABLE IF NOT EXISTS \\1/g' ";
        $cmd .= " > " . $sauvegarde ;
        $r = exec ($cmd);
        if ($r === false) {
          $erreur_msg .= "Sauvegarde &eacute;&eacute;é<br />";
        } elseif ($r != "") {
          $erreur_msg .= "Sauvegarde r&eacute;ussi<br />";
        } else {
          $erreur_msg .= "Sauvegarde r&eacute;ussi ($r)<br />";
        }
      }


    } elseif ($action == 'effacer') {
      // Efface une sauvegarde
      $erreur_msg = "Effacement d'une sauvegarde<br />";
      $sauvegarde = urldecode(ObtenirValeur('sauvegarde', ''));
      $confirm = ObtenirValeur('confirm', '');
      if ($confirm == 'true') {
        if ( is_file(dirname(__FILE__) . "/backup/" . $sauvegarde) ) {
          if ( unlink(dirname(__FILE__) . "/backup/" . $sauvegarde) ) {
            $erreur_msg .= 'Sauvegarde effacer';
          } else {
            $erreur_msg .= 'Échec de l\'effacement';
          }
        } else {
          $erreur_msg .= 'Sauvegarde introuvable';
        }
      } else {
        $erreur_msg .= 'Effacer ' . $sauvegarde . ' : ';
        //$erreur_msg .= '<a href="' . $url .'&page=init&action=effacer&sauvegarde=' . urlencode($sauvegarde) . '&confirm=true">OUI</a>';
        $erreur_msg .= " <a href='" . $url_base . "' data-parametreurl=' {\"action\": \"effacer\", \"confirm\": \"true\", \"sauvegarder\": \"" . urlencode($sauvegarde) . "\" } ' title='Effacer'>OUI</a> ";
      }


    } elseif ($action == 'restaurer') {
      // Restaure une sauvegarde
      $erreur_msg = "Restauration d'une sauvegarde<br />";
      $sauvegarde = urldecode(ObtenirValeur('sauvegarde', ''));
      $confirm = ObtenirValeur('confirm', '');
      if ($confirm == 'true') {
        if ( is_file(dirname(__FILE__) . "/backup/" . $sauvegarde) ) {
          $db_nom = $_SESSION['db'];
          $cmd = " mysql --user=" . $mysql_user . " --password=" . $mysql_pass . " --host=localhost --database $db_nom < " . dirname(__FILE__) . "/backup/" . $sauvegarde;
          $erreur_msg .= $cmd . "<br />";
          $r = exec ($cmd);
          if ($r === false) {
            $erreur_msg .= 'Échec de la restauration de la sauvegarde';
          } else {
            $erreur_msg .= 'Sauvegarde restaurer';
          }
        } else {
          $erreur_msg .= 'Sauvegarde introuvable';
        }
      } else {
        $erreur_msg .= 'Restauré ' . $sauvegarde . ' : ';
        //$erreur_msg .= '<a href="' . $url .'&page=init&action=restaurer&sauvegarde=' . urlencode($sauvegarde) . '&confirm=true">OUI</a>';
        $erreur_msg .= " <a href='" . $url_base . "' data-parametreurl=' {\"action\": \"restaurer\", \"confirm\": \"true\", \"sauvegarder\": \"" . urlencode($sauvegarde) . "\" } ' title='Restaurer'>OUI</a> ";
        $erreur_msg .= '<br />ATTENTION, LA BASE DE DONN&Eacute;E ACTUELLE SERA EFFACER.';
      }


    } elseif ($action == 'motdepasse') {
      // Obtiens quelques valeurs
      $nonce = ObtenirValeur('nonce', '');
      // Verifie le nonce
      $valide = true;
      if ( !$cnonce->verifyNonce($nonce) ) {
        $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
        $valide = false;
      }
      // Message
      if ($valide) {
        $erreur_msg .= 'Changement du mot de passe';
        // Mot de passe
        $motdepasse_actuelle = ObtenirValeur('motdepasse_actuelle', '');
        $nouveau_motdepasse = ObtenirValeur('nouveau_motdepasse', '');
        $confirmation_motdepasse = ObtenirValeur('confirmation_motdepasse', '');
        // Validation
        if ($motdepasse_actuelle =='' || $nouveau_motdepasse == '' || $confirmation_motdepasse == '') {
          $erreur_msg .= '<br/>Vous devez remplir tout les champs.';
        } elseif ($nouveau_motdepasse != $confirmation_motdepasse) {
          $erreur_msg .= '<br/>Le nouveau mot de passe ne correspond pas.';
        } else {
          $sql = "SELECT * FROM " . $mysql_db . ".utilisateurs WHERE utilisateur='" . addslashes( $_SESSION['utilisateur'] ) . "' ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
          }
          if (mysqli_num_rows($requete_resultat) <> 1) {
            // Trop ou aucun utilisateur trouver
            $erreur_msg .= '<br/>Mauvais nom d\'utilisateur';
          } else {
            $ligne = mysqli_fetch_assoc($requete_resultat);
            // Verifie le hash
            if (!password_verify($motdepasse_actuelle, $ligne['motdepasse'])) {
              $erreur_msg .= '<br/>Mauvais mot de passe';
            } else {
              // Mise a jour de la base de donne
              mysqli_free_result($requete_resultat);
              $sql = "UPDATE " . $mysql_db . ".utilisateurs SET motdepasse=? WHERE utilisateur='" . addslashes( $_SESSION['utilisateur'] ) . "' ";
              $stmt = mysqli_prepare($mysql_conn, $sql);
              $hash_motdepasse = password_hash($nouveau_motdepasse, PASSWORD_BCRYPT );
              mysqli_stmt_bind_param($stmt, 's', $hash_motdepasse);
              if ( mysqli_stmt_execute($stmt) ) {
                $erreur_msg .= '<br/>Mise à jour du mot de passe réussi.';
              } else {
                $erreur_msg .= '<br/>Échec de mise à jour du mot de passe.';
                $erreur_msg .= mysqli_stmt_error($stmt);
              }
            }
          }
        }
      } // if valide
    }


  // Enleve quelques valeurs du URL
  $url_actuelle = FunctionURLChange($url_actuelle, 'sauvegarde', '' );


  // Obtient la liste des sauvegarde actuelle
    $fichiers_sauvegarde = glob(dirname(__FILE__) . "/backup/backup-" . $_SESSION['db'] . "-*.sql");
    $liste_sauvegarde = array();
    foreach ($fichiers_sauvegarde as $cle => $valeur) {
      if ( !in_array( $valeur, array(".","..") ) ) {
        if (!is_dir( $valeur)) { // dirname(__FILE__) . "/backup/" .
          $sauvegarde = array();
          $sauvegarde['sauvegarde'] = urlencode(basename($valeur));
          $sauvegarde['taille'] = filesize($valeur);
          $liste_sauvegarde[ count($liste_sauvegarde) ] = $sauvegarde;
        }
      }
    }
    $smarty->assign('liste_sauvegarde', $liste_sauvegarde, true);


  // Defini le fichier template
    $template_fichier = 'init.tpl';

?>
