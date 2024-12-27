{include file="page_entete.tpl"}
{include file="menu_entete.tpl"}


  {if $action == "vuereconciliationreference"}
    {include file="depense_vuereconcilier.tpl"}


  {else}
    <br />
    <table border="1" cellpadding="1" cellspacing="1">
      <tr class="couleur_table_titre">
        <td colspan="9" align="center">
          <b>D&eacute;tail du compte : {$liste_compte[$compte_id].description}</b><br />
          {if $liste_compte[$compte_id].methode_calcule_interet > 0 && $liste_compte[$compte_id].taux_interet > 0}
            Taux d'intérêt : {$liste_compte[$compte_id].taux_interet} %
          {/if}
        </td>
      </tr>
      <tr class="couleur_table_colonne_titre">
        <td width="110">Transaction</td>
        <td width="80" >Date</td>
        <td width="110">D&eacute;pense</td>
        <td width="170">Description</td>
        <td width="70" align="center">Act.</td>
        <td width="70" align="center">Rec.</td>
        <td width="100" align="center">Montant</td>
        <td width="100" align="center">Solde</td>
        <td width="70" align="center">Pointer</td>
      </tr>
    {foreach $lignes as $id => $depense}
      {if $depense.afficherSeparateurAvant == True}
        <tr>
          <td colspan="9">
            &nbsp;
          </td>
        </tr>
      {/if}
      <tr id="ligne_{$depense.id}" class="{$depense.couleur}">
        <!-- if $depense.estAujourdhui == True
          <td>&nbsp;</td>
          <td>{$depense.dateformat}</td>
          <td colspan="5">&nbsp;</td>
          <td class="{if $depense.solde_symbole == 1}
                          couleur_montant_positif
                        {else}
                          couleur_montant_negatif
                        {/if}" align="right">{$depense.soldeformat}</td>
          <td>&nbsp;</td>
        -->
        {if $depense.estJourneeVide == True}
          <td>&nbsp;</td>
          <td>{$depense.dateformat}</td>
          <td colspan="5">{$depense.description}</td>
          <td class="{if $depense.solde_symbole == 1}
                          couleur_montant_positif
                        {else}
                          couleur_montant_negatif
                        {/if}" align="right">{$depense.soldeformat}</td>
          <td>&nbsp;</td>
        {else}
<!-- Transaction -->
          <td>
            {if $depense.id == $modifier_depense_id}
              {include file="html_liste_type_transaction.tpl" id="" name="modifier_type_transaction_id_{$depense.id}" defaultvalue=$depense.type_transaction_id disabled=True disablevalue=-1 extra=""}<br />
              {if $depense.tr_compte_id > -1}
                {if $depense.tr_compte_id == $compte_id}
                  vers le compte :
                {else}
                  depuis le compte :
                {/if}  
                {include file="html_liste_compte.tpl" id="" name="modifier_transfert_compte_id_{$depense.id}" defaultvalue=$depense.tr_compte_id disabled=True disablevalue={$depense.compte_id} extra=""}
              {/if}
            {else}
              {$depense.type_transaction_description}
              {if $depense.type_transaction_id == $type_transaction_transfert}
                <br /><font size="-1">
                  {if $depense.symbole == 0}
                  vers 
                {else}
                  depuis 
                {/if}  
                {$depense.tr_compte_description}</font>
              {/if}
            {/if}
          </td>
<!-- Date -->
          <td>
            {if $depense.id == $modifier_depense_id && $depense.peuxModifier == true}
              <input type="text" id="modifier_date_depense_{$depense.id}" name="modifier_date_depense_{$depense.id}" title="Date" placeholder="Date" value="{$depense.date_seule}" onClick="GetDate(this);" />
            {else}
              {if $depense.dateformat == ''}&nbsp;{else}{$depense.dateformat}{/if}
            {/if}          
          </td>
<!-- Type depense -->
          <td>
            {if $depense.id == $modifier_depense_id}
              {include file="html_liste_type_depense.tpl"
                       id="" name="modifier_type_depense_id_{$depense.id}" defaultvalue=$depense.type_depense_id disabled=False}
            {else}
              {$depense.type_depense_description}
            {/if}
          </td>
<!-- Description et note -->
          <td {if $depense.notes != ""}
                title = "{$depense.notes}"
              {/if}>
            {if $depense.id == $modifier_depense_id}
              <input type="text" id="modifier_description_{$depense.id}" name="modifier_description_{$depense.id}" title="Description" placeholder="Description" value="{$depense.description}" />
              <textarea id="modifier_notes_{$depense.id}" name="modifier_notes_{$depense.id}" title="Notes" placeholder="Notes" rows="10" cols="40">{$depense.notes}</textarea>
            {else}
              {if $depense.notes != ""}
                <img src="images/notes.png" title="{$depense.notes}" />
              {/if}
              {$depense.description}
            {/if}
          </td>
<!-- Action et Reconciliation pour ligne virtuelle -->
	         {if  $depense.virtuel == true}
           <td align="center" colspan="2">
             <!!-- Action pour les lignes virtuelle d'interet -->
{if $depense.type_transaction_id == $type_transaction_interet}
  <a href='{$url_base}' data-parametreurl=' {ldelim} "action": "ajouterdepense", 
    "nonce": "{$nonce}",
    "ajouter_date_depense": "{$depense.date_depense}",
    "ajouter_type_transaction_id": "{$type_transaction_interet}",
    "ajouter_description": "Intérêt",
    "ajouter_montant": "{$depense.montant}",
    "ajouter_symbole": "{$depense.symbole}" {rdelim} '
    title="Converti ligne virtuelle en interet"><img width="16" height="16" src="images/convertir.png" title="Convertir" /></a>
{/if}
             &nbsp;
           </td>
         {else}
<!-- Action -->
            <td align="center">
              {if $modifier_depense_id == $depense.id || $effacer_depense_id == $depense.id || $depense.virtuel == true}
                &nbsp;
                <!-- Aucune action en mode modifier ou effacer -->
              {else}
                {if $depense.type_transaction_id == $type_transaction_reconciliation}
                  <a href='{$url_base}' data-parametreurl=' {ldelim}"depense_id": {$depense.id}, "action": "vuereconciliationreference" {rdelim} ' title="Voir"><img src="images/eye.jpg" title="Voir" /></a>
                {else}
                  <img width="16" height="16" src="images/noicon.gif" title="A" />
                {/if}

                <a href='{$url_base}' data-parametreurl=' {ldelim}"modifier_depense_id": {$depense.id}, "action": "depense_modifier" {rdelim} ' title="Modifier"><img src="images/edit.png" title="Modifier" /></a>

                {if $depense.peuxEffacer == True}
                  <a href='{$url_base}' data-parametreurl=' {ldelim}"effacer_depense_id": {$depense.id}, "action": "depense_effacer" {rdelim} ' title="Effacer"><img src="images/drop.png" title="Effacer" /></a>
                {else}
                  <img width="16" height="16" src="images/noicon.gif" title="A" />
                {/if}
              {/if}
            </td>
<!-- Reconciliation -->
            <td align="center" id="td_reconcilier_{$depense.id}">
              {if $modifier_depense_id == $depense.id || $effacer_depense_id == $depense.id || $depense.virtuel == true}
                &nbsp;
                <!-- Aucun checkbox en mode modifier ou effacer -->
              {else}
                {if $depense.reconcilier_depense_id > 0}
                  (ref #{$depense.transfert_compte_id})
                {else}
                  <input type="checkbox" id="chk_reconcilier_{$depense.id}" name="reconcilier[]" value="{$depense.id}" />
                {/if}
              {/if}
            </td>
          {/if}
<!-- Montant -->
          <td class="{if $depense.symbole == 1}
                          couleur_montant_positif
                        {else}
                          couleur_montant_negatif
                        {/if}" align="right">
            {if $depense.id == $modifier_depense_id}
              {if $depense.peuxModifier == True}
                {include file="html_liste_symbole.tpl" id="" name="modifier_symbole_{$depense.id}" defaultvalue=$depense.symbole disabled=False extra=""}
                <input type="number" step="any" id="modifier_montant_{$depense.id}" name="modifier_montant_{$depense.id}" title="Montant" placeholder="Montant" size="7" value="{$depense.montant}" /><br />
              {else}
                {$depense.montantformat}
              {/if}
            {else}
              <label id="montant_{$depense.id}" data-montant="{if $depense.symbole <> 1}-{/if}{$depense.montant}">{$depense.montantformat}</label>
            {/if}
          </td>
<!-- Solde -->
          <td class="{if $depense.solde_symbole == 1}
                          couleur_montant_positif
                        {else}
                          couleur_montant_negatif
                        {/if}" align="right">{$depense.soldeformat}</td>
<!-- Pointer -->
          <td align="center" id="td_pointer_{$depense.id}">
            {if $modifier_depense_id == $depense.id || $effacer_depense_id == $depense.id || $depense.virtuel == true}
              &nbsp;
              <!-- Aucun checkbox en mode modifier ou effacer -->
            {else}
              <input type="checkbox" id="chk_pointer_{$depense.id}" name="pointer[]" value="{$depense.id}" {if $depense.pointer == 1} checked {/if}
                     data-checked="{if $depense.pointer == 1}checked{/if}" data-changer="0" />
            {/if}
          </td>
        {/if}
      </tr>




<!-- Effacer confirmation -->
      {if $depense.id == $effacer_depense_id}
        <tr>
          <td colspan="4"></td>
          <td colspan="5">
            <form id="effacer_depense" name="effacer_depense" action="{$url_base}" method="post">
              <input type="hidden" name="nonce" value="{$nonce}" />
              <input type="hidden" name="action" value="depense_effacer_confirmer" />
              <input type="hidden" id="effacer_depense_id" value="{$depense.id}" />
              <input type="button" name="soumettre" data-form="effacer_depense" data-parametreurl=' {ldelim}"effacer_depense_id":{$depense.id}{rdelim} ' value="Effacer" />
              <input type="button" id="efface_depense_cmd_cancel" value="Annuler" /><br />
              Si c'est un transfert, l'autre d&eacute;pense sera aussi supprim&eacute;.<br />
              Si cette d&eacute;pense a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; automatiquement, elle ne sera pas rajout&eacute;<br />
              Si la d&eacute;pense a &eacute;t&eacute; r&eacute;concilier, elle ne pourra &ecirc;tre effacer
            </form>
          </td>
        </tr>
      {/if}
<!-- Modification confirmation -->
      {if $depense.id == $modifier_depense_id}
        <tr>
          <td colspan="4"></td>
          <td colspan="5">
            <form id="modifier_depense" name="modifier_depense" action="{$url_base}" method="post" data-verifiechampsligne='{$depense.id}'>
              <input type="hidden" name="nonce" value="{$nonce}" />
              <input type="hidden" name="action" value="depense_modifier_confirmer" />
              <input type="hidden" id="modifier_depense_id" value="{$depense.id}" />
              <input type="button" name="soumettre" data-form="modifier_depense" data-parametreurl=' {ldelim}"modifier_depense_id":{$depense.id}{rdelim} ' value="Modifier" />
              <input type="button" id="modifier_depense_cmd_cancel" value="Annuler" /> <br />
              <div style="display:none;">
                <input type="text" id="modifier_type_transaction_id" name="modifier_type_transaction_id" value="{$depense.type_transaction_id}" />
                <input type="text" id="modifier_transfert_compte_id" name="modifier_transfert_compte_id" value="{$depense.tr_compte_id}" />
                <input type="text" id="modifier_date_depense" name="modifier_date_depense" value="{$depense.date_seule}" />
                <input type="text" id="modifier_type_depense_id" name="modifier_type_depense_id" value="{$depense.type_depense_id}" />
                <input type="text" id="modifier_description" name="modifier_description" value="{$depense.description}" />
                <input type="text" id="modifier_notes" name="modifier_notes" value="{$depense.notes}" />
                <input type="text" id="modifier_montant" name="modifier_montant" value="{$depense.montant}" />
                <input type="text" id="modifier_symbole" name="modifier_symbole" value="{$depense.symbole}" />
              </div>
              Si c'est un transfert, l'autre d&eacute;pense sera aussi modfier.<br />
              <!-- Si cette d&eacute;pense a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; automatiquement,
              elle ne sera plus consid&eacute;rer dans ce groupe<br /> -->
            </form>
          </td>
        </tr>
      {/if}
    {/foreach}




<!-- Ligne pied du tableau -->
    <tr>
      <td colspan="5">Affichage jusqu'au : {$afficherjusquau}<br />
                      &nbsp;&nbsp;
                        <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher": "+1"{rdelim} ' title="+ 1 mois">+ 1 mois</a> | 
                        <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher": "+3"{rdelim} ' title="+ 3 mois">+ 3 mois</a> | 
                        <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher": "+6"{rdelim} ' title="+ 6 mois">+ 6 mois</a> | 
                        <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher": "+12"{rdelim} ' title="+ 1 an">+ 1 an</a> | 
                        <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher": "+24"{rdelim} ' title="+ 2 ans">+ 2 ans</a> 
                      <br />
                      <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher_journee_vide": "0"{rdelim} ' title="Ne pas afficher les journ&eacute;es vides">Ne pas afficher les journ&eacute;es vides</a> |
                      <a href="{$url_base}" data-parametreurl=' {ldelim}"afficher_journee_vide": "1"{rdelim} ' title="Afficher les journ&eacute;es vides">Afficher les journ&eacute;es vides</a>
      </td>
      <td colspan="1" align="center">
        <form id="depense_reconcilier" action="{$url_base}" method="post">
          <input type="hidden" name="nonce" value="{$nonce}" />
          <input type="hidden" name="action" value="reconcilier" />
          <input type="hidden" id="liste_reconcilier_id" name="liste_reconcilier_id" value="" />
          <input type="button" id="depense_reconcilier_cmd" value="R&eacute;concilier" disabled="disabled" />
          <div id="reconcilier_solde">0.00 $</div>
        </form>
      </td>
      <td colspan="2">&nbsp;</td>
      <td colspan="1" align="center">
        <form id="depense_pointer" action="{$url_base}" method="post">
          <input type="hidden" name="nonce" value="{$nonce}" />
          <input type="hidden" name="action" value="pointer" />
          <input type="hidden" id="liste_pointer_id" name="liste_pointer_id" value="" />
          <input type="button" id="depense_pointer_cmd" name="depense_pointer_cmd" value="Pointer" disabled="disabled" />
          <div id="pointer_solde">0.00 $</div>
        </form>
      </td>
    </tr>

    <tr>
      <td colspan="9"><br /><hr /><br /></td>
    </tr>




<!-- Formulaire d'ajout -->
    <tr id="ajouterdepense_row">
      <td colspan="9">
        <form id="depense_ajouter" name="depense_ajouter" action="{$url_base}" method="post">
          <input type="hidden" name="nonce" value="{$nonce}" />
          <input type="hidden" name="action" value="ajouterdepense" />
          Type : {include file="html_liste_type_transaction.tpl" id="" name="ajouter_type_transaction_id"
                                      defaultvalue=$ajouter_type_transaction_id disabled=False disablevalue=$type_transaction_reconciliation extra=" OnChange=\"DeactiveTransfertCompte('ajouter_type_transaction_id', 'transfert_compte')\" "}
            <div id="transfert_compte">transfert vers :<br />{include file="html_liste_compte.tpl"
                                  id="" name="transfert_compte_id" defaultvalue=$ajouter_transfert_compte_id disabled=False disablevalue=$compte_id extra=""}</div><br/>
          Date : <input type="text" name="ajouter_date_depense" title="Date" placeholder="Date" value="{$ajouter_date_depense|date_format:'%Y-%m-%d'}" onClick="GetDate(this);" /><br/>
          Type d&eacute;pense : {include file="html_liste_type_depense.tpl" id="" name="ajouter_type_depense_id" 
                                  defaultvalue=$ajouter_type_depense_id disabled=False extra=" OnChange=\"ChoisirSymbole('ajouter_type_depense_id', 'ajouter_symbole')\" "  }<br/>
          Description : <input type="text" id="ajouter_description" name="ajouter_description" title="Description" placeholder="Description" style="width:90%;" value="{$ajouter_description}" /><br/>
          Montant : {include file="html_liste_symbole.tpl" id="" name="ajouter_symbole" defaultvalue=$ajouter_symbole disabled=False extra=""}
                    <input type="number" step="any" name="ajouter_montant" title="Montant" placeholder="Montant" size="7" value="{$ajouter_montant}" /><br/>
          Notes : <textarea name="ajouter_notes" title="Notes" placeholder="Notes" rows="5" cols="46">{$ajouter_notes}</textarea><br/>
          <input type="button" name="soumettre" data-form="depense_ajouter" data-parametreurl="" value="Ajouter d&eacute;pense" />
      </form>
    </td>
    </tr>
    </table>
  {/if}


{include file="menu_pied.tpl"}
{include file="page_pied.tpl"}
