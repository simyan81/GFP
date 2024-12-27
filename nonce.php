<?php
/*
  Source :
    https://medium.com/nerd-for-tech/how-to-create-a-simple-nonce-in-php-a5afe046beee
    https://stackoverflow.com/questions/4145531/how-to-create-and-use-nonces

  Comment l'utiliser :
    Pour le formulaire
      require_once('nonce.php');
      $cnonce = new class_Nonce();
      // Cree un nonce pour les formulaires
        $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_depense', 10), true);
      <form method=get name="form_login">
        <input type="hidden" name="nonce" value="{$nonce}" />
        <input type=submit />
      </form>

    Pour la verification
      $nonce = ObtenirValeur('nonce', '');
      if ( $cnonce->verifyNonce($nonce) ) {
        // Ok
      } else {
        $erreur .= 'D&eacute;lai de soumission d&eacute;pass&eacute;<BR>';
      }

  La classe sauvegarder les 'nonce' dans un tableau dans $_SESSION['nonce']
    ID du formulaire
      ID du nonce
        md5 nonce
        expire time (timestamp)
        expire time (text)
        nonce
  Le nonce est ce que l'utilisateur a de sont cote
  nonce = salt + ID formulaire + expire time + hash
  Le hash en sha256 = NONCE_SECRET + salt + expire time

  */

// Prevent hack ?
  if (!isset($smarty)) {
    die('Bad call');
  }


// defini quelques valeurs
  define('NONCE_SECRET', 'CEIUHET745T$^&%&%^gFGBF$^');
  $cnonce = new class_Nonce();


// Classe
  class class_Nonce {
    // Contrustor
    function __construct() {
      self::removeExpired();
    }
    
    // Remove expired
    private function removeExpired() {
      if( isset($_SESSION['nonce']) && is_array($_SESSION['nonce']) ) {
        foreach ( $_SESSION['nonce'] as $form_id => $form_array) {
          if (is_array ($form_array ) ) {
            foreach ( $form_array as $k => $v) {
              if ( !is_array($v) ) {
                // invalid array
                //echo "Array nonce $form_id : $k are invalid<br>";
              } else {
                // Check time
                if ( count($v) >= 2) {
                  if ($v[1] < time()) {
                    // Expired
                    //echo "Array nonce $form_id : $k are expired<br>";
                    unset ( $_SESSION['nonce'][$form_id][$k] );
                  } else {
                    // nonce still valid
                    //echo "Array nonce $form_id : $k are valid<br>";
                  }
                } else {
                  //echo "Array nonce $form_id : $k have invalid count<br>";
                }
              }
            }
          } else {
            // invalid array
            //echo "Array nonce $form_id are invalid<br>";
          }
        }
      } else {
        // Array not initialized
        $_SESSION['nonce'] = array();
      }
    }
 
    // Generate salt
    private function generateSalt($length = 10){
      // Set up random characters
      $chars = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
      
      // Get the length of the random characters
      $char_len = strlen($chars) - 1;
      
      // Store output
      $output = '';
      
      // Iterate over $chars
      while (strlen($output) < $length) {
        /* get random characters and append to output till the length of the output
           is greater than the length provided */
        $output .= $chars[ rand(0, $char_len) ];
      }
      
      // Return the result
      return $output;
    }

    // Store Nonce
    private function storeNonce($form_id, $nonce, $time){
      // Argument must be a string
      if (is_string($form_id) == false) {
        throw new InvalidArgumentException("A valid Form ID is required");
      }
      
      // Group Generated Nonces and store with md5 Hash
      if ( !is_array($_SESSION['nonce'][$form_id]) ) {
        $_SESSION['nonce'][$form_id] = array();
      }
      
      // Make array
      $v = array();
      $v[0] = md5($nonce);
      $v[1] = $time; // Expiration time, include in the nonce value, but keep in session to remove old nonce
      
      // For debug only
      $v[2] = date('Y-m-d H:i:s', $time);
      $v[3] = $nonce;
      
      // Add to session array
      $_SESSION['nonce'][$form_id][] = $v;
      
      // Return true
      return true;
    }

    // Hash tokens and return nonce
    // $expiry_time = in minutes
    public function generateNonce($length = 10, $form_id, $expiry_time){
      // Our secret
      $secret = NONCE_SECRET;

      // Secret must be valid. You can add your regExp here
      if (is_string($secret) == false || strlen($secret) < 10) {
        throw new InvalidArgumentException("A valid Nonce Secret is required");
      }
      
      // Generate our salt
      $salt = self::generateSalt($length);
      
      // Convert the time to seconds
      $time = time() + (60 * intval($expiry_time));
      
      // Concatenate tokens to hash
      $toHash = $secret . $salt . $time;
      
      // Send this to the user with the hashed tokens
      $nonce = $salt . ':' . $form_id . ':' . $time . ':' . hash('sha256', $toHash);
      
      // Store Nonce
      self::storeNonce($form_id, $nonce, $time);
      
      // Return nonce
      return $nonce;
    }

    // Verify nonce
    public function verifyNonce($nonce){
      // Our secret
      $secret = NONCE_SECRET;
      
      // Split the nonce using our delimeter : and check if the count equals 4
      $split = explode(':', $nonce);
      if(count($split) < 2){
        //echo "invalid count<br>";
        return false;
      }

      // Reassign variables
      $salt = $split[0];
      $form_id = $split[1];
      $time = intval($split[2]);
      $oldHash = $split[3];
      
      // Check if the time has expired
      // Also possible to check with the session value, but time are in the hash, if user change it, the hash will be invalid
      if(time() > $time){
        //echo "time expired<br>";
        return false;
      }

      /* Nonce is proving to be valid, continue ... */

      // Check if nonce is present in the session array
      if(isset($_SESSION['nonce'][$form_id])){
        if (is_array ($_SESSION['nonce'][$form_id]) ) {
          $findMatch = false;
          foreach ( $_SESSION['nonce'][$form_id] as $k => $v) {
            if ( !is_array($v) ) {
              // Ignore it
              //echo "v not array<br>";
              //return false;
            } else {
              //check if hashed value matches
              // $v = $_SESSION['nonce'][$form_id][$k][0]
              // time = $_SESSION['nonce'][$form_id][$k][1]
              if($v[0] == md5($nonce)){
                $findMatch = $k;
                break;
              } else {
                //echo "hash mismatch (" . md5($nonce) . ")<br>";
                //return false;
              }
            }
          } // for each
          if ($findMatch === false) {
            //echo "hash not found (" . md5($nonce) . ")<br>";
            return false;
          } else {
            // Remove it from session array
            unset( $_SESSION['nonce'][$form_id][$findMatch]  );
          }
        } else {
          //echo "not array<br>";
          return false;
        }
      } else {
        //echo "unknown<br>";
        return false;
      }

      // Check if the nonce is valid by rehashing and matching it with the $oldHash
      $toHash = $secret . $salt . $time;
      $reHashed = hash('sha256', $toHash);
      
      // Match with the token
      if($reHashed !== $oldHash){
        //echo "old token<br>";
        return false;
      }
      
      /* Wonderful, Nonce has proven to be valid*/
      //echo "token valid<br>";
      return true;
    }

  } // end of class

?>