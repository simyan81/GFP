{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


<table border="1">
  <tr class="couleur_table_titre">
    <td colspan="4" align="center"><b>Liste des types de compte<b></td>
  </tr>
  <tr class="couleur_table_colonne_titre">
    <td width="250">Description</td>
    <td width="45" align="center">Ordre</td>
  </tr>
  {foreach $liste_type_compte as $id => $type_compte}
    {if $type_compte.numeroLigne is odd by 1}
      <tr class="couleur_table_ligne_impair">
    {else}
      <tr class="couleur_table_ligne_pair">
    {/if}
      <td>&nbsp;{$type_compte.description}</td>
      <td align="center">
        {if $type_compte.estPremier == false}
          <a href='{$url_base}' data-parametreurl=' {ldelim}"page":"type_compte", "action": "compte_deplacer", "type_compte_id": {$type_compte.id}, "ordre": {$type_compte.ordre - 3} {rdelim} ' title="Vers le haut"><img src="images/moveup.gif" title="Vers le haut" /></a>
        {else}
          <img width="16" height="16" src="images/noicon.gif" title="" />
        {/if}
        {if $type_compte.estDernier == false}
          <a href='{$url_base}' data-parametreurl=' {ldelim}"page":"type_compte", "action": "compte_deplacer", "type_compte_id": {$type_compte.id}, "ordre": {$type_compte.ordre + 3} {rdelim} ' title="Vers le bas"><img src="images/movedown.gif" title="Vers le bas" /></a>
        {else}
          <img width="16" height="16" src="images/noicon.gif" title="" />
        {/if}
      </td>
    </tr>
  {/foreach}
</table>


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
