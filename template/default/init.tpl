{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


<br />
<br />


Changer le mot de passe :<br />
<form id="motdepasse" name="motdepasse" action="{$url_base}" method="post">
    <input type="hidden" name="nonce" value="{$nonce}" />
    <input type="hidden" name="action" value="motdepasse" />
    Mot de passe actuelle : <input type="password" name="motdepasse_actuelle" autocomplete="new-password" value="" /><br />
    Nouveau mot de passe : <input type="password" name="nouveau_motdepasse" autocomplete="new-password" value="" /><br />
    Confirmation du mot de passe : <input type="password" name="confirmation_motdepasse" autocomplete="new-password" value="" /><br />
    <input type="button" name="soumettre" value="Changer mot de passe" data-form="motdepasse" data-parametreurl="" />
</form>


<br />
<br />


<hr/>


<br />
Initialisation de la base de donn&eacute;e<br />
<form id="initialisation_bd" name="initialisation_bd" action="{$url_base}" method="post">
    <input type="hidden" name="nonce" value="{$nonce}" />
    <input type="hidden" name="action" value="init" />
    <input type="button" name="soumettre" value="Initialisation BD" data-form="initialisation_bd" data-parametreurl="" />
</form>


<br />
Sauvegarde de la base de donn&eacute;e<br />
<form id="sauvegarde_bd" name="sauvegarde_bd" action="{$url_base}" method="post">
    <input type="hidden" name="nonce" value="{$nonce}" />
    <input type="hidden" name="action" value="sauvegarde" />
    <input type="button" name="soumettre" value="Sauvegarde BD" data-form="sauvegarde_bd" data-parametreurl="" />
</form>


<br />
Restaurer / Effacer DB<br />
{foreach $liste_sauvegarde as $id => $sauvegarde}
  - {$sauvegarde.sauvegarde} ({$sauvegarde.taille} octects) :
  <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "restaurer", "sauvegarde": "{$sauvegarde.sauvegarde|escape}" {rdelim} ' title="Restaurer">Restaurer</a> 
  <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "effacer", "sauvegarde": "{$sauvegarde.sauvegarde|escape}" {rdelim} ' title="Effacer">Effacer</a> 
  <br />
{/foreach}


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
