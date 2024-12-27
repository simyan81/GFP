
{* Initialise quelques variables *}
  {if !isset($name)}
    {assign var="name" value=""}
  {/if}
  {if !isset($id)}
    {assign var="id" value=$name}
  {/if}
  {if !isset($title)}
    {assign var="title" value="Groupe"}
  {/if}
  {if !isset($placeholder)}
    {assign var="placeholder" value="Groupe"}
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
        {if $extra <> ""} {$extra} {/if}
        autocomplete="off">
  {foreach $liste_groupe as $id => $ligne_groupe}
    <option value="{$ligne_groupe.id}"
      {if $ligne_groupe.id == $defaultvalue} selected {/if}
      {if $disablevalue == $ligne_groupe.id}
        disabled="disabled"
      {/if}
    >{$ligne_groupe.description}
    </option>
  {/foreach}
</select>
