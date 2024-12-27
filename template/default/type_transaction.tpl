{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


<table border="1">
  <tr class="couleur_table_titre">
    <td colspan="4" align="center"><b>Liste des types de transaction</b></td>
  </tr>
  <tr class="couleur_table_colonne_titre">
    <td width="250">Description</td>
    <td width="45" align="center">Ordre</td>
  </tr>
  {foreach $liste_type_transaction as $id => $type_transaction}
    {if $type_transaction.numeroLigne is odd by 1}
      <tr class="couleur_table_ligne_impair">
    {else}
      <tr class="couleur_table_ligne_pair">
    {/if}
      <td>&nbsp;{$type_transaction.description}</td>
      <td align="center">
        {if $type_transaction.estPremier == false}
          <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "transaction_deplacer", "type_transaction_id": {$type_transaction.id}, "ordre": {$type_transaction.ordre - 3} {rdelim} ' title="Vers le haut"><img src="images/moveup.gif" title="Vers le haut" /></a>
        {else}
          <img width="16" height="16" src="images/noicon.gif" title="" />
        {/if}
        {if $type_transaction.estDernier == false}
          <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "transaction_deplacer", "type_transaction_id": {$type_transaction.id}, "ordre": {$type_transaction.ordre + 3} {rdelim} ' title="Vers le haut"><img src="images/movedown.gif" title="Vers le bas" /></a>
        {else}
          <img width="16" height="16" src="images/noicon.gif" title="" />
        {/if}
      </td>
    </tr>
  {/foreach}
</table>


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
