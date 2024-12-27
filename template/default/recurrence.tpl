{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


<table border="1">
  <tr class="couleur_table_titre">
    <td colspan="12" align="center"><b>Liste des d&eacute;penses g&eacute;n&eacute;r&eacute;</b></td>
  </tr>
  <tr class="couleur_table_colonne_titre">
    <td width="85" align="center">Du</td>
    <td width="85" align="center">Au</td>
    <td width="170" align="center">Auto-g&eacute;n&eacute;re</td>
    <td width="170" align="center">Type d'interval</td>
    <td width="70" align="center">Compte</td>
    <td width="110" align="center">Type transaction</td>
    <td width="130" align="center">Type d&eacute;pense</td>
    <td width="100" align="center">Description</td>
    <td width="150" align="center">Notes</td>
    <td width="80" align="center">Montant</td>
    <td width="40" align="center">Act.</td>
    <td width="40" align="center">Ordre</td>
  </tr>
  {foreach $recurrence_list as $id => $recurrence}
    {if $recurrence.numeroLigne is odd by 1}
      <tr id="row_{$recurrence.id}" class="couleur_table_ligne_impair">
    {else}
      <tr id="row_{$recurrence.id}" class="couleur_table_ligne_pair">
    {/if}
    {if $recurrence.id == $modifier_recurrence_id}
      <td align="center">{$recurrence.date_debut}<input type="hidden" id="recurrence_date_debut_{$recurrence.id} name="recurrence_date_debut_{$recurrence.id}" value="{$recurrence.date_debut}" /></td>
        <td align="center">
          <input type="text"
            id="recurrence_date_fin_{$recurrence.id}" name="recurrence_date_fin_{$recurrence.id}"
            title="Date de fin" placeholder="Date de fin"
            value="{$recurrence.date_fin}" onClick="GetDate(this);" />
        </td>
        <td><input type="text" id="recurrence_auto_interval_{$recurrence.id}" name="recurrence_auto_interval_{$recurrence.id}" value="{$recurrence.auto_interval}" size="3" placeholder="interval" title="Auto Interval" />
              <select id="recurrence_auto_type_interval_{$recurrence.id}" name="recurrence_auto_type_interval_{$recurrence.id}" title="Auto Type Interval">
                {foreach $auto_type_interval_list as $id => $ati_ligne}
                  <option value="{$id}" {if $id == $recurrence.auto_type_interval} selected {/if} >{$ati_ligne}</option> <br />
                {/foreach}
              </select> &agrave; l'avance
        </td>
        <td><select id="recurrence_type_interval_{$recurrence.id}" name="recurrence_type_interval_{$recurrence.id}" title="Type Interval">
                  {foreach $type_interval_list as $id => $ligne}
                    <option value="{$id}" {if $recurrence.type_interval == $id} selected {/if} >{$ligne}</option> <br />
                  {/foreach}
            </select> <br />
            au <input type="text" id="recurrence_interval_{$recurrence.id}" name="recurrence_interval_{$recurrence.id}"
                  title="Interval" placeholder="Interval" value="{$recurrence.interval}" size="3" />
        </td>
        <td align="center">
          {include file="html_liste_compte.tpl" id="recurrence_compte_id_{$recurrence.id}" name="recurrence_compte_id_{$recurrence.id}"
                  defaultvalue=$recurrence.compte_id disabled=False extra=""}
        </td>
        <td>{$liste_type_transaction[ $recurrence.type_transaction_id ].description}
            {if $recurrence.type_transaction_id == $type_transaction_transfert}
              <br />vers : {include file="html_liste_compte.tpl"
                                    id="recurrence_transfert_compte_id_{$recurrence.id}"
                                    name="recurrence_transfert_compte_id_{$recurrence.id}"
                                    defaultvalue=$recurrence.transfert_compte_id disabled=False extra=""} 
            {/if}
        </td>
        <td> <!-- {$liste_type_depense[ $recurrence.type_depense_id ].description} -->
            {include file="html_liste_type_depense.tpl"
               id="recurrence_type_depense_id_{$recurrence.id}"
               name="recurrence_type_depense_id_{$recurrence.id}"
               title="Type d&eacute;pense"
               placeholder="Type d&eacute;pense"
               defaultvalue=$recurrence.type_depense_id
               disabled=False extra=" OnChange=\"ChoisirSymbole('recurrence_type_depense_id_{$recurrence.id}', 'recurrence_symbole_{$recurrence.id}')\" "}
        </td>
        <td><input type="text" id="recurrence_description_{$recurrence.id}" name="recurrence_description_{$recurrence.id}" value="{$recurrence.description}" title="Description" placeholder="Description" /></td>
        <td><textarea name="recurrence_notes_{$recurrence.id}" rows="10" cols="20" title="Notes" placeholder="Notes">{$recurrence.notes}</textarea></td>
        <td>{include file="html_liste_symbole.tpl" id="" name="recurrence_symbole_{$recurrence.id}" defaultvalue=$recurrence.symbole disabled=False extra=""}
            <input type="number" step="any" id="recurrence_montant_{$recurrence.id}" name="recurrence_montant_{$recurrence.id}" size="7" value="{$recurrence.montant}" title="Montant" placeholder="Montant" /> <br />
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="right" colspan="12">
            <form id="modifier_recurrence_form" name="modifier_recurrence_form" action="{$url_base}" method="post" autocomplete="off" data-verifiechampsligne='{$recurrence.id}'>
              <input type="hidden" name="nonce" value="{$nonce}" />
              <input type="hidden" name="action" value="modifier_recurrence_confirme" />
              <input type="hidden" id="recurrence_id" name="recurrence_id" value="{$recurrence.id}" />
                  
              <input type="hidden" name="recurrence_date_debut" value="{$recurrence.date_debut}" />
              <input type="hidden" name="recurrence_date_fin" value="{$recurrence.date_fin}" />
              <input type="hidden" name="recurrence_auto_interval" value="{$recurrence.auto_interval}" />
              <input type="hidden" name="recurrence_auto_type_interval" value="{$recurrence.auto_type_interval}" />

              <input type="hidden" name="recurrence_type_interval" value="{$recurrence.type_interval}" />
              <input type="hidden" name="recurrence_interval" value="{$recurrence.interval}" />
            
              <input type="hidden" name="recurrence_compte_id" value="{$recurrence.compte_id}" />
              <input type="hidden" name="recurrence_type_transaction_id" value="{$recurrence.type_transaction_id}" />
              <input type="hidden" name="recurrence_transfert_compte_id" value="{$recurrence.transfert_compte_id}" />
            
              <input type="hidden" name="recurrence_type_depense_id" value="{$recurrence.type_depense_id}" />
            
              <input type="hidden" name="recurrence_description" value="{$recurrence.description}" />
              <input type="hidden" name="recurrence_notes" value="{$recurrence.notes}" />
            
              <input type="hidden" name="recurrence_symbole" value="{$recurrence.symbole}" />
              <input type="hidden" name="recurrence_montant" value="{$recurrence.montant}" />
       
              Les entr&eacute;es &agrave; venir seront modifi&eacute;es (sauf ceux qui sont r&eacute;concilier ou pointer)<br />
              <input type="checkbox" name="recurrence_modifierpasser" value="1" title="Inclure les dates pass&eacute;es" />Inclure aussi les entr&eacute;es des dates pass&eacute;<br />
              Changer les valeurs de la r&eacute;currence n'affectera pas, ni n'effacera les entr&eacute;es existante<br />
              <input type="button" name="soumettre" value="Sauvegarder" title="Sauvegarder" data-form="modifier_recurrence_form" data-parametreurl=' {ldelim}"modifier_recurrence_id":{$recurrence.id}{rdelim} ' />
              <input type="button" id="modifier_recurrence_cmd_cancel" value="Annuler" title="Annuler" />
              <br /><br />
            </form>
          </td>
    {else}
        <td align="center">{$recurrence.date_debut}</td>
        <td align="center">{$recurrence.date_fin}</td>
        <td>{$recurrence.auto_interval} {$recurrence.auto_type_interval_text} avant</td>
        <td>{$recurrence.type_interval_text}<br />
            au {$recurrence.interval}
        </td>
        <td align="center">{$liste_compte[ $recurrence.compte_id ].description}</td>
        <td>{$liste_type_transaction[ $recurrence.type_transaction_id ].description}
            {if $recurrence.type_transaction_id == $type_transaction_transfert}
              <br />vers : {$recurrence.transfert_compte_id_text}
            {/if}
        </td>
        <td>{$liste_type_depense[ $recurrence.type_depense_id ].description}</td>
        <td>{$recurrence.description}</td>
        <td>{nl2br($recurrence.notes)}</td>
        <td align="right">{$recurrence.montantformat}</td>
        <td align="center">
          {if $recurrence.id != $effacer_recurrence_id}
            <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "modifier_recurrence", "modifier_recurrence_id": {$recurrence.id} {rdelim} ' title="Modifier"><img src="images/edit.png" title="Modifier" /></a>
            <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "effacer_recurrence", "effacer_recurrence_id": {$recurrence.id} {rdelim} ' title="Effacer"><img src="images/drop.png" title="Effacer" /></a>
          {/if}
        </td>
        <td align="center">
          {if $recurrence.id != $effacer_recurrence_id}
            {if $recurrence.estPremier == false}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "deplacer_recurrence", "recurrence_id": {$recurrence.id}, "ordre": {$recurrence.ordre - 3} {rdelim} ' title="Vers le haut"><img src="images/moveup.gif" title="Vers le haut" /></a>
            {else}
              <img width=16 height=16 src="images/noicon.gif" title="" />
            {/if}
            {if $recurrence.estDernier == false}
              <a href='{$url_base}' data-parametreurl=' {ldelim}"action": "deplacer_recurrence", "recurrence_id": {$recurrence.id}, "ordre": {$recurrence.ordre + 3} {rdelim} ' title="Vers le bas"><img src="images/movedown.gif" title="Vers le bas" /></a>
            {else}
              <img width=16 height=16 src="images/noicon.gif" title="" />
            {/if}
          {/if}
        </td>
        {if $recurrence.id == $effacer_recurrence_id}
          </tr>
         <tr>
          <td align="right" colspan="12">
            <form id="effacer_recurrence_form" name="effacer_recurrence_form" action="{$url_base}" method="post">
              <input type="hidden" name="nonce" value="{$nonce}" />
              <input type="hidden" id="action" name="action" value="effacer_recurrence_confirme" />
              <input type="hidden" id="recurrence_id" name="recurrence_id" value="{$recurrence.id}" />
              Les entr&eacute;es &agrave; venir seront effacer (sauf ceux qui sont r&eacute;concilier ou pointer)<br />
              <input type="checkbox" name="recurrence_modifierpasser" value="1" title="Inclure les dates pass&eacute;es" />Inclure aussi les entr&eacute;es des dates pass&eacute;<br />
              <input type="button" name="soumettre" value="Effacer" title="Effacer" data-form="effacer_recurrence_form" data-parametreurl=' {ldelim}"effacer_recurrence_id":{$recurrence.id}{rdelim} ' />
              <input type="button" id="effacer_recurrence_cmd_cancel" value="Annuler" title="Annuler" />
              <br /><br />
            </form>
          </td>
        {/if}
        
      {/if}
    </tr>
  {/foreach}
</table>


<br />
<hr />
<br />


<form id="ajouter_recurrence_form" name="ajouter_recurrence_form" action="{$url_base}" method="post" autocomplete="off">
<table border="1" id="ajouter_recurrence_table">
  <tr>
    <td class="couleur_table_colonne_titre" align="center" colspan="11"><b>Ajout</b></td>
  </tr>
  <tr>
        <td>
          <table border="0">
            <input type="hidden" name="nonce" value="{$nonce}">
            <input type="hidden" name="action" value="ajouter_recurrence" />
            <input type="hidden" name="recurrence_id" value="0" />
            <tr>
              <td>
                Date de d&eacute;but : <input type="text"
                                              id="ajouter_recurrence_date_debut" name="ajouter_recurrence_date_debut"
                                              title="Date de d&eacute;but" placeholder="Date de d&eacute;but"
                                              value="{$ajouter_recurrence_date_debut}" onClick="GetDate(this);" /> <br />
                Date de fin : <input type="text"
                                     id="ajouter_recurrence_date_fin" name="ajouter_recurrence_date_fin"
                                     title="Date de fin" placeholder="Date de fin"
                                     value="{$ajouter_recurrence_date_fin}" onClick="GetDate(this);" /> <br />

                Auto-g&eacute;n&eacute;re : <input type="text" id="ajouter_recurrence_auto_interval" name="ajouter_recurrence_auto_interval"
                                     title="Auto Interval" placeholder="Auto Interval" value="{$ajouter_recurrence_auto_interval}" size="3" />
                  <select id="ajouter_recurrence_auto_type_interval" name="ajouter_recurrence_auto_type_interval" title="Auto Type Interval">
                    {foreach $auto_type_interval_list as $id => $ligne}
                      <option value="{$id}" {if $ajouter_recurrence_auto_type_interval == $id} selected {/if} >{$ligne}</option> <br />
                    {/foreach}
                  </select> &agrave; l'avance
                <br />

                Type d'interval : <select id="ajouter_recurrence_type_interval" name="ajouter_recurrence_type_interval" title="Type Interval">
                  {foreach $type_interval_list as $id => $ligne}
                    <option value="{$id}" {if $ajouter_recurrence_type_interval == $id} selected {/if} >{$ligne}</option> <br />
                  {/foreach}
                </select> <br />
                Interval : <input type="text" id="ajouter_recurrence_interval" name="ajouter_recurrence_interval"
                                  title="Interval" placeholder="Interval" value="{$ajouter_recurrence_interval}" size="3" /> <br />

                Compte : {include file="html_liste_compte.tpl" id="" name="ajouter_recurrence_compte_id" defaultvalue=$ajouter_recurrence_compte_id disabled=False extra=""} <br />

                Type de transaction : {include file="html_liste_type_transaction.tpl" 
                  id=""
                  name="ajouter_recurrence_type_transaction_id" 
                  defaultvalue=$ajouter_recurrence_type_transaction_id
                  disabled=False 
                  disablevalue=$type_transaction_reconciliation 
                  disablevalue2=$type_transaction_ajustement
                  extra=" OnChange=\"DeactiveTransfertCompte('ajouter_recurrence_type_transaction_id', 'ajouter_recurrence_transfert_compte')\" "} <br />
                <div id="ajouter_recurrence_transfert_compte">
                  Transfert vers le compte : {include file="html_liste_compte.tpl"
                                                      id=""
                                                      name="ajouter_recurrence_transfert_compte_id"
                                                      defaultvalue=$ajouter_recurrence_transfert_compte_id disabled=False extra=""} <br />
                </div>
                Type de d&eacute;pense : {include file="html_liste_type_depense.tpl" 
                                                  id=""
                                                  name="ajouter_recurrence_type_depense_id"
                                                  defaultvalue=$ajouter_recurrence_type_depense_id disabled=False
                                                  extra=" OnChange=\"ChoisirSymbole('ajouter_recurrence_type_depense_id', 'ajouter_recurrence_symbole')\" "  } <br />

                Description : <input type="text" id="ajouter_recurrence_description" name="ajouter_recurrence_description"
                                     title="Decription" placeholder="Description" value="{$ajouter_recurrence_description}" /> <br />
                Montant : {include file="html_liste_symbole.tpl" id="" name="ajouter_recurrence_symbole" defaultvalue=$ajouter_recurrence_symbole disabled=False extra=""}
               <input type="number" step="any" id="ajouter_recurrence_montant" name="ajouter_recurrence_montant" title="Montant" placeholder="Montant" size="7" value="{$ajouter_recurrence_montant}" /> <br />
             </td>
             <td width="20">&nbsp;</td>
             <td>
               Notes :<br /> <textarea id="ajouter_recurrence_notes" name="ajouter_recurrence_notes" title="Notes" placeholder="Notes" rows="10" cols="40">{$ajouter_recurrence_notes}</textarea> <br />
               <input type="hidden" id="ajouter_recurrence_modifierpasser" name="ajouter_recurrence_modifierpasser" value="0" />
             </td>
           </tr>
        <tr>
          <td colspan=3><input type="button" id="ajouter_recurrence_cmd" name="soumettre" value="Ajouter recurrencer" data-form="ajouter_recurrence_form" data-parametreurl="" /></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
