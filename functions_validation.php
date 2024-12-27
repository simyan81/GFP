<?php
  // Prevent hack ?
    if (!isset($smarty)) {
      die('Bad call');
    }


/****************************************************************************************************
**** verifierFormatDate : Retourne 'true' si la valeur entré est une date valide                 ****
****************************************************************************************************/
  function verifierFormatDate($date) {
    // Format de date : YYYY-MM-DD- HH:MM:SS
    if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/", $date, $parts)) {
      if(checkdate($parts[2],$parts[3],$parts[1])) {
        return true;
      } else {
        return false;
      }
    // Format de date : YYYY-MM-DD
    } elseif (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
      if(checkdate($parts[2],$parts[3],$parts[1])) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }


/****************************************************************************************************
**** Function de validation : Retour 'true' si valide                                            ****
****************************************************************************************************/
  function compte_id_est_valide($compte_id) {
    return true;
  }
  function type_transaction_id_est_valide($type_transaction_id) {
    return true;
  }
  function type_depense_id_est_valide($type_depense_id) {
    return true;
  }
  function symbole_est_valide($symbole) {
    if (!is_numeric($symbole) ) {
      return false;
    } elseif ($symbole < 0 || $symbole > 1) {
      return false;
    }
    return true;
  }
  function montant_est_valide($montant) {
    if (!is_numeric($montant) ) {
      return false;
    }
    return true;
  }
  function depense_id_est_valide($depense_id) {
    if (!is_numeric($depense_id) ) {
      return false;
    }
    return true;
  }
?>
