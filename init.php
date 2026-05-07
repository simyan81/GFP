<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


$ajout_msg .= "";


  // Obtient quelques valeurs
    $action = ObtenirValeur('action', '', 'token');


  // Cree un nonce pour les formulaires
    $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_init', 10), true);

  function ObtenirNomBddSecurise($db_nom) {
    if (!is_string($db_nom) || !preg_match('/^[a-zA-Z0-9_]+$/', $db_nom)) {
      return '';
    }
    return $db_nom;
  }

  function ObtenirCheminSauvegarde($nomFichier) {
    $nom = basename($nomFichier);
    return dirname(__FILE__) . '/backup/' . $nom;
  }


  // Verifie les action
    if ($action == 'init') {
      // Obtiens quelques valeurs
      $nonce = ObtenirValeur('nonce', '', 'token');
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
        $db_nom = ObtenirNomBddSecurise($_SESSION['db']);
        if ($db_nom === '') {
          $erreur_msg .= 'Nom de base de donn&eacute;es invalide.<br>';
          $valide = false;
        }
        if ($valide && $confirm == 'true') {
          $cmd = 'mysql --host=localhost --user=' . escapeshellarg($mysql_user)
               . ' --password=' . escapeshellarg($mysql_pass)
               . ' --database=' . escapeshellarg($db_nom)
               . ' < ' . escapeshellarg(dirname(__FILE__) . '/init.sql');
          $r = exec($cmd);
          if ($r === false) {
            $erreur_msg .= "Initialisation &eacute;chou&eacute;e<br />";
          } else {
            $erreur_msg .= "Initialisation r&eacute;ussie<br />";
          }
        } else {
          //$erreur_msg .='<a href="' . $url .'&page=init&action=init&confirm=true">OUI</a>';
          $erreur_msg .= " <a href='" . $url_base . "' data-parametreurl=' {\"action\": \"init\", \"confirm\": \"true\" } ' title='Initialisation'>OUI</a> ";
          $erreur_msg .= '<br />ATTENTION, LA BASE DE DONN&Eacute;E ACTUELLE SERA EFFACER.';
        }
      }


    } elseif ($action == 'sauvegarde') {
      // Obtiens quelques valeurs
      $nonce = ObtenirValeur('nonce', '', 'token');
      // Verifie le nonce
      $valide = true;
      if ( !$cnonce->verifyNonce($nonce) ) {
        $erreur_msg .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
        $valide = false;
      }
      // Creer une sauvegarde
      if ($valide) {
        $erreur_msg = "Sauvegarde de la base de donn&eacute;<br />";
        $db_nom = ObtenirNomBddSecurise($_SESSION['db']);
        if ($db_nom === '') {
          $erreur_msg .= 'Nom de base de donn&eacute;es invalide.<br>';
          $valide = false;
        }
        if ($valide) {
          $sauvegarde = dirname(__FILE__) . "/backup/backup-" .  $db_nom . "-" . date("Y-m-d") . ".sql";
          // --no-create-info
          $cmd = 'mysqldump --skip-add-drop-table --no-tablespaces --user=' . escapeshellarg($mysql_user)
               . ' --password=' . escapeshellarg($mysql_pass)
               . ' --host=localhost ' . escapeshellarg($db_nom);
          $cmd .= " | sed -r 's/CREATE TABLE (`[^`]+`)/TRUNCATE TABLE \\1; CREATE TABLE IF NOT EXISTS \\1/g' ";
          $cmd .= ' > ' . escapeshellarg($sauvegarde);
          $r = exec($cmd);
          if ($r === false) {
            $erreur_msg .= "Sauvegarde &eacute;chou&eacute;e<br />";
          } else {
            $erreur_msg .= "Sauvegarde r&eacute;ussie<br />";
          }
        }
      }


    } elseif ($action == 'effacer') {
      // Efface une sauvegarde
      $erreur_msg = "Effacement d'une sauvegarde<br />";
      $sauvegarde = urldecode(ObtenirValeur('sauvegarde', ''));
      $confirm = ObtenirValeur('confirm', '');
      $backup_file = ObtenirCheminSauvegarde($sauvegarde);
      if ($confirm == 'true') {
        if ( is_file($backup_file) ) {
          if ( unlink($backup_file) ) {
            $erreur_msg .= 'Sauvegarde effacée';
          } else {
            $erreur_msg .= 'Échec de l\'effacement';
          }
        } else {
          $erreur_msg .= 'Sauvegarde introuvable';
        }
      } else {
        $erreur_msg .= 'Effacer ' . basename($sauvegarde) . ' : ';
        //$erreur_msg .= '<a href="' . $url .'&page=init&action=effacer&sauvegarde=' . urlencode($sauvegarde) . '&confirm=true">OUI</a>';
        $erreur_msg .= " <a href='" . $url_base . "' data-parametreurl=' {\"action\": \"effacer\", \"confirm\": \"true\", \"sauvegarder\": \"" . urlencode($sauvegarde) . "\" } ' title='Effacer'>OUI</a> ";
      }


    } elseif ($action == 'restaurer') {
      // Restaure une sauvegarde
      $erreur_msg = "Restauration d'une sauvegarde<br />";
      $sauvegarde = urldecode(ObtenirValeur('sauvegarde', ''));
      $backup_file = ObtenirCheminSauvegarde($sauvegarde);
      $confirm = ObtenirValeur('confirm', '');
      if ($confirm == 'true') {
        if ( is_file($backup_file) ) {
          $db_nom = ObtenirNomBddSecurise($_SESSION['db']);
          if ($db_nom === '') {
            $erreur_msg .= 'Nom de base de donn&eacute;es invalide.<br>';
          } else {
            $cmd = 'mysql --user=' . escapeshellarg($mysql_user)
                 . ' --password=' . escapeshellarg($mysql_pass)
                 . ' --host=localhost --database=' . escapeshellarg($db_nom)
                 . ' < ' . escapeshellarg($backup_file);
            $r = exec($cmd);
            if ($r === false) {
              $erreur_msg .= 'Échec de la restauration de la sauvegarde';
            } else {
              $erreur_msg .= 'Sauvegarde restaurée';
            }
          }
        } else {
          $erreur_msg .= 'Sauvegarde introuvable';
        }
      } else {
        $erreur_msg .= 'Restauré ' . basename($sauvegarde) . ' : ';
        //$erreur_msg .= '<a href="' . $url .'&page=init&action=restaurer&sauvegarde=' . urlencode($sauvegarde) . '&confirm=true">OUI</a>';
        $erreur_msg .= " <a href='" . $url_base . "' data-parametreurl=' {\"action\": \"restaurer\", \"confirm\": \"true\", \"sauvegarder\": \"" . urlencode($sauvegarde) . "\" } ' title='Restaurer'>OUI</a> ";
        $erreur_msg .= '<br />ATTENTION, LA BASE DE DONN&Eacute;E ACTUELLE SERA EFFACER.';
      }


    } elseif ($action == 'motdepasse') {
      // Obtiens quelques valeurs
      $nonce = ObtenirValeur('nonce', '', 'token');
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
