
{* Initialise quelques variables *}
  {if !isset($name)}
    {assign var="name" value=""}
  {/if}
  {if !isset($id)}
    {assign var="id" value=$name}
  {/if}
  {if !isset($title)}
    {assign var="title" value="Symbole"}
  {/if}
  {if !isset($placeholder)}
    {assign var="placeholder" value="Symbole"}
  {/if}
  {if !isset($disabled)}
    {assign var="disabled" value=False}
  {/if}

  {if !isset($defaultvalue)}
    {assign var="defaultvalue" value=""}
  {/if}
  {if !isset($disablevalue)}
    {assign var="disablevalue" value=""}
  {/if}
  {if !isset($extra)}
    {assign var="extra" value=""}
  {/if}

  {if $id==""}
    {assign var="id" value=$name}
  {/if}

  {if $name<>""}
    {assign var="name" value="name='{$name}'"}
  {/if}
  {if $id<>""}
    {assign var="id" value="id='{$id}'"}
  {/if}
  {if $title<>""}
    {assign var="title" value="title='{$title}'"}
  {/if}
  {if $placeholder<>""}
    {assign var="placeholder" value="placeholder='{$placeholder}'"}
  {/if}
{* Fin de l'initialisation de quelques variables *}

{if $disabled == True}
  <input type="hidden" {$id} {$name} {$title} {$placeholder} value="{$defaultvalue}" />
  {assign var="name" value=$name|cat:"_disabled"}
{/if}

<select {$id} {$name} {$title} {$placeholder}
        {if $disabled == True} disabled="disabled" {/if}
        autocomplete="off">
  {foreach $liste_symbole as $lid => $ligne_symbole}
    <option value="{$lid}"
      {if $lid == $defaultvalue} selected {/if}
    >{$ligne_symbole}
    </option>
  {/foreach}
</select>
