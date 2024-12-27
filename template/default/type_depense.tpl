{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


<table border="1">
  <tr class="couleur_table_titre">
    <td colspan="4" align="center"><b>Liste des types de d&eacute;pense</b></td>
  </tr>
  <tr class="couleur_table_colonne_titre">
    <td width="250">Description</td>
    <td width="45">Symbole</td>
    <td width="45" align="center">Ordre</td>
    <td width="45" align="center">Act.</td>
  </tr>
  {foreach $liste_type_depense as $id => $type_depense}
    {if $type_depense.numeroLigne is odd by 1}
      <tr class="couleur_table_ligne_impair">
    {else}
      <tr class="couleur_table_ligne_pair">
    {/if}
      {if $modifier_type_depense_id == $type_depense.id}
          <td>
            <input type="text" size="40" id="modifier_type_depense_description_{$type_depense.id}" name="modifier_type_depense_description_{$type_depense.id}" title="Description" placeholder="Description" value="{$type_depense.description}" />
          </td>
          <td align="center">
            {include file="html_liste_symbole.tpl" id="modifier_type_depense_symbole_{$type_depense.id}" name="modifier_type_depense_symbole_{$type_depense.id}" defaultvalue=$type_depense.symbole disabled=False extra=""}
          </td>
          <td align="center" colspan=2>
            <form id="modifier_type_depense" name="modifier_type_depense" action="{$url_base}" method="post" data-verifiechampsligne='{$type_depense.id}'>
              <input type="hidden" name="nonce" value="{$nonce}" />
              <input type="hidden" name="action" value="modifier_type_depense" />
              <input type="hidden" name="type_depense_id" value="{$type_depense.id}" />
              <input type="hidden" id="modifier_type_depense_description" name="modifier_type_depense_description" value="{$type_depense.description}" />
              <input type="hidden" id="modifier_type_depense_symbole" name="modifier_type_depense_symbole" value="{$type_depense.symbole}" />
              <input type="button" name="soumettre" value="Sauvegarder" data-form="modifier_type_depense" />
            </form>
          </td>
      {elseif $effacer_type_depense_id == $type_depense.id}
        <td>&nbsp;{$type_depense.description}</td>
        <td align="center">{$liste_symbole[$type_depense.symbole]}</td>
        <td align="center" colspan=2>
          <form id="effacer_type_depense" name="effacer_type_depense" action="{$url_base}" method="post">
            <input type="hidden" name="nonce" value="{$nonce}" />
            <input type="hidden" name="action" value="effacer_type_depense" />
            <input type="hidden" name="type_depense_id" value="{$type_depense.id}" />
            <input type="submit" value="Effacer" data-form="effacer_type_depense" />
        </form>
          </td>
      {else}
        <td>&nbsp;{$type_depense.description}</td>
        <td align="center">{$liste_symbole[$type_depense.symbole]}</td>
        <td align="center">
            {if $type_depense.estPremier == false}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "deplacer_type_depense", "type_depense_id": {$type_depense.id}, "ordre": {$type_depense.ordre - 3} {rdelim} ' title="Vers le haut"><img src="images/moveup.gif" title="Vers le haut" /></a>
            {else}
              <img width=16 height=16 src="images/noicon.gif" title="" />
            {/if}
            {if $type_depense.estDernier == false}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "deplacer_type_depense", "type_depense_id": {$type_depense.id}, "ordre": {$type_depense.ordre + 3} {rdelim} ' title="Vers le bas"><img src="images/movedown.gif" title="Vers le bas" /></a>
            {else}
              <img width=16 height=16 src="images/noicon.gif" title="" />
            {/if}
        </td>
        <td align="center">
            {if $type_depense.id < 50}
              <!-- 'Autres' est un type systeme -->
            {else}
              <a href="{$url_base}" data-parametreurl=' {ldelim}"modifier_type_depense_id": {$type_depense.id} {rdelim} ' title="+ 3 mois"><img src="images/edit.png" title="Modifier" /></a>
                
              {if $type_depense.useCount > 0}
                <img width=16 height=16 src="images/noicon.gif" title="" />
              {else}
                <a href="{$url_base}" data-parametreurl=' {ldelim}"effacer_type_depense_id": {$type_depense.id} {rdelim} ' title="+ 3 mois"><img src="images/drop.png" title="Effacer" /></a>
              {/if}
            {/if}
        </td>
      {/if}
    </tr>
  {/foreach}
</table>


<br />
* Il est impossible d'effacer un type de d&eacute;pense s'il est d&eacute;j&agrave; utilis&eacute;<br />


<br />
<hr />
<br />


<table border="1">
  <tr>
    <td class="couleur_table_colonne_titre" align="center"><b>Ajout un type de d&eacute;pense</b></td>
  </tr>

  <tr>
    <td>
      <form id="ajouter_type_depense" name="ajouter_type_depense" action="{$url_base}" method="post">
        <input type="hidden" name="nonce" value="{$nonce}">
        <input type="hidden" name="action" value="ajouter_type_depense">
        Description : <input type="text" size="40" id="ajouter_depense_description" name="ajouter_depense_description" title="Description" placeholder="Description" value=""><br />
        Symbole : {include file="html_liste_symbole.tpl" name="ajouter_depense_symbole" defaultvalue="" disabled=False extra=""}<br />
        Ordre : <select id="ajouter_depense_ordre" name="ajouter_depense_ordre" title="Ordre" placeholder="Ordre">
                  {foreach $listordre as $id => $ligne}
                    <option VALUE={$id}>{$ligne}</option>
                  {/foreach}
                </select><br />
        <input type="button" name="soumettre" value="Ajouter" data-form="ajouter_type_depense" />
      </form>
    </td>
  </tr>
</table>


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
