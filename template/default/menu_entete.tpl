<font size=4><u>Gestionnaire de finance personnel</u></font>
<br />
<br />
<a href="{$url_base}&page=comptes" data-parametreurl=' {ldelim}"page":"comptes" {rdelim} '>Liste des comptes</a> -
<a href="{$url_base}&page=type_depense" data-parametreurl=' {ldelim}"page":"type_depense" {rdelim} '>Type de d&eacute;pense</a> -
<a href="{$url_base}&page=recurrence" data-parametreurl=' {ldelim}"page":"recurrence" {rdelim} '>Liste des r&eacute;currences</a> -
<a href="{$url_base}&page=rapport" data-parametreurl=' {ldelim}"page":"rapport" {rdelim} '>Rapport</a> -
<a href="{$url_base}&page=creditcard" data-parametreurl=' {ldelim}"page":"creditcard" {rdelim} '>TEST CreditCard</a> -
<a href="{$url_base}&page=init" data-parametreurl=' {ldelim}"page":"init" {rdelim} '>Init</a> -
<a href="{$url_base}&page=logout" data-parametreurl=' {ldelim}"page":"logout" {rdelim} '>D&eacute;conexion</a>
<br />
<font size=1>
(Syst&ecirc;me :
  <a href='{$url_base}&page=type_compte' data-parametreurl=' {ldelim}"page":"type_compte" {rdelim} '>type_compte</a> -
  <a href='{$url_base}&page=type_transaction' data-parametreurl=' {ldelim}"page":"type_transaction" {rdelim} '>type_transaction</a>
)
</font>
<br />

<br />
<label id="afficher_debug"><u>Afficher/cacher Info Debug</u></label>
<div id="debug_div" style="white-space:nowrap;border: black solid 1px; display:none;">
  url_base : {$url_base}<br/>
  url_actuelle : {$url_actuelle}<br/>
  script : {$script}<br/>
  page: {$page}<br/>
  template_fichier: {$template_fichier}<br/>
  template_nom: {$template_nom}<br/>
  defilerauid: {$defilerauid}<br/>
  utilisateur: {$utilisateur}<br/>
</div>


<br /><br />


{if $todo_msg != ""}
  <label id="afficher_todo"><u>Afficher/cacher TODO liste</u></label>
  <div id="todo_liste" style="display:none;">
    <font color="orange">{$todo_msg}</font>
  </div>
  <br />
  <br />
{/if}


{if $erreur_msg != ""}
  <font class="couleur_message_erreur">{$erreur_msg}</font>
  <br />
{/if}


{if $ajout_msg != ""}
  {$ajout_msg}
  <br />
{/if}
