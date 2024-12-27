{literal}

// Function Javascript

/*
  il y a aussi du javascript dans :
    depense.tpl
    recurrence.tpl
*/

  // Variable principale
    var liste_type_depense = new Array();
    var liste_type_compte = new Array();
    var liste_type_transaction = new Array();
    var liste_compte = new Array();
    var liste_groupe = new Array();
    var liste_symbole = new Array();
    var liste_type_depense_symbole = new Array();
{/literal}

  // Variable de Smarty
    var utilisateur = '{$utilisateur}';
    var type_transaction_transfert = {$type_transaction_transfert};
    // Placer dans : entete.tpl
    // sera toujours 'javascript.tpl' dans ce fichier : var template_fichier = '{$template_fichier}'
    // sera toujours 'javascript dans ce fichier : var page = '{$page}';
    //   var url = '{$url_base}';
    //   var url_actuelle = '{$url_actuelle}';
    //   var defilerauid = '{$defilerauid}';
    //   var compteid = '{$compte_id}';

  // Variable des listes
    {foreach $liste_type_depense as $id => $item}
      liste_type_depense[{$item.id}] = '{$item.description|escape}';
      liste_type_depense_symbole[{$item.id}] = '{$item.symbole}';
    {/foreach}

    {foreach $liste_type_compte as $id => $item}
      liste_type_compte[{$id}] = '{$item.description|escape}';
    {/foreach}

    {foreach $liste_type_transaction as $id => $item}
      liste_type_transaction[{$id}] = '{$item.description|escape}';
    {/foreach}

    {foreach $liste_compte as $id => $item}
      liste_compte[{$id}] = '{$item.description|escape}';
    {/foreach}

    {foreach $liste_groupe as $id => $item}
      liste_groupe[{$id}] = '{$item.description|escape}';
    {/foreach}

    {foreach $liste_symbole as $id => $item}
      liste_symbole[{$id}] = '{$item}';
    {/foreach}

{literal}

$(document).ready(function(){
  // Pou debug
  $('#afficher_todo').click( function (event) {
    $("#todo_liste").toggle();
  });
  $('#afficher_debug').click( function (event) {
    $("#debug_div").toggle();
  });

  // Quand clique sur un lien
  $('a').click( function (event) {
    // Vérifier si l'attribut "parametre" existe
    var parametresString = $(this).attr('data-parametreurl');
    //var parametresStringA = $(this).data('parametreurl');
    if (!parametresString) {
      alert ('ATTENTION, le lien n\'as pas l\'attribut data-parametreurl de défini');
      //event.preventDefault();
      //return false;
    } else {
      if (parametresString.trim() === "") {
        //alert("L'attribut 'data-parametreurl' est vide pour ce lien.");
      } else {
        try {
          // Met a jour le lien
          var parametres = JSON.parse(parametresString);
          //alert (parametresString);
          //alert (parametres);
          //ChangeURLetRecharge(parametres, true);
          var le_url = url_base; //window.location.href;
          le_url = ChangeURL(parametres, le_url);
          //alert(le_url);
          window.location.href = le_url;
          event.preventDefault();
        } catch (error) {
          // S'il y a une erreur lors du parsing de la chaîne JSON, afficher une erreur
          alert("L'attribut 'data-parametreurl' n'est pas un JSON valide pour ce lien", error);
          event.preventDefault();
        }
      }
    }
  });

  // Verifie le 'nonce' quand envoye un formulaire
  $('form').submit( function (event) {
    // Vérifier si le champ "nonce" existe dans le formulaire soumis
    var nonceValue = $(this).find('input[name="nonce"]').val();

    // Vérifier si le champ "nonce" a une valeur
    if (!nonceValue) {
      alert ('ATTENTION, le formulaire n\'as pas le champs nonce de défini');
      // event.preventDefault();
    } else {
      // Ok
    }
  });

  // Clique sur un bouton nommer 'soumettre'
  $('input[type=button][name="soumettre"]').click(function() {
    // Trouver le formulaire parent
    // var formParent = $(this).closest('form'); // Ne fonctionne pas car un element form n'est pas permis dans un table
    var id_form = $(this).data('form');
    var formulaire = $('#' + id_form);
    // Besoin de mettre a jour les champs du formulaire
    // Car un element <form> ne peux etre separer dans un element <table>
    // Cela cause probleme avec le jQuery
    var verifierChampsLigne = formulaire.data('verifiechampsligne');
    if (verifierChampsLigne !== undefined) {
      formulaire.find('input, select, textarea, number').each(function() {
        // formulaire.find('input[data-allo="bonjour"], select[...], textarea..., number...').each(function() {
        var id = $(this).attr('id');
        var nom = $(this).attr('name');
        var champ_correspondant = {};
        var nom_champ_correspondant = '';
        
        if (id !== undefined) {
          nom_champ_correspondant = id + '_' + verifierChampsLigne;
        } else if (nom !== undefined) {
          nom_champ_correspondant = nom + '_' + verifierChampsLigne;
        } else {
          // Impossible, le champs n'as ni ID, ni nom
        }

        champ_correspondant = $('#' + nom_champ_correspondant);
        if (champ_correspondant.length != 1) {
          var s = '[name="' + nom_champ_correspondant + '"]';
          champ_correspondant = $(s);  
        }

        if (champ_correspondant.length > 0) {
          $(this).val( champ_correspondant.val() );
        }

      });
    }

    // Obtient le URL actuelle
    var actuel_url =  url_actuelle;

    if (formulaire.length > 0) {
      // Ajouter des parametres supplementaire au URL si besoins
      var parametreurl = $(this).data('parametreurl');
      //var aaa = "{'modifier_depense_id':102}"; // fail
      //var aaa = '{"modifier_depense_id":102}'; // ok
      //var ccc = JSON.parse(aaa);
      if (parametreurl === undefined) {
        // parametreurl non defini
        alert ('ATTENTION, le formulaire n\'as pas l\'attribut data-parametreurl de défini');
      } else {
        if (parametreurl.trim() === "") {
          //alert("L'attribut 'data-parametreurl' est vide pour ce formulaire.");
        } else {
          try {
            var parametres = JSON.parse(parametreurl);
            actuel_url = ChangeURL(parametres, actuel_url);
          } catch (error) {
            // S'il y a une erreur lors du parsing de la chaîne JSON, afficher une erreur
            alert("L'attribut 'data-parametreurl' n'est pas un JSON valide pour ce formulaire", error);
            event.preventDefault();
          }
        }
      }
      // Changer l'URL de l'action du formulaire
      formulaire.attr('action', actuel_url);
      formulaire.submit();
    } else {
      alert ('Formulaire introuvable');
    }
  });
  

  // Button dans la page depense
  $('#depense_pointer_cmd').click( function() {
    var msg = '';
    var chk_pointer = $('input[type=checkbox][name^="pointer"][data-changer="1"]');

    if (chk_pointer.length < 1) {
      alert ('Vous devez sélectionner au moins 1 lignes');
      return false;
    }

    var r = $('#depense_reconcilier_cmd').attr('disabled');
    if (r == undefined) { r = 'enabled'; }
    if (r == 'enabled') {
      msg += 'Vous avez aussi réconcilier des lignes, mais vous ne les avez pas confirmé.\n' +
              ' Si vous continuer, vous devrez les réconcilier à nouveau.\n';
    }

    if (msg != '') {
      if (confirm (msg + '\n\n' + 
                   'Voulez-vous pointé les lignes sélectionné ?') == false) {
        return false;
      }
    }

    $(window).unbind('beforeunload');

    $('#depense_pointer').submit();
  });
  $('#depense_reconcilier_cmd').click( function() {
    var msg = '';
    var chk_reconcilier = $('input[id^="chk_reconcilier_"]:checked');

    if (chk_reconcilier.length < 2) {
      alert ('Vous devez sélectionner au moins 2 lignes');
      return false;
    }

    var non_pointer = false;
    chk_reconcilier.each(function() {
      //var numero = $(this).attr('id').split('_')[2];
      var numero = $(this).val();
      var chk_pointer = $('#chk_pointer_' + numero).data('checked'); // 'checked' ou ''
      if (chk_pointer != 'checked') {
        non_pointer = true;
      }
    });
    if (non_pointer == true) {
      msg += 'Vous avez sélectionné des lignes non pointer.\n'
    }

    var p = $('#depense_pointer_cmd').attr('disabled');
    if (p == undefined) { p = 'enabled'; }
    if (p == 'enabled') {
      msg += 'Vous avez aussi pointé des lignes, mais vous ne les avez pas confirmé.\n' +
              ' Si vous continuer, vous devrez les pointé à nouveau.\n';
    }

    if (msg != '') {
      if (confirm (msg + '\n\n' + 
                   'Voulez-vous réconcilier les lignes sélectionné ?\n' +
                   'La réconciliation sera en date de la ligne la plus récente') == false) {
        return false;
      }
    }

    $(window).unbind('beforeunload');

    $('#depense_reconcilier').submit();
  });

  // Clique sur cellule dans la page depense
  $("[id^='td_pointer_']").click(function() {
    var lu = (this.id.lastIndexOf("_")) + 1;
    var id = this.id.substring(lu);
    var chk_pointer = $('#chk_pointer_' + id);

    var checked = chk_pointer.is(':checked'); // 'true' ou 'false'
    if (checked == true) {
      chk_pointer.prop( "checked", false );
    } else {
      chk_pointer.prop( "checked", true );
    }

    // chk_pointer.trigger('click'); // Cause une boucle infini
    chk_pointer.trigger('change');
  });
  $("[id^='td_reconcilier_']").click(function() {
    var lu = (this.id.lastIndexOf("_")) + 1;
    var id = this.id.substring(lu);
    var chk_reconcilier = $('#chk_reconcilier_' + id);

    var checked = chk_reconcilier.is(':checked'); // true or false
    if (checked == true) {
      chk_reconcilier.prop( "checked", false );
    } else {
      chk_reconcilier.prop( "checked", true );
    }

    // chk_reconcilier.trigger('click'); // Cause une boucle infini
    chk_reconcilier.trigger('change');
  });


  // Clique case a cocher dans la page depense
  $('input[type=checkbox][name^="pointer"]').click(function() {
    event.stopPropagation(); // Sans cela, le click sur le 'td' est declencher aussi
  });
  $('input[type=checkbox][name^="reconcilier"]').click(function() {
    event.stopPropagation(); // Sans cela, le click sur le 'td' est declencher aussi
  });


  // Valeur case a cocher change dans la page depense
  $('input[type=checkbox][name^="pointer"]').change(function() {
    checkbox_Pointer_Click(this);
  });
  $('input[type=checkbox][name^="reconcilier"]').change(function() {
    checkbox_Reconcilier_Click(this);
  });


  // Evenement sur page specifique
  if (page == 'logged') {
    var le_url = ChangeURL({"page": ""}, url_base, 2000);
  } else if (page == 'logout') {
    var le_url = ChangeURL({"page": ""}, url_base, 2000);

  } else if (page == 'comptes') {
      // Scroll au compte
      if ( $('#compte_id').length == 1 ) {
        var id = $('#compte_id').val();
        DefileVersElement( $('#ligne_compte_' + id));
      } else if ( $('#groupe_id').length == 1 ) {
        var id = $('#groupe_id').val();
        DefileVersElement( $('#ligne_groupe_' + id));
      } else if (defilerauid != "" && defilerauid != "-1") {
        // Specific row
        DefileVersElement( $('#ligne_' + defilerauid));
      } else {
        DefileVersElement( $('#liste') );
      }

      // Buttons Cancel
      $('#modifier_compte_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#modifier_compte_id').length == 1 ) {
          id = $('#modifier_compte_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, 'compte_' + id);
      });
      $('#efface_compte_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#effacer_compte_id').length == 1 ) {
          id = $('#effacer_compte_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, 'compte_' + id);
      });

      $('#modifier_groupe_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#modifier_groupe_id').length == 1 ) {
          id = $('#modifier_groupe_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, 'groupe_' + id);
      });
      $('#efface_groupe_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#effacer_groupe_id').length == 1 ) {
          id = $('#effacer_groupe_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, 'groupe_' + id);
      });

  } else if (page == 'depenses') {
      // Choisi le symbole par default
      ChoisirSymbole('ajouter_type_depense_id', 'ajouter_symbole');
  
      // Deactive le 'transfert' dans le type de compte
      DeactiveTransfertCompte('ajouter_type_transaction_id', 'transfert_compte');

      // Scroll a la depense
      if ( $('#depense_id').length == 1 ) {
        var id = $('#depense_id').val();
        DefileVersElement( $('#ligne_' + id));
      } else if (defilerauid > 0) {
        // Specific row
        DefileVersElement( $('#ligne_' + defilerauid));
      } else {
        DefileVersElement( $('#ajouterdepense_row') );
        $('#ajouter_description').focus();
      }
        
      // Buttons Cancel
      $('#modifier_depense_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#modifier_depense_id').length == 1 ) {
          id = $('#modifier_depense_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, id);
      });
      $('#efface_depense_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#effacer_depense_id').length == 1 ) {
          id = $('#effacer_depense_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, id);
      });
  } else if (page == 'recurrence') {
      // Disable some transaction type
      DeactiveTransfertCompte('ajouter_recurrence_type_transaction_id', 'ajouter_recurrence_transfert_compte');

      // Scroll at the good row
      if ( $('#recurrence_id').length == 1 ) {
        // Edit or delete row
        var id = $('#recurrence_id').val();
        DefileVersElement( $('#row_' + id));
      } else if (defilerauid > 0) {
        // Specific row
        DefileVersElement( $('#row_' + defilerauid));
      } else {
        // Add form
        DefileVersElement( $('#ajouter_recurrence_table') );
      }

      // Cancel button
      $('#modifier_recurrence_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#recurrence_id').length == 1 ) {
          id = $('#recurrence_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, id);
      });
      $('#effacer_recurrence_cmd_cancel').click( function() {
        var id = -1;
        if ( $('#recurrence_id').length == 1 ) {
          id = $('#recurrence_id').val();
        }
        RechargeEtDefileVersElement(url_actuelle, id);
      });

  }

  // Change le titre de la page
  if (page == 'javascript') {
    // Impossible ?
  } else if (page == 'login') {
    $(document).prop('title', 'GFP - Connexion');
  } else if (page == 'logout') {
    $(document).prop('title', 'GFP - Déconnexion');

  } else if (page == 'init') {
    $(document).prop('title', 'GFP - Initialisation');

  } else if (page == 'comptes') {
    $(document).prop('title', 'GFP - Liste des comptes');
  } else if (page == 'depenses') {
    $(document).prop('title', 'GFP - Dépense pour le compte ' + liste_compte[compteid]);
  } else if (page == 'recurrence') {
    $(document).prop('title', 'GFP - Liste des récurrences');

  } else if (page == 'type_depense') {
    $(document).prop('title', 'GFP - Type de dépense');
  } else if (page == 'type_compte') {
    $(document).prop('title', 'GFP - Type de compte');
  } else if (page == 'type_transaction') {
    $(document).prop('title', 'GFP - Type de transaction');

  } else if (page == 'rapport') {
    $(document).prop('title', 'GFP - Rapport');
  } else {

  }

}); // Fin de la function 'ready'


// Fonction pour mette a jour le button Pointer (lorsque la case a cocher est cliquer)
function checkbox_Pointer_Click(chk_pointer) {
  var chk = $('#' + chk_pointer.id);

  var actuelle = chk.is(':checked'); // 'true' ou 'false'
  var sauvegarder = chk.data('checked'); // 'checked' ou ''

  var changer = 0;
  if ((sauvegarder == 'checked' && actuelle == false) ||
      (sauvegarder == '' && actuelle == true)) {
    changer = 1;
  }

  chk.attr('data-changer', changer);

  MiseAJourChampPointer();
}


// Fonction pour mette a jour le button Reconcilier (lorsque la case a cocher est cliquer)
function checkbox_Reconcilier_Click(chk_reconcilier) {
  MiseAJourChampReconcilier();
}


// Fonction pour mette a jour le button Pointer
function MiseAJourChampPointer() {
  var chk = $('input[type=checkbox][name^="pointer"][data-changer="1"]');

  var sommes = 0;
  var liste_pointer_id = '';

  chk.each(function(i, obj) {
    var id =  $('#' + obj.id).val();
    sommes += parseFloat( $('#montant_' + id).data('montant') );
    if (liste_pointer_id != '') {
      liste_pointer_id += ',';
    }
    liste_pointer_id += id;
  });

  $('#liste_pointer_id').val(liste_pointer_id);
  
  $('#pointer_solde').html( sommes.toFixed(2) + ' $');

  if (liste_pointer_id != '') {
    $('#depense_pointer_cmd').attr('disabled', false);
  } else {
    $('#depense_pointer_cmd').attr('disabled', true);
  }

  PrevenirAvantActualisation();
}


// Fonction pour mette a jour le button Reconcilier
function MiseAJourChampReconcilier() {
  var chk = $('input[type=checkbox][name^="reconcilier"]:checked');

  var sommes = 0;
  var liste_reconcilier_id = '';

  chk.each(function(i, obj) {
    var id =  $('#' + obj.id).val();
    sommes += parseFloat( $('#montant_' + id).data('montant') );

    if (liste_reconcilier_id != '') {
      liste_reconcilier_id += ',';
    }
    liste_reconcilier_id += id;
  });

  $('#liste_reconcilier_id').val(liste_reconcilier_id);

  $('#reconcilier_solde').html( sommes.toFixed(2) + ' $');

  if (liste_reconcilier_id != '') {
    $('#depense_reconcilier_cmd').attr('disabled', false);
  } else {
    $('#depense_reconcilier_cmd').attr('disabled', true);
  }

  PrevenirAvantActualisation();
}


// Fonction pour prevenir l'actualisation de la page si des changement ne sont pas appliquer
function PrevenirAvantActualisation() {
    var r = $('#depense_reconcilier_cmd').attr('disabled');
    var p = $('#depense_pointer_cmd').attr('disabled');

    if (r == undefined) { r = 'enabled'; }
    if (p == undefined) { p = 'enabled'; }
    
    if (r == 'enabled' || p == 'enabled') {
      $(window).on('beforeunload', function() {
        return "Vous avez des modification non sauvegardé. Si vous continué, vous les perdrez.";
      } );
    } else {
      $(window).unbind('beforeunload');
    }
}


// Fonction pour mettre a jour les parametres de l'URL et recharge la page si besoin
function ChangeURL(parametres, le_url, recharger = false) {
  // Cree object
  const nouveau_url = new URL(le_url);

  // Met a jour chaque parametre
  for (const [parametre, valeur] of Object.entries(parametres)) {
    nouveau_url.searchParams.set(parametre, valeur);
  }
  /*
    // Recherche le parametre
    var re = new RegExp("([?&])" + parametre + "=.*?(&|$)", "i");
    var separateur = actuel_url.indexOf('?') !== -1 ? "&" : "?";
    if (actuel_url.match(re)) {
      nouveau_url = actuel_url.replace(re, '$1' + parametre + "=" + valeur + '$2');
    } else {
      nouveau_url = actuel_url + separateur + parametre + "=" + valeur;
    }
  */

  // Recharge la page
  if (recharger === false) {
    // Non
  } else if (recharger === true) {
    window.location.href = nouveau_url;
  } else if (Number.isInteger(recharger)) {
    setTimeout("window.location.href='" + nouveau_url + "';", recharger);
  } else {
    // Valeur invalide
  }

  // Retourne le nouveau URL
  return nouveau_url;
  /*
    const url = new URL(location);
    url.searchParams.set("foo", "bar");
    history.pushState({}, "", url);
  */
}


// Function pour defiler a un element
function DefileVersElement(element){
  aaa = element.offset().top - 15;
  $('html').animate({scrollTop: aaa}, 700);

  /*
    var offset = element.offset().top - $(window).scrollTop();
    alert(offset);
    alert(window.innerHeight);

    if(offset > window.innerHeight){
      alert('a');
      // Not in view so scroll to it
      $('html,body').animate({scrollTop: offset}, 1000);
      return false;
    }
  */

  return true;
}


// Function pour recharger la page et scroller a l'element
// *** aucune utilisation a pars ici
function RechargeEtDefileVersElement(url, idElement) {
  if ( idElement != "" ) {
    url =  ChangeURL({"defilerauid": idElement}, url, true);
  }
}




// Function pour deactiver le compte source dans la liste de comtpe de destination
// Utiliser directement dans les "inputs select" avec OnChange
function DeactiveTransfertCompte(src, dst) {
  var lst = document.getElementById(src);
  if (lst.value == type_transaction_transfert) {
    obj1 = document.getElementById(dst);
    obj1.style.display = 'block';
  } else {
    obj1 = document.getElementById(dst);
    obj1.style.display = 'none';
  }

  //var type_depense_id =  parseInt(lst_type_depense_id.value);
  //var s = liste_type_depense_symbole[type_depense_id];

  //'ajouter_type_transaction_id'
  //'transfert_compte'
}


// Function pour choix le signe dans la liste
// Utiliser directement dans les "inputs select" avec OnChange
function ChoisirSymbole(src, dst) {
    var lst_type_depense_id=document.getElementById(src);
    var type_depense_id =  parseInt(lst_type_depense_id.value);
    var s = liste_type_depense_symbole[type_depense_id];
    var lst_symbole=document.getElementById(dst);
    lst_symbole.value = s;
}




// Pour debug
  function dump(obj) {
    var out = '';
    for (var i in obj) {
      out += i + ": " + obj[i] + "\n";
    }
    alert(out);
    // or, if you wanted to avoid alerts...
    //var pre = document.createElement('pre');
    //pre.innerHTML = out;
    //document.body.appendChild(pre)
  }


{/literal}
