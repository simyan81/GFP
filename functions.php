<?php
  // Initialisation de Smarty
    $smarty = new Smarty;
    // $smarty->debugging = true;
    $smarty->caching = false;
    //$smarty->caching = true;
    $smarty->cache_lifetime = 120;
    $smarty->force_compile = true;
    //$smarty->clearAllCache();

 // MySQL
    $mysql_conn = null;

  // Message
    $erreur_msg = '';
    $ajout_msg = '';
    $todo_msg = 'TEST';

  // Initialise quelques variables
    $liste_type_compte = array();
    $liste_type_transaction = array();
    $liste_type_depense = array();
    $liste_groupe = array();
    $liste_compte = array();
    $liste_symbole = array();

  // Initialise les listes systeme
    $liste_methode_calcule_interet = array(
        0 => 'Aucun interet',
        1 => 'Intérêt composé à la fin du mois',
    );


  // Type de transaction (ID provenant de la DB)
    $type_transaction_normale = 1;
    $type_transaction_transfert = 2;
    $type_transaction_reconciliation = 3;
    $type_transaction_ajustement = 4;
    $type_transaction_interet = 5;

  // Variable de date
    $maintenant_unix = time();
    $maintenant =  date('Y-m-d H:i:s', $maintenant_unix);
    $aujourdhui = date('Y-m-d', $maintenant_unix);
    $date_trop_ancienne = date('Y-m-d', strtotime(date("Y-m-d", $maintenant_unix) . " -14 day"));
    $date_trop_avenir = date('Y-m-d', strtotime(date("Y-m-d", $maintenant_unix) . " +14 day"));

    // ancien nom : showtodate
    $afficherjusquau = date('Y-m-d', strtotime(date("Y-m-d", $maintenant_unix) . " +" . (7 * 6) . " day"));
    //$afficherjusquau = date('Y-m-d', strtotime(date("Y-m-d", $maintenant_unix) . " +" . (7 * 54) . " day"));


// Inclure la configuration
  require_once 'config.php';


// Inclure les fonctions de validation
  require_once 'functions_validation.php';







// function pour changer valeur dans le url pour developement future ?
  function FunctionURLChange($url, $parametre, $valeur) {
    // Parse l'URL existante pour obtenir les paramètres actuels
    $parse = parse_url($url);
    parse_str($parse['query'], $params);

    // Modifie les paramètres
    if ($valeur == '') {
      unset( $params[$parametre] );
    } else {
      $params[$parametre] = $valeur;
    }

    // Reconstruit la chaîne de requête
    $newQuery = http_build_query($params);

    // Construit la nouvelle URL en utilisant les composants d'origine et la nouvelle chaîne de requête
    $newUrl = '';

    $newUrl = (isset ($parse['scheme']) && $parse['scheme'] != '' ? $parse['scheme'] . '://' : '') .
              (isset ($parse['host']) && $parse['host'] != '' ? $parse['host'] : '') .
              $parse['path'] . '?' . $newQuery;

    return $newUrl;
  }


// function pour garder que les valeurs de base dans le URL
  function FunctionURLDeBase($url) {
    // Parse l'URL existante pour obtenir les paramètres actuels
    $parse = parse_url($url);
    parse_str($parse['query'], $params);

    // Modifie les paramètres
    $new_params = array();
    foreach ($params as $k => $v) {
      if (strtolower($k) == 'u') {
        // Ok
        $new_params[$k] = $v;
        $params[$k] = $_SESSION['db'];
      } elseif (strtolower($k) == 'page') {
        // Ok
        $new_params[$k] = $v;
      } elseif (strtolower($k) == 'compte_id') {
        // Ok si page = depenses
        $new_params[$k] = $v;
      } else {
        unset ( $params[$k] );
      }
    }

    // Ajoute certaine valeur si manquante
    if ( !isset($params['u']) ) {
      $params['u'] = $_SESSION['db'];
    }

    // Reconstruit la chaîne de requête
    $newQuery = http_build_query($params);

    // Construit la nouvelle URL en utilisant les composants d'origine et la nouvelle chaîne de requête
    $newUrl = '';

    $newUrl = (isset ($parse['scheme']) && $parse['scheme'] != '' ? $parse['scheme'] . '://' : '') .
              (isset ($parse['host']) && $parse['host'] != '' ? $parse['host'] : '') .
              $parse['path'] . '?' . $newQuery;

    return $newUrl;
  }



















/******************************************************************************************************
**** ObtenirValeur : Obtien les valeurs du POST ou du GET                                          ****
******************************************************************************************************/
  function ObtenirValeur($nomvaleur, $valeurpardefault) {
    unset ($v);
    if (isset($_POST[$nomvaleur])) {
      $v = $_POST[$nomvaleur];
    } else {
      if (isset($_GET[$nomvaleur])) {
        $v = $_GET[$nomvaleur];
      } else {
        // Rien
      }
    }
    if (!isset ($v)) {
      return $valeurpardefault;
    }
    if (!is_numeric($v) &&
        !is_array($v) ) {
      $v = addslashes($v);
    }
    return $v;
  }


/******************************************************************************************************
**** DateSeulement : Retourne que la date sans l'heure                                             ****
******************************************************************************************************/
  function DateSeulement($d) {
    $bRet = verifierFormatDate($d);
    if ($bRet === false) {
      return false;
    }
    $s = explode(' ', $d);
    return $s[0];
  }


/******************************************************************************************************
**** AbrvJourSemaine : Retourne l'abreviation du jour de la semaine                                ****
******************************************************************************************************/
  function AbrvJourSemaine($d) {
    $bRet = verifierFormatDate($d);
    if ($bRet === false) {
      return '-';
    }
    $wd = date('w', strtotime($d)) ;
    switch ($wd) {
      case 1: return 'Lun.';
      case 2: return 'Mar.';
      case 3: return 'Mer.';
      case 4: return 'Jeu.';
      case 5: return 'Ven.';
      case 6: return 'Sam.';
      case 0: return 'Dim.';
    }
    return '-';
  }


/******************************************************************************************************
**** formatMonnaie : Retourne le montant formater                                                  ****
******************************************************************************************************/
  function formatMonnaie($montant, $symbole) {
    // Format nombre
      $s = number_format ( $montant, 2, '.' , ' ');
    // Symbole definie ?
      if ($symbole < 0 || $symbole > 1) {
        if ($montant < 0) {
          $symbole = 0;
        } else {
          $symbole = 1;
        }
      }
    // Negatif ?
      if ($symbole == 1) {
        // +
        $s = "$s $";
      } else {
        // -
        $s = "-($s) $";
      }
    // Retourne le montant formater
      return $s;
  }




/******************************************************************************************************
**** myslashes : ??????????????                                                                    ****
******************************************************************************************************/
function myslashes($s) {
  //return htmlspecialchars(stripslashes( $s ));
  // htmlspecialchars cause trouble at some place
  // in template file, can use ${variablename|escape}
  return stripslashes( $s );
}


/******************************************************************************************************
**** Fonction pour ajouter une depense dans la DB                                                  ****
******************************************************************************************************/
  function ajouterDepense(&$errmsg, &$ligne_virtuel, $compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $transfert_compte_id, $recurrence_id) {
    // Variable global
      global $mysql_conn;
      global $type_transaction_transfert;
      global $type_transaction_ajustement;
    // Valeur de retour par default
      $rRet = false;
    // Est un transfert
      if ($type_transaction_id == $type_transaction_transfert) {
        $ligne_virtuel_transfert = ($ligne_virtuel === false ? false : array());
        $rRet1 = false;
        $rRet2 = false;
        $refid1 = 0;
        $refid2 = 0;
        $rRet1 = ajouterEntreeDepense($errmsg, $ligne_virtuel, $refid1, $compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $recurrence_id);
        $rRet2 = ajouterEntreeDepense($errmsg, $ligne_virtuel, $refid2, $transfert_compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, ($symbole == 1 ? 0 : 1), $recurrence_id);
        if ($ligne_virtuel === false) {
          if ($rRet1 > 0 && $rRet2 > 0) {
            // Met a jour les reference
            $sql = "UPDATE depenses SET transfert_compte_id = $refid2 WHERE id = $refid1";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            // mysqli_free_result($requete_resultat); // Passe besoin pour un UPDATE
            $sql = "UPDATE depenses SET transfert_compte_id = $refid1 WHERE id = $refid2";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            // mysqli_free_result($requete_resultat); // Passe besoin pour un UPDATE
            $rRet = true;
          } else {
            $rRet = false;
          }
        } else {
          $ligne_virtuel[ 0 ]['tr_compte_id'] = $transfert_compte_id;
          $ligne_virtuel[ 1 ]['tr_compte_id'] = $compte_id;
          $ligne_virtuel[ 0 ]['transfert_compte_id'] = 0;
          $ligne_virtuel[ 1 ]['transfert_compte_id'] = 0;
          $rRet = true;
        }
    // Est un ajustement
      } elseif ($type_transaction_id == $type_transaction_ajustement) {
        // Obtient le solde
          $sql = "";
          $sql .= "SELECT c.*, ";
          $sql .= "       IFNULL( (SELECT SUM(montant)";
          $sql .= "          FROM depenses ";
          $sql .= "         WHERE compte_id = c.id";
          $sql .= "           AND est_effacer = 0 ";
          $sql .= "           AND date_depense < '" . $date_depense . "' ";
          $sql .= "           AND reconcilier_depense_id = 0";
          $sql .= "           AND montant >= 0), 0) AS entre,";
          $sql .= "       IFNULL( (SELECT SUM(montant)";
          $sql .= "          FROM depenses ";
          $sql .= "         WHERE compte_id = c.id";
          $sql .= "           AND est_effacer = 0 ";
          $sql .= "           AND date_depense < '" . $date_depense . "' ";
          $sql .= "           AND reconcilier_depense_id = 0";
          $sql .= "           AND montant < 0), 0) AS sortie ";
          $sql .= "  FROM comptes AS c ";
          $sql .= " WHERE c.id = " . $compte_id;
          $sql .= " ORDER BY c.ordre ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            $erreur_msg .= 'Requete invalid. solde compte ajustement.';
            return false;
          }
          if (mysqli_num_rows($requete_resultat) <> 1) {
            $erreur_msg .= 'Requete invalid. solde compte ajustement #1.';
            return false;
          }
          $ligne = mysqli_fetch_assoc($requete_resultat);
          $ligne['solde'] = $ligne['entre'] + $ligne['sortie'];
          mysqli_free_result($requete_resultat);
        // Verifie la difference
          $newsolde = ($symbole = 1 ? $montant : -($montant)) - $ligne['solde'];
          $newsolde = round( $newsolde, 2);
        // Ajoute entree
          $montant = ABS($newsolde);
          $symbole = ($newsolde < 0 ? 0 : 1);
          $refid = 0;
          $rRet = ajouterEntreeDepense($errmsg, $ligne_virtuel, $refid, $compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $recurrence_id);
    // Est une depense reguliere
      } else {
        $refid = 0;
        $rRet = ajouterEntreeDepense($errmsg, $ligne_virtuel, $refid, $compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $recurrence_id);
      }
    // Retourne
      return $rRet;
  }
/******************************************************************************************************
**** Fonction pour ajouter une entree de depense dans la DB (fonctionner utiliser par ajouterDepense ) ****
******************************************************************************************************/
function ajouterEntreeDepense(&$errmsg, &$ligne_virtuel, &$refid, $compte_id, $type_transaction_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $recurrence_id) {
    // Variable global
      global $maintenant;
      global $mysql_conn;
    // Initialise variable
      $errmsg = '';
      $ajouter = true;
    // Valide les donnees saisie
      $date_depense = trim($date_depense);
      if ($date_depense == '') {
        //$date_depense = $maintenant;
        $errmsg .= 'Date invalide.';
        $ajouter = false;
      } elseif (verifierFormatDate($date_depense) === false) {
        //$date_depense = $maintenant;
        $errmsg .= 'Format de date invalide (' . $date_depense . ').';
        $ajouter = false;
      }
      if (!is_numeric($type_transaction_id)) {
        $errmsg .= 'Type de transaction invalide.';
        $ajouter = false;
      }
      if (!is_numeric($type_depense_id)) {
        $errmsg .= 'Type de depense invalide (' . $type_depense_id . ').';
        $ajouter = false;
      }
      if (!is_numeric($montant)) {
        $errmsg .= 'Montant invalide (' . $montant . ').';
        $ajouter = false;
      }
      if (!is_numeric($symbole)) {
        $errmsg .= 'Type symbole invalide.';
        $ajouter = false;
      }
      if (!is_numeric($recurrence_id)) {
        $errmsg .= 'recurrence_id invalide.';
        $ajouter = false;
      }
      if ($ajouter == false) {
        return false;
      }
    // Insert la ligne ou garde en virtuel
      if ($ligne_virtuel === false) {
        // Requete SQL
          $sql = "INSERT INTO depenses (est_effacer, date_ajouter,compte_id, type_transaction_id, date_depense, type_depense_id, description, notes, montant, recurrence_id) VALUES (";
          $sql .= "0, ";
          $sql .= "'$maintenant', ";
          $sql .= "$compte_id, ";
          $sql .= "$type_transaction_id, ";
          $sql .= "'$date_depense', ";
          $sql .= "$type_depense_id, ";
          $sql .= "'" . addslashes($description) . "', ";
          $sql .= "'" . addslashes($notes) . "', ";
          $sql .= ($symbole == 0 ? -$montant : $montant) . ", ";
          $sql .= "$recurrence_id ";
          $sql .= ")";
        // Insere dans la DB
          $requete_resultat = mysqli_query($mysql_conn, $sql);
          if (!$requete_resultat) {
            die('Requ&ecirc;te invalide (ajouterEntreeDepense) : ' . mysqli_error($mysql_conn));
          }
        // Libere la ressource
          //mysqli_free_result($requete_resultat); // Pas besoin pour un INSERT
        // Retourne le ID
          $refid = mysqli_insert_id($mysql_conn);
      } else {
        // Ajoute la ligne dans le tableau virtuel (les champs correspond a la table 'depenses')
        $ligne_virtuel[] = array(
          'virtuel' => true,
          'id' => 0,
          'pointer' => 0,
          'est_effacer' => 0,
          'date_ajouter' => $maintenant,
          'compte_id' => $compte_id,
          // 'compte_description' => Definie plus tard
          'type_transaction_id' => $type_transaction_id,
          // 'type_transaction_description' => Definie plus tard
          'date_depense' => $date_depense,
          'type_depense_id' => $type_depense_id,
          // 'type_depense_description' => Definie plus tard
          'description' => addslashes($description),
          'notes' => addslashes($notes),
          'montant' => ($symbole == 0 ? -$montant : $montant),
          'recurrence_id' => $recurrence_id,
          'reconcilier_depense_id' => 0,
          'transfert_compte_id' => -1,
          'tr_compte_id' => -1,
          'tr_id' => -1,
          'tr_compte_description' => '',
          'tr_reconcilier_depense_id' => -1,
        );
      }
    // Retourne
      return true;
  }


/******************************************************************************************************
**** Mise a jour d'une depense                                                                     ****
******************************************************************************************************/
  function modifierDepense(&$errmsg, $depense_id, $recurrence_id, $date_depense, $type_depense_id, $description, $notes, $montant, $symbole, $transfert_compte_id) {
    // Variable global
      global $mysql_conn;
      global $type_transaction_transfert;
      global $type_transaction_reconciliation;
    // Initialise quelques variables
      $errmsg ='';
      $sqlDepense = '';
      $sqlReference = '';
    // Verifie que la depense existe
      if (!obtientEntreeDepense($errmsg, $ligne, $depense_id)) {
        return false;
      }
    // Requete SQL
      $sqlDepense  = 'UPDATE depenses ';
      $sqlDepense .= "   SET description = '" . addslashes($description) . "', ";
      $sqlDepense .= "       notes = '" . addslashes($notes) . "', ";
      // $sqlDepense .= '        recurrence_id = -(ABS(recurrence_id)), ';
      if (is_numeric($type_depense_id)) {
        $sqlDepense .= '       type_depense_id = ' . $type_depense_id . ', ';
      }
      if ($ligne['peuxModifier'] == true) {
        $sqlDepense .= "       date_depense = '" . $date_depense . "', ";
      }
    // Copie la requete de base
      $sqlReference = $sqlDepense;
    // Ajouter le montant selon le type de depense
      if (is_numeric($montant) &&
          is_numeric($symbole) && ($symbole > -1 && $symbole < 2) &&
          $ligne['peuxModifier'] == true) {
        $sqlDepense   .= '       montant = ' . ($symbole == 0 ? -$montant : $montant) . ', ';
        $sqlReference .= '       montant = ' . ($symbole == 0 ? $montant : -$montant) . ', '; // Montant inverse dans l'autre compte
        //$sqlDepense .= '       symbole = ' . $symbole . ', ';
        //$sqlReference .= '       symbole = ' . ($symbole == 1 ? 0 : 1) . ', ';
      }
      $sqlDepense .= '       est_effacer = 0 WHERE ';
      $sqlReference .= '       est_effacer = 0 WHERE ';
    // Ajouter les conditions
      // Si c'est un transfert
      if ($ligne['transfert_compte_id'] > 0 ||
          $ligne['type_transaction_id'] == $type_transaction_transfert) {
        $sqlDepense .=   " id = $depense_id ";
        $sqlReference .= " id = " . $ligne['transfert_compte_id'];
      // Si c'est une reconciliation
      } elseif ($ligne['reconcilier_depense_id'] > 0 ||
                $ligne['type_transaction_id'] == $type_transaction_reconciliation) {
        $sqlDepense .=   " id = $depense_id ";
        $sqlReference = '';
      // Si c'est une transaction normale
      } else {
        $sqlDepense .=   "id = $depense_id ";
        $sqlReference = '';
      }

    // Execute les requetes
      if ($sqlDepense != '') {
        $requete_resultat = mysqli_query($mysql_conn, $sqlDepense);
        if (!$requete_resultat) {
          die('Requ&ecirc;te invalide (modifierDepense:1) : ' . mysqli_error($mysql_conn));
        }
      }
      if ($sqlReference != '') {
        $requete_resultat = mysqli_query($mysql_conn, $sqlReference);
        if (!$requete_resultat) {
          die('Requ&ecirc;te invalide (modifierDepense:2) : ' . mysqli_error($mysql_conn));
        }
      }
    // Retourne
      return true;
  }


/******************************************************************************************************
**** Format quelques elements de la depense pour l'affichage                                       ****
******************************************************************************************************/
  function formatEntreeDepense(&$ligne) {
    // $ligne provient de la requete SQL

    // Variable global
      global $liste_type_transaction;
      global $type_transaction_reconciliation;
      global $aujourdhui;
      global $date_trop_ancienne;
      global $date_trop_avenir;
//echo "<pre>" . var_export($ligne, true) . "</pre>";

// Defini quelque valeur si non defini
//if ( !isset($ligne['tr_compte_description'])) {
//  $ligne['tr_compte_description'] = 'AAAAA';
//}

    // Verifie que les champs de base sont present
      if ( !isset($ligne['tr_compte_id'])) { die("Le champ 'tr_compte_id' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['compte_description'])) { die("Le champ 'compte_description' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['type_depense_description'])) { die("Le champ 'type_depense_description' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['type_transaction_description'])) { die("Le champ 'type_transaction_description' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['tr_compte_description'])) { die("Le champ 'tr_compte_description' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['description'])) { die("Le champ 'description' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['notes'])) { die("Le champ 'notes' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['montant'])) { die("Le champ 'montant' n'est pas présente dans la requete SQL."); }
      //if ( !isset($ligne['symbole'])) { die("Le champ 'symbole' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['date_depense'])) { die("Le champ 'date_depense' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['reconcilier_depense_id'])) { die("Le champ 'reconcilier_depense_id' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['type_transaction_id'])) { die("Le champ 'type_transaction_id' n'est pas présente dans la requete SQL."); }
      if ( !isset($ligne['tr_reconcilier_depense_id'])) { die("Le champ 'tr_reconcilier_depense_id' n'est pas présente dans la requete SQL."); }

    // Enleve les caractere d'echapement
      $ligne['compte_description'] = myslashes($ligne['compte_description']);
      $ligne['type_depense_description'] = myslashes($ligne['type_depense_description']);
      $ligne['type_transaction_description'] = myslashes($ligne['type_transaction_description']);
      // $ligne['type_transaction_description'] = $liste_type_transaction[ $ligne['type_transaction_id'] ] ['description'];
      $ligne['tr_compte_description'] = myslashes($ligne['tr_compte_description']);
      $ligne['description'] = myslashes( $ligne['description'] );
      $ligne['notes'] = myslashes( $ligne['notes'] );
    // Format montant
      $ligne['symbole'] = ($ligne['montant'] < 0 ? 0 : 1);
      $ligne['montant'] = abs($ligne['montant']);
      $ligne['montantformat'] =  formatMonnaie($ligne['montant'], $ligne['symbole']);
    // Solde
      if (!isset( $ligne['solde'] ) ) {
        $ligne['solde'] = formatMonnaie(0, 1);
      }
      $ligne['solde_symbole'] = ($ligne['solde'] < 0 ? 0 : 1);
      $ligne['soldeformat'] = formatMonnaie(abs($ligne['solde']), $ligne['solde_symbole']);
    // Format date
      $ligne['date_annee'] = date("Y", strtotime( $ligne['date_depense'] ));
      $ligne['date_mois'] = date("m", strtotime( $ligne['date_depense'] ));
      $ligne['date_jour'] = date("d", strtotime( $ligne['date_depense'] ));
      $ligne['date_seule'] = DateSeulement($ligne['date_depense']);
      $ligne['dateformat'] = AbrvJourSemaine($ligne['date_depense']) . ' ' . DateSeulement($ligne['date_depense']);

    // Initialise quelques valeurs par default
      if (!isset( $ligne['afficherSeparateurAvant'] ) ) {
        $ligne['afficherSeparateurAvant'] = false;
      }
      if (!isset( $ligne['estAujourdhui'] ) ) {
        $ligne['estAujourdhui'] = false;
      }
      if (!isset( $ligne['estJourneeVide'] ) ) {
        $ligne['estJourneeVide'] = false;
      }
      if ( !isset($ligne['virtuel']) ) {
        $ligne['virtuel'] = false;
      }
      if (!is_numeric($ligne['tr_compte_id'])) {
        $ligne['tr_compte_id'] = -1;
      }

   // Si peux modifier la depense
      $ligne['peuxModifier'] = false;
      if ($ligne['reconcilier_depense_id'] > 0 ||   /* La depense a ete reconcilier */
          $ligne['tr_reconcilier_depense_id'] > 0 || /* Si la depense du transfert a ete reconcilier */
          $ligne['type_transaction_id'] == $type_transaction_reconciliation || /* La depense est une reconciliation */
          $ligne['virtuel'] == true /* La depense set virtuel */
          ) {
        $ligne['peuxModifier'] = false;
      } else {
        $ligne['peuxModifier'] = true;
      }

    // Si peux effacer la depense
      $ligne['peuxEffacer'] = false;
      if ($ligne['reconcilier_depense_id'] > 0 || /* La depense a ete reconcilier */
          $ligne['type_transaction_id'] == $type_transaction_reconciliation || /* La depense est une reconciliation */
          $ligne['tr_reconcilier_depense_id'] > 0 || /* Si la depense du transfert a ete reconcilier */
          $ligne['virtuel'] == true /* La depense set virtuel */
         ) {
        $ligne['peuxEffacer'] = false;
      } else {
        $ligne['peuxEffacer'] = true;
      }

    // Couleur de la ligne
      $classe_couleur = '';
      if ($ligne['virtuel'] == true) {
        $classe_couleur = 'couleur_ligne_virtuel';
      } else {
        $d = DateSeulement($ligne['date_depense']);
        $classe_couleur = obtenirCouleurLigne($d);
      /*} elseif ($d == $aujourdhui) {
        $classe_couleur = 'couleur_date_aujourdhui';
      } elseif ($d < $date_trop_ancienne) {
        $classe_couleur = 'couleur_date_trop_ancienne';
      } elseif ($d < $aujourdhui ) {
        $classe_couleur = 'couleur_date_ancienne';
      } elseif ($d > $date_trop_avenir) {
        $classe_couleur = 'couleur_date_trop_avenir';
      } elseif ($d <= $date_trop_avenir) {
        $classe_couleur = 'couleur_date_avenir';
      } else {
        $classe_couleur = 'couleur_date_inconnue';
        */
      }
      $ligne['couleur'] = $classe_couleur;

    // Retoune
      return true;
  }

/******************************************************************************************************
**** obtenirCouleurLigne : Obtien la couleur de la ligne selon la date de la depense               ****
******************************************************************************************************/
  function obtenirCouleurLigne($date_depense, $forcer_virtuel = false) {
    global $aujourdhui;
    global $date_trop_ancienne;
    global $date_trop_avenir;
    global $date_trop_avenir;
    
    if ($date_depense == $aujourdhui) {
      return 'couleur_date_aujourdhui';
    } elseif ($date_depense < $date_trop_ancienne) {
      return 'couleur_date_trop_ancienne';
    } elseif ($date_depense < $aujourdhui ) {
      return 'couleur_date_ancienne';
  } elseif ($date_depense > $date_trop_avenir) {
      return 'couleur_date_trop_avenir';

    } elseif ($date_depense <= $date_trop_avenir) {
      return 'couleur_date_avenir';
    //} elseif ($forcer_virtuel == true) {
    //  return 'couleur_ligne_virtuel';

//    } elseif ($date_depense > $date_trop_avenir) {
//      return 'couleur_date_trop_avenir';

    } else {
      return 'couleur_date_inconnue';
    }
  }


/******************************************************************************************************
**** obtientEntreeDepense : Recupere une entre de depense dans la base de donnee                   ****
******************************************************************************************************/
  function obtientEntreeDepense(&$err_msg, &$r, $depense_id) {
    // Variable global
      global $mysql_conn;
      global $type_transaction_reconciliation;

    // Variable de retour
      $r = array();

    // Verifie si la depense existe
      if (!depense_id_est_valide($depense_id) ) {
        return false;
      }

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

      $sql .= " WHERE  depense.id = " . $depense_id . " ";

      $sql .= " ORDER BY depense.date_depense, CASE WHEN depense.montant >= 0 THEN 1 ELSE 0 END, depense.id ";

    // Execute requete SQL
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (obtientEntreeDepense) : ' . mysqli_error($mysql_conn) . "<br>SQL Query : $sql");
      }

    // Verifie le nombre de resultat
      if (mysqli_num_rows($requete_resultat) <> 1) {
        die('ID de Depense invalid (' . mysqli_num_rows($requete_resultat) . ')(' . $depense_id . ')');
      }

    // Recupere le premier resultat
      $r = mysqli_fetch_assoc($requete_resultat);

    // Format la depense
      if (!formatEntreeDepense($r)) {
        $r  = array();
        return false;
      }

    // Retourne
      return true;
  }


/******************************************************************************************************
**** DateDerniereEntreeRecurrence : Retourne la date de la derniere entree créer de la recurrencce ****
******************************************************************************************************/
function DateDerniereEntreeRecurrence($recurrence_id) {
  // Variable global
    global $mysql_conn;

  // Requete SQL
    $sql  = "SELECT * ";
    $sql .= "  FROM depenses ";
    $sql .= " WHERE recurrence_id = " . $recurrence_id . " ";
    $sql .= "   AND est_effacer = 0 ";
    $sql .= " ORDER BY date_depense DESC ";
    $sql .= " LIMIT 1";
  // Nouvelle requete
    $sql  = " SELECT date_derniere_depense_ajouter AS date_depense ";
    $sql .= "   FROM recurrence ";
    $sql .= " WHERE id = " . $recurrence_id . " ";

  // Execute requete SQL
    $requete_resultat = mysqli_query($mysql_conn, $sql);

  // Verifie erreur
    if (!$requete_resultat) {
      die('Requ&ecirc;te invalide (DateDerniereEntreeRecurrence) : ' . mysqli_error($mysql_conn));
    }

  // Retourne date
    $dateDerniereEntree = null;
    if (mysqli_num_rows($requete_resultat) <> 1) {
      // Aucune ligne ou trop de ligne
    } else {
      $ligne_recurrence = mysqli_fetch_assoc($requete_resultat);
      $dateDerniereEntree = DateSeulement( $ligne_recurrence['date_depense'] );
    }

  // Libre la ressource
    mysqli_free_result($requete_resultat);

  // Retourne
    return $dateDerniereEntree;
}


/******************************************************************************************************
**** recurrenceDepenseEntree : Genere les entree de depense d'une recurrence                       ****
****   $ligne_virtuel = false ou array                                                             ****
****   $compte_id est ignorer si $ligne_virtuel = false                                            ****
****   $afficherjusquau est ignorer si $ligne_virtuel = false                                      ****
******************************************************************************************************/
  function recurrenceDepenseEntree($recurrence_id, $compte_id, $afficherjusquau, &$ligne_virtuel, &$ajout_msg, &$erreur_msg) {
    // Variable global
    global $mysql_conn;
    global $maintenant_unix;


    // Obtient les informations de la recurrence
    // Requete SQL
      $sql = "SELECT * FROM recurrence where id = " . $recurrence_id;
    // Execute la requete SQL
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (recurrenceDepenseEntree) : ' . mysqli_error($mysql_conn));
      }
    // Verifie le compte
      if (mysqli_num_rows($requete_resultat) <> 1) {
        $erreur_msg .= "Impossible de trouv&eacute; l'entr&eacute;e $recurrence_id<br>";
        return false;
      }
    // Obtient les valeurs
      $recurrence = mysqli_fetch_assoc($requete_resultat);

    // Log
      $ajout_msg .= "<b>Entr&eacute;e pour " . $recurrence['description'] . "</b><br/>";


    // Obtient la date de la derniere entree dans les depenses
      $last_recurrence_row = array();
      $ignorerPremier = true;
      $dateDerniereEntree = DateDerniereEntreeRecurrence($recurrence_id);
      if ($dateDerniereEntree == null) {
        $ajout_msg .= "&nbsp;Aucune entr&eacute;e existante<br/>";
        $ignorerPremier = false; // Aucune ligne n'as ete genere actuellement
        $last_recurrence_row['date_depense'] = $recurrence['date_debut'];
        // Si interval est a la fin du mois, s'assurer de commencer avec la fin du mois
        if ($recurrence['type_interval'] == 1) {
          $premierJourDuMoisSuivant = date("Y-m-01",  $maintenant_unix);
          $last_recurrence_row['date_depense'] = date("Y-m-t",  $maintenant_unix);
        }
      } else {
        $ajout_msg .= "&nbsp;Date de la derni&egrave;re entr&eacute;e le " . $dateDerniereEntree . "<br/>";
        $ignorerPremier = true; // La derniere ligne correspond a la premiere ligne genere ci-apres
        $last_recurrence_row['date_depense'] = $dateDerniereEntree;
      }

      // Initialise la date de debut
      $recurrence_date_debut_timestamp = strtotime($last_recurrence_row['date_depense']);

      // Initialise la date de fin
      if ($recurrence['date_fin'] != '') {
        $recurrence_date_fin_timestamp = strtotime($recurrence['date_fin']);
      } else {
        $recurrence_date_fin_timestamp = '';
      }

      // Defini la date maximale
      if ($recurrence['auto_interval_valeur'] < 1) {
        $recurrence['auto_interval_valeur'] = 1;
      }
      if ($recurrence['auto_type_interval'] == 0) { // Moi
        $max_time = strtotime(date("Y-m-d") . " +" . $recurrence['auto_interval_valeur'] . " month");
      } elseif ($recurrence['auto_type_interval'] == 2) { // Semaine
        $max_time = strtotime(date("Y-m-d") . " +" . $recurrence['auto_interval_valeur'] . " week");
      } elseif ($recurrence['auto_type_interval'] == 3) { // Jour
        $max_time = strtotime(date("Y-m-d") . " +" . $recurrence['auto_interval_valeur'] . " day");
      } else { // Par default, 1 mois d'avance
        $max_time = strtotime(date("Y-m-d") . " +1 month");
      }

      // Si affichage virtuel
      if ($ligne_virtuel === false) {
       // Garde max_time
      } else {
        $temp = strtotime($afficherjusquau);
        if ($temp > $max_time) {
          $max_time = $temp;
        }
      }

      // Log
      if (false == true) {
        $ajout_msg .= "Entry ID : " . $recurrence_id . "<br>";
        $ajout_msg .= "Last entry was : " . $last_recurrence_row['date_depense'] . "<br>";
        $ajout_msg .= "date_fin : " . $recurrence['date_fin'] . "<br>";
        $ajout_msg .= "date_fin_timestamp : " . $recurrence_date_fin_timestamp . "<br>";
        $ajout_msg .= "auto_type_interval : " . $recurrence['auto_type_interval'] . "<br>";
        $ajout_msg .= "auto_interval_valeur : " . $recurrence['auto_interval_valeur'] . "<br>";
        $ajout_msg .= "type_interval : " . $recurrence['type_interval'] . "<br>";
        $ajout_msg .= "interval_valeur : " . $recurrence['interval_valeur'] . "<br>";
        $ajout_msg .= "now : " . date("Y-m-d") .  "<br>";
        $ajout_msg .= "max : " . date("Y-m-d", $max_time) .  "<br>";
      }

      // Genere les depenses
      // Pour prevenir les boucle infinie
        $loop_infinie = 400;
      // compteur
        $compteur_entree_ajouter = 0;
      // Initialise la date de depart de la boucle
        $date_actuelle_timestamp = $recurrence_date_debut_timestamp;
      // Initialise variable
        $mettre_a_jour_date_derniere_depense_ajouter = false;
      // Boucle
        do {
          // Initialise la date
            $date_actuelle = date("Y-m-d", $date_actuelle_timestamp);
          // Ajoute la depense
            if ($ignorerPremier == true) {
              // Ignore la premier entre car c'est la meme que la derniere entree existante dans la DB
              $ignorerPremier = false;
            } else {
              // Log
              $ajout_msg .= "&nbsp;Entr&eacute;e ajouter en date du : $date_actuelle <BR>";
              // Ajout la depense
              $msg = '';
              $ligne_virtuel_temp = false;
              if ($ligne_virtuel !== false) {
                $ligne_virtuel_temp = array();
              }
              $bRet = ajouterDepense($msg,
                                        $ligne_virtuel_temp,
                                        $recurrence['compte_id'],
                                        $recurrence['type_transaction_id'],
                                        $date_actuelle,
                                        $recurrence['type_depense_id'],
                                        $recurrence['description'],
                                        $recurrence['notes'],
                                        abs($recurrence['montant']),
                                        ( $recurrence['montant'] < 0 ? 0 : 1),
                                        $recurrence['transfert_compte_id'],
                                        $recurrence['id'] );
              if ($ligne_virtuel !== false) {
                if ( count($ligne_virtuel_temp) > 1 ) {
                  if ($compte_id == $recurrence['compte_id']) {
                    $ligne_virtuel[] = $ligne_virtuel_temp[0];
                  } else {
                    $ligne_virtuel[] = $ligne_virtuel_temp[1];
                  }
                } else {
                  $ligne_virtuel[] = $ligne_virtuel_temp[0];
                }
              } else {
                $mettre_a_jour_date_derniere_depense_ajouter = true;
              }
              // Verifie le resultat de l'ajout
              if ($bRet == false) {
                $ajout_msg .= "Impossible d'ajouter l'entr&eacute;e. Processus arreter. " . $msg . '<BR>';
                break;
              } else {
                // Augment le compteur d'ajout
                $compteur_entree_ajouter += 1;
              }
            }
          // Verifie la prochaine date de depense
            if ($recurrence['type_interval'] == 0) { // Mensuel
              $date_actuelle_timestamp = strtotime(date("Y-m-d",$date_actuelle_timestamp) . " +" . $recurrence['interval_valeur'] . " month");
            } elseif ($recurrence['type_interval'] == 1) { // A la fin du mois
              $premierJourDuMoisSuivant = strtotime(date("Y-m-d",$date_actuelle_timestamp) . " +1 day");
              $premierJourDuMoisSuivant = strtotime(date("Y-m-d",$premierJourDuMoisSuivant) . " +" . ($recurrence['interval_valeur'] - 1) . " month");
              $date_actuelle_timestamp = strtotime(date("Y-m-t", $premierJourDuMoisSuivant));
            } elseif ($recurrence['type_interval'] == 2) { // Hebdomadaire
              $date_actuelle_timestamp = strtotime(date("Y-m-d",$date_actuelle_timestamp) . " +" . $recurrence['interval_valeur'] . " week");
            } elseif ($recurrence['type_interval'] == 3) { // Quotidien
              $date_actuelle_timestamp = strtotime(date("Y-m-d",$date_actuelle_timestamp) . " +" . $recurrence['interval_valeur'] . " day");
            } elseif ($recurrence['type_interval'] == 4) { // Anuelle
              $date_actuelle_timestamp = strtotime(date("Y-m-d",$date_actuelle_timestamp) . " +" . $recurrence['interval_valeur'] . " year");
            } else {
              $date_actuelle_timestamp = 0;
              break;
            }
          // Verifie la date maximale
            // Si a atteint la date de fin
              if ($recurrence_date_fin_timestamp != '' &&  $date_actuelle_timestamp > $recurrence_date_fin_timestamp) {
                $ajout_msg .= '&nbsp;Date de fin ateinte.<br>';
                break;
              }
            // Si a atteint la date limite d'ajout
              if ($date_actuelle_timestamp > $max_time) {
                $ajout_msg .= "&nbsp;Date limite d'ajout d'avance ateinte.<br>";
                break;
              }
            // Si a atteint la limite de la boucle
              $loop_infinie--;
              if ($loop_infinie < 1) {
                $ajout_msg .= "&nbsp;Nombre d'entr&eacute;e maximum atteint.<br>";
                break;
              }
        } while ($loop_infinie > 0);
      // Met a jour le champ date_derniere_depense_ajouter
        if ($mettre_a_jour_date_derniere_depense_ajouter == true) {
          $sql = " UPDATE recurrence
                     JOIN ( SELECT recurrence_id,
                                   MAX(date_depense) AS date_depense
                              FROM depenses
                             WHERE recurrence_id = " . $recurrence['id'] . "
                               AND est_effacer = 0
                             GROUP BY recurrence_id
                          ) AS d ON d.recurrence_id = id
                      SET date_derniere_depense_ajouter = d.date_depense
                    WHERE id = " . $recurrence['id'] . " ";
          $requete_resultat = mysqli_query($mysql_conn, $sql);
        }
      // Log
        if ($compteur_entree_ajouter < 1) {
          $ajout_msg .= "&nbsp;Aucune entr&eacute;e &agrave; ajouter<br>";
        } else {
          $ajout_msg .= "&nbsp;" . $compteur_entree_ajouter . " entr&eacute;e ajouter<br>";
        }
  }

/****************************************************************************************************
**** Functions pour les listes                                                                   ****
****************************************************************************************************/
  function ObtenirLesListes() {
    // Type Depense
      ObtenirListeTypeDepense();
    // Type Compte
      ObtenirListeTypeCompte();
    // Type Transaction
      ObtenirListeTypeTransaction();
    // Comptes
      ObtenirListeComptes();
    // Groupes
      ObtenirListeGroupes();
    // Symbole
      ObtenirListeSymbole();
  }

  function ObtenirListeSymbole() {
    global $liste_symbole;
    $liste_symbole = array();
    $liste_symbole[ 0 ] = '-';
    $liste_symbole[ 1 ] = '+';
  }

  function ObtenirListeGroupes($updateOrder = false) {
      global $liste_groupe;
      global $mysql_conn;
      global $erreur_msg;
      global $ajout_msg;

      if ($updateOrder == true) {
            $sql  = ' SET @rank=0; ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            $sql  = ' UPDATE groupes SET ordre=@rank:=(@rank+2) ';
            $sql .= ' ORDER BY ordre ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre des groupes.<BR>";
            } else {
              $ajout_msg .= 'Ordre des groupes mise &agrave; jour.<BR>';
            }
      }


      $liste_groupe = array();
      $sql  = 'SELECT g.* ';
      $sql .= '  FROM groupes AS g';
      $sql .= ' WHERE IfNull(g.est_effacer, 0) = 0 ';
      $sql .= ' ORDER BY g.ordre ';
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (ObtenirListeGroupes) : ' . mysqli_error($mysql_conn));
      }
      $estPremier = true;
      $dernierID = 0;
      $numeroLigne = 0;
      while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
        $numeroLigne += 1;
        $ligne['numeroLigne'] = $numeroLigne;
        $ligne['estPremier'] = $estPremier;
        $ligne['estDernier'] = false;
        $ligne['description'] = myslashes( $ligne['description'] );
        $liste_groupe[ $ligne['id'] ] = $ligne;
        $estPremier = false;
        $dernierID = $ligne['id'];
      }
      $liste_groupe[ $dernierID ]['estDernier'] = true;
      mysqli_free_result($requete_resultat);
  }

  function ObtenirListeComptes($updateOrder = false) {
      global $liste_compte;
      global $mysql_conn;
      global $erreur_msg;
      global $ajout_msg;


      if ($updateOrder == true) {
            $sql  = ' SET @rank=0; ';
            $requete_resultat = mysqli_query($mysql_conn,  $sql);
            // Get group of the selected account
            //$sql  = ' SET @gid = (SELECT IsNull(groupe_id, 0) FROM comptes WHERE id = ' . $compte_id . ');';
            //$requete_resultat = mysqli_query($mysql_conn, $sql);
            $sql  = ' UPDATE comptes SET ordre=@rank:=(@rank+2) ';
            //$sql .= ' WHERE groupe_id = @gid ';
            $sql .= ' ORDER BY ordre ';
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              $erreur_msg .= "Erreur lors de la mise &agrave; jour de l\'ordre des comptes.<BR>";
            } else {
              $ajout_msg .= 'Ordre des comptes mise &agrave; jour.<BR>';
            }
      }


      $liste_compte = array();
      $sql  = 'SELECT c.* ';
      $sql .= '  FROM comptes AS c';
      //$sql .= ' WHERE IfNull(c.est_effacer, 0) = 0 '; // Besoin d'afficher tout les comptes si il y a des depense qui y font reference
      $sql .= ' ORDER BY c.ordre ';
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (ObtenirListeComptes) : ' . mysqli_error($mysql_conn));
      }
      $estPremier = true;
      $dernierID = 0;
      $numeroLigne = 0;
      while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
        $numeroLigne += 1;
        $ligne['numeroLigne'] = $numeroLigne;
        $ligne['estPremier'] = $estPremier;
        $ligne['estDernier'] = false;
        $ligne['description'] = myslashes( $ligne['description'] );
        $ligne['est_effacer'] = ( $ligne['est_effacer'] == 1 ? true : false);
        $liste_compte[ $ligne['id'] ] = $ligne;
        $estPremier = false;
        $dernierID = $ligne['id'];
      }
      $liste_compte[ $dernierID ]['estDernier'] = true;
      mysqli_free_result($requete_resultat);
  }

  function ObtenirListeTypeTransaction() {
      global $liste_type_transaction;
      global $mysql_conn;

      $liste_type_transaction = array();
      $sql  = 'SELECT tt.* ';
      $sql .= '  FROM type_transaction AS tt';
      $sql .= ' ORDER BY tt.ordre ';
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (ObtenirListeTypeTransaction) : ' . mysqli_error($mysql_conn));
      }
      $estPremier = true;
      $dernierID = 0;
      $numeroLigne = 0;
      while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
        $numeroLigne += 1;
        $ligne['numeroLigne'] = $numeroLigne;
        $ligne['estPremier'] = $estPremier;
        $ligne['estDernier'] = false;
        $ligne['description'] = myslashes( $ligne['description'] );
        $liste_type_transaction[ $ligne['id'] ] = $ligne;
        $estPremier = false;
        $dernierID = $ligne['id'];
      }
      $liste_type_transaction[ $dernierID ]['estDernier'] = true;
      mysqli_free_result($requete_resultat);
  }

  function ObtenirListeTypeCompte() {
      global $liste_type_compte;
      global $mysql_conn;

      $liste_type_compte = array();
      $sql  = 'SELECT tc.* ';
      $sql .= '  FROM type_compte AS tc';
      $sql .= ' ORDER BY tc.ordre ';
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (ObtenirListeTypeCompte) : ' . mysqli_error($mysql_conn));
      }
      $estPremier = true;
      $dernierID = 0;
      $numeroLigne = 0;
      while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
        $numeroLigne += 1;
        $ligne['numeroLigne'] = $numeroLigne;
        $ligne['estPremier'] = $estPremier;
        $ligne['estDernier'] = false;
        $ligne['description'] = myslashes( $ligne['description'] );
        $liste_type_compte[ $ligne['id'] ] = $ligne;
        $estPremier = false;
        $dernierID = $ligne['id'];
      }
      $liste_type_compte[ $dernierID ]['estDernier'] = true;
      mysqli_free_result($requete_resultat);
  }

  function ObtenirListeTypeDepense() {
      global $liste_type_depense;
      global $mysql_conn;

      $liste_type_depense = array();
      $sql  = 'SELECT *, (SELECT COUNT(id) ';
      $sql .= '             FROM depenses ';
      $sql .= '            WHERE type_depense_id = d.id) AS useCount';
      $sql .= '  FROM type_depense AS d';
      $sql .= ' ORDER BY ordre, description';
      $requete_resultat = mysqli_query($mysql_conn, $sql);
      if (!$requete_resultat) {
        die('Requ&ecirc;te invalide (ObtenirListeTypeDepense) : ' . mysqli_error($mysql_conn));
      }
      $estPremier = true;
      $dernierID = 0;
      $numeroLigne = 0;
      while ($ligne = mysqli_fetch_assoc($requete_resultat)) {
        $numeroLigne += 1;
        $ligne['numeroLigne'] = $numeroLigne;
        $ligne['estPremier'] = $estPremier;
        $ligne['estDernier'] = false;
        $ligne['description'] = myslashes( $ligne['description'] );
        $liste_type_depense[ $ligne['id'] ] = $ligne;
        $estPremier = false;
        $dernierID = $ligne['id'];
      }
      $liste_type_depense[ $dernierID ]['estDernier'] = true;
      mysqli_free_result($requete_resultat);
  }

?>
