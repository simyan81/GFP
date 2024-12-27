{include file="page_entete.tpl"}
<!-- Aucun menu sur cette page -->


  {if $erreur != ''}
    <font class="couleur_message_erreur">{$erreur}</font>
  {/if}

  <form action="{$url_base}&page=login" method="post">
    <input type="hidden" name="nonce" value="{$nonce}" />
    Nom d'utilisateur : <input type="text" name="utilisateur" value="{$utilisateur}" /><br />
    Mot de passe : <input type="password" name="motdepasse" value="" /><br />
    <input type="submit" value="Login" />
  </form>


<!-- Aucun menu sur cette page -->
{include file="page_pied.tpl"}
