    <br>
    <table border="0" width="900" cellpadding="1" cellspacing="1">
      <tr class="couleur_table_titre">
        <td colspan="6" align="center">
          <b>D&eacute;tail de la r&eacute;conciliation</b>
        </td>
      </tr>
      <tr class="couleur_table_colonne_titre">
        <td width="110">Transaction</td>
        <td width="90">Date</td>
        <td width="110">D&eacute;pense</td>
        <td width="110">Description</td>
        <td width="50">Act.</td>
        <td width="100" align="center">Montant</td>
      </tr>
    {foreach $lignes as $id => $depense}
      <tr class="{$depense.couleur}">
        {if $depense.estAujourdhui == True}
          <td>&nbsp;</td>
          <td>{$depense.dateformat}</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        {else}
          <td>{$depense.type_transaction_description}</td>
          <td>{$depense.dateformat}</td>
          <td>{$depense.type_depense_description}</td>
          <td {if $depense.notes != ""}
                title = "{$depense.notes}"
              {/if}>
              {if $depense.notes != ""}
                <img src="images/notes.png" />
              {/if}
              {$depense.description}</td>
          <td align="center">
            {if $depense.type_transaction_id == $type_transaction_reconciliation}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "vuereconciliationreference", "compte_id": {$compte_id}, "depense_id": {$depense.id} {rdelim} ' title="Voir"><img src="images/eye.jpg" title="Voir" /></a>
            {else}
              <img width=16 height=16 src="images/noicon.gif" title="A" />
            {/if}
          </td>
          <td class="{if $depense.symbole == 1}
                          couleur_montant_positif
                        {else}
                          couleur_montant_negatif
                        {/if}" align="right">
                        {$depense.montantformat}</td>
        {/if}
      </tr>
    {/foreach}

    <tr class="couleur_table_colonne_titre">
      <td colspan="5">&nbsp;</td>
      <td class="{if $depense.solde_symbole == 1}
                          couleur_montant_positif
                        {else}
                          couleur_montant_negatif
                        {/if}" align="right">
                        {$depense.soldeformat}</td>
    </tr>
  </table>
