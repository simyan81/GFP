{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


<table border="1" id="liste">
  <tr class="couleur_table_titre">
    <td colspan="5" align="center"><b>Liste des comptes</b></td>
  </tr>
  <tr class="couleur_table_colonne_titre">
    <td width="200" colspan="2">Description</td>
    <td width="100" align="right">Solde</td>
    <td width="45" align="center">Ordre</td>
    <td width="35" align="center">Act.</td>
  </tr>

  {foreach from=$groupes item=groupe}
    <tr id="ligne_groupe_{$groupe.id}" class="couleur_table_colonne_titre">
<!-- Groupe Description -->
      <td colspan="2">
        {if $groupe.id == $modifier_groupe_id}
          <input type="text" id="modifier_description_{$groupe.id}" name="modifier_description_{$groupe.id}" title="Description" placeholder="Description" value="{$groupe.description}" />
        {else}
          {$groupe.description}
        {/if}
      </td>
<!-- Groupe Solde -->
      <td align="right">{$groupe.grandtotalformat}</td>
<!-- Groupe Ordre -->
      <td align="center">{if $groupe.estPremier == false}
          <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "groupe_deplacer", "groupe_id": {$groupe.id}, "ordre": {$groupe.ordre - 3} {rdelim} ' title="Vers le haut"><img src="images/moveup.gif" title="Vers le haut" /></a>
        {else}
          <img width="16" height="16" src="images/noicon.gif" title="" />
        {/if}
        {if $groupe.estDernier == false}
          <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "groupe_deplacer", "groupe_id": {$groupe.id}, "ordre": {$groupe.ordre + 3} {rdelim} ' title="Vers le bas"><img src="images/movedown.gif" title="Vers le bas" /></a>
        {else}
          <img width="16" height="16" src="images/noicon.gif" title="" />
        {/if}
      </td>
<!-- Groupe Action -->
        <td align="center">
          {if $modifier_groupe_id == $groupe.id || $effacer_groupe_id == $groupe.id}
            &nbsp;
            <!-- Aucune action en mode modifier ou effacer -->
          {else}
            <a href='{$url_base}' data-parametreurl=' {ldelim}"modifier_groupe_id": {$groupe.id}, "action": "groupe_modifier" {rdelim} ' title="Modifier"><img src="images/edit.png" title="Modifier"></a>

            {if $groupe.peuxEffacer == True}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"effacer_groupe_id": {$groupe.id}, "action": "groupe_effacer" {rdelim} ' title="Effacer"><img src="images/drop.png" title="Effacer"></a>
            {else}
              <img width="16" height="16" src="images/noicon.gif" title="A" />
            {/if}
          {/if}
        </td>
      </tr>
    </tr>
<!-- Effacer confirmation -->
    {if $groupe.id == $effacer_groupe_id}
      <tr>
        <td colspan="4"></td>
        <td colspan="5">
          <form id="effacer_groupe" name="effacer_groupe" action="{$url_base}" method="post">
            <input type="hidden" name="nonce" value="{$nonce}" />
            <input type="hidden" name="action" value="groupe_effacer_confirmer" />
            <input type="hidden" id="effacer_groupe_id" value="{$groupe.id}" />
            <input type="button" name="soumettre" data-form="effacer_groupe" data-parametreurl=' {ldelim}"effacer_groupe_id":{$groupe.id}{rdelim} ' value="Effacer" />
            <input type="button" id="efface_groupe_cmd_cancel" value="Annuler" /><br />
          </form>
        </td>
      </tr>
    {/if}
<!-- Modification confirmation -->
    {if $groupe.id == $modifier_groupe_id}
      <tr>
        <td colspan="4"></td>
        <td colspan="5">
          <form id="modifier_groupe" name="modifier_groupe" action="{$url_base}" method="post" data-verifiechampsligne='{$groupe.id}'>
            <input type="hidden" name="nonce" value="{$nonce}" />
            <input type="hidden" name="action" value="groupe_modifier_confirmer" />
            <input type="hidden" id="modifier_groupe_id" value="{$groupe.id}" />
            <input type="button" name="soumettre" data-form="modifier_groupe" data-parametreurl=' {ldelim}"modifier_groupe_id":{$groupe.id}{rdelim} ' value="Modifier" />
            <input type="button" id="modifier_groupe_cmd_cancel" value="Annuler" /> <br />
            <div style="display:none;">
              <input type="text" id="modifier_description" name="modifier_description" value="{$groupe.description}" />
            </div>
          </form>
        </td>
      </tr>
    {/if}







    {if {$groupe.comptes|count} == 0}
      <tr>
        <td colspan=5>
          Il n'y a aucun compte dans ce groupe
        </td>
      </tr>
    {else}
      {foreach from=$groupe.comptes item=compte}
        {if $compte.numeroLigne is odd by 1}
          <tr id="ligne_compte_{$compte.id}" class="couleur_table_ligne_impair">
        {else}
          <tr id="ligne_compte_{$compte.id}" class="couleur_table_ligne_pair">
        {/if}
<!-- Voir compte -->
          <td align="center">
            {if $modifier_compte_id == $compte.id || $effacer_compte_id == $compte.id}
              &nbsp;
              <!-- Aucune action en mode modifier ou effacer -->
            {else}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"page":"depenses", "compte_id": {$compte.id} {rdelim} ' title="Voir"><img src="images/eye.jpg" title="Voir"/></a>
            {/if}
          </td>
<!-- Description -->
          <td>
            {if $compte.id == $modifier_compte_id}
              <input type="text" id="modifier_description_{$compte.id}" name="modifier_description_{$compte.id}" title="Description" placeholder="Description" value="{$compte.description}" />
            {else}
              {$compte.description}
            {/if}
            <!-- {if $compte.nombreavant > 0} (*{$compte.nombreavant}) {/if} -->
          </td>
<!-- Solde -->
          <td align="right">{$compte.solde}</td>
<!-- Ordre -->
          <td align="center">{if $compte.estPremier == false}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "compte_deplacer", "compte_id": {$compte.id}, "ordre": {$compte.ordre - 3} {rdelim} ' title="Vers le haut"><img src="images/moveup.gif" title="Vers le haut" /></a>
            {else}
              <img width="16" height="16" src="images/noicon.gif" title="" />
            {/if}
            {if $compte.estDernier == false}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "compte_deplacer", "compte_id": {$compte.id}, "ordre": {$compte.ordre + 3} {rdelim} ' title="Vers le bas"><img src="images/movedown.gif" title="Vers le bas" /></a>
            {else}
              <img width="16" height="16" src="images/noicon.gif" title="" />
            {/if}
          </td>
<!-- Action : Compte -->
          <td align="center">
            {if $modifier_compte_id == $compte.id || $effacer_compte_id == $compte.id}
              &nbsp;
              <!-- Aucune action en mode modifier ou effacer -->
            {else}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"modifier_compte_id": {$compte.id}, "action": "compte_modifier" {rdelim} ' title="Modifier"><img src="images/edit.png" title="Modifier"/></a>
              {if $compte.peuxEffacer == True}
                <a href='{$url_base}' data-parametreurl=' {ldelim}"effacer_compte_id": {$compte.id}, "action": "compte_effacer" {rdelim} ' title="Effacer"><img src="images/drop.png" title="Effacer"/></a>
              {else}
                <img width="16" height="16" src="images/noicon.gif" title="A" />
              {/if}
            {/if}
          </td>
        </tr>
<!-- Effacer confirmation -->
        {if $compte.id == $effacer_compte_id}
          <tr>
            <td colspan="4"></td>
            <td colspan="5">
              <form id="effacer_compte" name="effacer_compte" action="{$url_base}" method="post">
                <input type="hidden" name="nonce" value="{$nonce}" />
                <input type="hidden" name="action" value="compte_effacer_confirmer" />
                <input type="hidden" id="effacer_compte_id" value="{$compte.id}" />
                <input type="button" name="soumettre" data-form="effacer_compte" data-parametreurl=' {ldelim}"effacer_compte_id":{$compte.id}{rdelim} ' value="Effacer" />
                <input type="button" id="efface_compte_cmd_cancel" value="Annuler" /><br />
              </form>
            </td>
          </tr>
        {/if}
<!-- Modification confirmation -->
        {if $compte.id == $modifier_compte_id}
          <tr>
            <td colspan="4">
              Méthode de calcule de l'intérêt :
                <select id="modifier_methode_calcule_interet_{$compte.id}" title="Méthode de calcule de l'intérêt" placeholder="Méthode de calcule de l'intérêt">
                  {foreach $liste_methode_calcule_interet as $id => $ligne}
                    <option value="{$id}" {if $compte.methode_calcule_interet == $id} selected {/if} >{$ligne}</option>
                  {/foreach}
                </select><br />
              Taux d'intérêt : <input type="text" size="7" id="modifier_taux_interet_{$compte.id}" title="Taux d'intérêt" placeholder="Taux d'intérêt" value="{$compte.taux_interet}"> %
            </td>
            <td colspan="5">
              <form id="modifier_compte" name="modifier_compte" action="{$url_base}" method="post" data-verifiechampsligne='{$compte.id}'>
                <input type="hidden" name="nonce" value="{$nonce}" />
                <input type="hidden" name="action" value="compte_modifier_confirmer" />
                <input type="hidden" id="modifier_compte_id" value="{$compte.id}" />
                <input type="button" name="soumettre" data-form="modifier_compte" data-parametreurl=' {ldelim}"modifier_compte_id":{$compte.id}{rdelim} ' value="Modifier" />
                <input type="button" id="modifier_compte_cmd_cancel" value="Annuler" /> <br />
                <div style="display:none;">
                  <input type="text" id="modifier_description"  name="modifier_description"  value="{$compte.description}" />
                  <input type="text" id="modifier_methode_calcule_interet" name="modifier_methode_calcule_interet"  value="{$compte.methode_calcule_interet}">
                  <input type="text" id="modifier_taux_interet"  name="modifier_taux_interet"  value="{$compte.taux_interet}">
                </div>
              </form>
            </td>
          </tr>
        {/if}
      {/foreach}
    {/if}
  {/foreach}
</table>


<br />
<hr />
<br />


<table border="1">
  <tr class="couleur_table_titre">
    <td colspan="1" align="center"><b>Ajouter un compte</b></td>
  </tr>
  <tr>
    <td>
      <form id="ajouter_compte" name="ajouter_compte" action="{$url_base}" method="post">
        <input type="hidden" name="nonce" value="{$nonce}">
        <input type="hidden" name="action" value="ajouter_compte" />
        Description : <input type="text" size="40" name="ajouter_compte_description" title="Description" placeholder="Description" value="" /><br />
        Groupe : {include file="html_liste_groupe.tpl" name="ajouter_compte_groupe" defaultvalue="" disabled=False extra=""}<br />
        Type de compte : {include file="html_liste_type_compte.tpl" name="ajouter_compte_type" defaultvalue="" disabled=False extra=""}<br />
        Méthode de calcule de l'intérêt : 
          <select id="ajouter_compte_methode_calcule_interet" name="ajouter_compte_methode_calcule_interet" title="Méthode de calcule de l'intérêt" placeholder="Méthode de calcule de l'intérêt">
            {foreach $liste_methode_calcule_interet as $id => $ligne}
              <option value="{$id}">{$ligne}</option>
            {/foreach}
          </select><br />
        Taux d'intérêt : <input type="text" size="7" name="ajouter_compte_taux_interet" title="Taux d'intérêt" placeholder="Taux d'intérêt" value="0.00"> %<br>
        Ordre : <select id="ajouter_compte_ordre" name="ajouter_compte_ordre" title="Ordre" placeholder="Ordre">
                  {foreach $compte_listeordre as $id => $ligne}
                    <option value="{$id}">{$ligne}</option>
                  {/foreach}
                </select><br />
        <input type="button" name="soumettre" value="Ajouter compte" data-form="ajouter_compte" data-parametreurl="" />
      </form>
    </td>
  </tr>
</table>


<br />
<hr />
<br />


<table border="1">
  <tr class="couleur_table_titre">
    <td colspan=""1" align="center"><b>Ajouter un groupe</b></td>
  </tr>
  <tr>
    <td>
      <form id="ajouter_groupe" name="ajouter_groupe" action="{$url_base}" method="post">
        <input type="hidden" name="nonce" value="{$nonce}">
        <input type="hidden" name="action" value="ajouter_groupe" />
        Description : <input type="text" size="40" id="ajouter_groupe_description" name="ajouter_groupe_description" title="Description" placeholder="Description" value="" /><br />
        Ordre : <select id="ajouter_groupe_ordre" name="ajouter_groupe_ordre" title="Ordre" placeholder="Ordre">
                  {foreach $groupe_listeordre as $id => $ligne}
                    <option VALUE="{$id}">{$ligne}</option>
                  {/foreach}
                </select><br />
        <input type="button" name="soumettre" value="Ajouter groupe" data-form="ajouter_groupe" data-parametreurl="" /><br />
      </form>
    </td>
  </tr>
</table>


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
