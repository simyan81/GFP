<?php

  // Demarre la session
    $bRet = session_start();
    if ($bRet === false) {
      die('Échec de l\'initialisation de la sessions.');
    }


  // Inclure fichier
    require_once ('smarty/libs/Smarty.class.php');
    require_once ('functions.php');
    require_once ('nonce.php');


  // Obtient la valeur 'page' du URL
    $page = ObtenirValeur ("page", "");


  // Initialise les valeurs de session
    if (!isset($_SESSION['identifier'])) {
      $_SESSION['identifier'] = 0;
      $_SESSION['db'] = '';
      $_SESSION['utilisateur'] = '';
      $_SESSION['id'] = 0;
    } elseif ($_SESSION['identifier'] != 1 ) {
      $_SESSION['db'] = '';
      $_SESSION['utilisateur'] = '';
      $_SESSION['id'] = 0;
    }


  // Defini le dossier du template
    $template_nom = 'default';
    /*
      // Force le template selon le navigateur
      if ( strstr($_SERVER['HTTP_USER_AGENT'], 'iPhone') ||
           strstr($_SERVER['HTTP_USER_AGENT'], 'iPod')    ) {
        $template_nom = 'mobile';
      } else {
        $template_nom = 'default';
      }
    */
    $smarty->setTemplateDir( './templates/' . $template_nom . '/');
    $template_fichier = '';


  // URL de base (la variable sert juste pour ajouter les autres valeurs avec un &)
    $url_actuelle = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //$url_base = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]" . $_SERVER['SCRIPT_NAME'] . '?u=' . $_SESSION['db'];
    $url_base = FunctionURLDeBase($url_actuelle);


  // Ajouter les variables de base a Smarty
    $smarty->assign('template_nom', $template_nom, true);
    //$smarty->assign('script', basename(__FILE__), true); // Retourne le nom du fichier en resouant les symlink
    $smarty->assign('script', basename( $_SERVER['SCRIPT_NAME'] ), true); // $_SERVER['SCRIPT_NAME'] = /subfolder/script.php
    $smarty->assign('page', $page, true);
    $smarty->assign('url_base', $url_base, true);
    $smarty->assign('url_actuelle', $url_actuelle, true);
    $smarty->assign('utilisateur', $_SESSION['utilisateur'], true);
  // Ajouter quelque variable par default (seront remplacer si bessoin)
    $smarty->assign('defilerauid', '', true);
    $smarty->assign('compte_id', '', true);

  // Connection a MySQL si session ouverte
    if ($_SESSION['db'] != '') {
      $mysql_conn = mysqli_connect("localhost", $mysql_user, $mysql_pass)
                or die("Impossible de se connecter : " . mysqli_error());
      $db_selected = mysqli_select_db($mysql_conn, $_SESSION['db']);
      if (!$db_selected) {
        die ('Impossible de sélectionner la base de données : ' . mysqli_error($mysql_conn));
      }
      // Obtient les listes
      ObtenirLesListes();
    }


  // Affiche la bonne page
    if ($page == 'javascript' ) {
      // Dois etre placer en premier car si apres 'login', la page JavaScript affichera la page login
      $smarty->debugging = false;
      $template_fichier = 'javascript.tpl';
    } elseif (($page == 'login' && $_SESSION['identifier'] != 1) || $_SESSION['db'] == '' ) {
      // Affiche la bonne page
        if ($page == '') {
          $page = 'login';
        }
      // Obtiens les valeurs
        $utilisateur = ObtenirValeur ('utilisateur', '');
        $motdepasse = ObtenirValeur ('motdepasse', '');
        $nonce = ObtenirValeur ('nonce', '');
      // Assigne les variable a Smarty
        $smarty->assign('utilisateur', $utilisateur, true);
        $smarty->assign('motdepasse', $motdepasse, true);
      // Verifie les valeurs saisie
        if ($utilisateur =='' && $motdepasse == '' && $nonce == '') {
          $erreur = '';
          $_SESSION['identifier'] = 0;
          $_SESSION['db'] = '';
          $_SESSION['utilisateur'] = '';
        } else if ($utilisateur =='' || $motdepasse == '' || $nonce == '') {
          $erreur = 'Veuillez entrez un nom d\'utilisateur et un mot de passe';
          $_SESSION['identifier'] = 0;
          $_SESSION['db'] = '';
          $_SESSION['utilisateur'] = '';
        } else {
          // Verifie le nonce
          if ( $cnonce->verifyNonce($nonce) ) {
            // Connection a MySQL
            if (!$mysql_conn) {
              $mysql_conn = mysqli_connect("localhost", $mysql_user, $mysql_pass)
                        or die("Impossible de se connecter : " . mysqli_error());
            }
            $db_selected = mysqli_select_db($mysql_conn, $mysql_db);
            if (!$db_selected) {
              die ('Impossible de selectionner la base de donnees : ' . mysqli_error($mysql_conn));
            }
            // Verifie si l'utilisateur existe
            $sql = "SELECT * FROM " . $mysql_db . ".utilisateurs WHERE utilisateur='" . addslashes($utilisateur) . "' ";
            $requete_resultat = mysqli_query($mysql_conn, $sql);
            if (!$requete_resultat) {
              die('Requ&ecirc;te invalide : ' . mysqli_error($mysql_conn));
            }
            if (mysqli_num_rows($requete_resultat) <> 1) {
              // Trop ou aucun utilisateur trouver
              $erreur = 'Mauvais nom d\'utilisateur ou mot de passe';
              $_SESSION['identifier'] = 0;
              $_SESSION['db'] = '';
              $_SESSION['utilisateur'] = '';
            } else {
              $ligne = mysqli_fetch_assoc($requete_resultat);
              // Verifie le hash du mot de passe
              if (!password_verify($motdepasse, $ligne['motdepasse'])) {
                $erreur = 'Mauvais nom d\'utilisateur ou mot de passe';
                $_SESSION['identifier'] = 0;
                $_SESSION['db'] = '';
                $_SESSION['utilisateur'] = '';
              } else {
                $_SESSION['identifier'] = 1;
                $_SESSION['db'] = 'pfm_' . $ligne['db_prefix'];
                $_SESSION['utilisateur'] = $ligne['utilisateur'];
                $_SESSION['id'] = $ligne['id'];
                $page = '';
                $erreur = '';
              }
            }
            // Libere les ressource
            mysqli_free_result($requete_resultat);
          } else { // nonce invalide
            $erreur = 'Délai de soumission dépassé';
          }
        }
        $smarty->assign('erreur', $erreur, true);
      // Affiche le bon template
        if ($_SESSION['identifier'] == 0) {
          $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_login', 10), true);
          $template_fichier = 'login.tpl';
        } elseif ($page == '' ) {
          $smarty->assign('page', 'logged', true);
          $template_fichier = 'logged.tpl';
        } else {
          $smarty->assign('nonce', $cnonce->generateNonce(25, 'form_login', 10), true);
          $template_fichier = 'login.tpl';
        }
    } elseif (($page == 'login' && $_SESSION['identifier'] == 1) ) {
      $smarty->assign('page', 'logged', true);
      $template_fichier = 'logged.tpl';
    } elseif ($page == 'logout' ) {
      // Detruit la sessions
        session_destroy();
      // Affiche le template
        $template_fichier = 'logout.tpl';

    } elseif ($page == 'comptes') {
      require_once ('comptes.php');
    } elseif ($page == 'type_depense') {
      require_once ('type_depense.php');
    } elseif ($page == 'depenses') {
      require_once ('depenses.php');
    } elseif ($page == 'recurrence') {
      require_once ('recurrence.php');


    } elseif ($page == 'rapport') {
      require_once ('rapport.php');
    } elseif ($page == 'creditcard') {
      require_once ('todo_creditcart.php');

    } elseif ($page == 'init') {
      require_once ('init.php');

    } elseif ($page == 'type_compte') {
      require_once ('type_compte.php');
    } elseif ($page == 'type_transaction') {
      require_once ('type_transaction.php');
    } else {
      $template_fichier = 'bienvenue.tpl';
    }


// TODO Liste
$todo_msg .= "
  <br>A voir en prioriter :

  <br> - Menage du code dans depense.php

  <br> - Compte avec interet, si on est le 1er et que toute les lignes des mois precedent sont reconcilier
         il calcule de l'interet pour le mois passer alors qu il devrais commencer a compter au mois qui a une depense ou aujourdhui
         le 1er du mois ou si dans le mois courant il n,y a pas encore eu de depense ???

  <br> - uniformise la facon que j'ajoute les lignes de le array
          $ ligne_virtuel[] = array(
            'virtuel' => true,
            'id' => 0,
            ...
            'tr_reconcilier_depense_id' => -1,
          );
          pas l'ideale car si on veux modifier les valeurs de la ligne ou la formater, cest plus comme cela
           temp = array(....);
           ligne_virtuel[] = temp;

  <br> - Renomme les fonction Javascript (fait) en francais et aussi ceux de PHP (a voir)

  <br> - Ajouter une fonctionnalite de style 'plan'.
         afin de pouvoir creer des plan de finance et voir si cest une idee viable ou pas avec le temps et les recurrence.

  <br> - creer un dossier pour github avec les fichiers dedans
         dans mon dossier de page web, creer des liens
         je vais aussi pourvoir me creer des fichier test dans le dossier web sans deranger github
         chown www-data:www-data -R gfp_github/
         ln -s /var/www/gfp_github/css/pfm.css pfm.css

  <br> - La liste des comptes dans le formulaire pour ajouter compte est pas bon,
         il ne prendre pas en compte le groupe, donc les comptes sont tous ensemble dans la meme liste
         faudrait ajuster cette liste pour cacher ou deactiver les comptes qui ne sont pas du meme groupe que le groupe qui est selectionner

  <br> - sur la page login il y a des erreur dans la console dans edge, ce sont des erreur de standardization du HTML
         le champs password doit avoir lattribut autocomplete de specifier a current-password
         et le javascript call ChoisirSymbole mais les parametre dentre de la focntion est null

  <br> - Quand on ajoute une entree (compte (ok), groupe (ok), depense, recurrence), s'assurer que tout les champs sont rempli, sans valeur null





  <hr>
    Apres avoir faite les prioriter, faire le tour pour s'assurre que les commentaire sont en francais.
    Verifier qu'il n'y a aucune information personnelle.
    Apres, je vais pouvoir posté sur GitHub
  <hr>




  <br> MOINS URGENT :
  <br>- Afficher clavier numerique sur mobile pour les zone des montants,
        fait dans depense, faudrait faire le tour des fichiers pour les changer

  <br>Dans les comptes/groupes
  <br> - ajouter 'limit credit' pour les compte 'Credit' avec option 'Seuil avertisement' qui va afficher d'une couleur si depase ce seuil
  <br> - ajouter calcul des frais et interet pour les compte type credit, epargne ou pret
  <br> - ajouter option pour les comptes : date facturation (sois une date, sois la fin du mois), le taux interet
  <br> - ajouter option 'Inverser symbole', comme pour les carte de credit
  <br> - Reconciliation automatique, ex:2ieme semaine de novembre, reconciclier tout ce qui a dans le mois d'octobre,
       2ieme semaine de l'annee, reconcilier tout ce qu'il y a l'an passee
  <br> - Faire systeme pour parser un copier/coller de Desjardins (j'avais commencer quelque chose qui fonctionnait a moitier)

  <br>Dans les depense :
  <br> - Enlever les bordures
  <br> - Pouvoir défaire une réconciliation
  <br> - Faire une requete commune de celle qui liste les depense dans ce fichier et celle de obtientEntreeDepense dans functions.php simple
        question davoir une function uniforme

  <br>Dans init :
  <br> - Ajouter email (develeppement future : envoyer une notice si un paiement arrive)
  <br> - enlever le message d'erreur dans les log : [Warning] Using a password on the command line interface can be insecure
          https://stackoverflow.com/questions/20751352/suppress-warning-messages-using-mysql-from-within-terminal-but-password-written


  <br>

  <hr>


  <br>AUTRE IDEE :
  <br> - Un calendrier flottant ?
  <br> - Afficher numero de semaine
  <br> - Mettre les date du week-end de couleur differente
  <br> - Mettre un petit espace entre chaque semaine (voir s'il faut agrendir l'espace entre chaque mois)
  <br> - mettre javascript sur la text zone du montant si tape un - ou un +, on le change dans la liste des symbole
  <br> - mettre du ajax pour afficher formulaire modifier/effacer plutot que de reloader
         pour le ajax en jquery, il y a un parametre de ajax.xhrFieds whitCredentials:true
         qui selon ce que je lis envoie les cookies au ajax (j'ai une conversation avec chatgpt fin novembre 2023 donc le tittre n'as rien a voir)

  <br>

  ";
  // Expression reguliere pour rechercher les balises HTML qui ne finisent pas par />
  $aaaa = " <(?!\/?(?:font|form|select|option|table|tr|td|a|!)\b)(?:(?!\/>)[^>])+>(?:(?!<\/?(?:font|form|select|option|table|tr|td|a|!)\b)[\s\S])*?<\/[^>]+> ";
$todo_msg .= "";
$smarty->assign('todo_msg', $todo_msg, true);


  // Assigne variable a Smarty
    $smarty->assign('url_actuelle', $url_actuelle, true);
    $smarty->assign('template_fichier', $template_fichier, true);
    $smarty->assign('erreur_msg', $erreur_msg, true);
    $smarty->assign('ajout_msg', $ajout_msg, true);
    $smarty->assign('liste_type_compte', $liste_type_compte, true);
    $smarty->assign('liste_type_transaction', $liste_type_transaction, true);
    $smarty->assign('liste_type_depense', $liste_type_depense, true);
    $smarty->assign('liste_groupe', $liste_groupe, true);
    $smarty->assign('liste_compte', $liste_compte, true);
    $smarty->assign('liste_symbole', $liste_symbole, true);
    $smarty->assign('liste_methode_calcule_interet', $liste_methode_calcule_interet, true);
    $smarty->assign('type_transaction_normale', $type_transaction_normale, true);
    $smarty->assign('type_transaction_transfert', $type_transaction_transfert, true);
    $smarty->assign('type_transaction_reconciliation', $type_transaction_reconciliation, true);
    $smarty->assign('type_transaction_ajustement', $type_transaction_ajustement, true);
    $smarty->assign('type_transaction_interet', $type_transaction_interet, true);



  // Affiche le template
    if ($template_fichier != '') {
      $smarty->display($template_fichier);
      exit;
    }
    die ('Erreur, aucune page.');

?>
