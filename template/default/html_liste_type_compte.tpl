
{* Initialise quelques variables *}
  {if !isset($name)}
    {assign var="name" value=""}
  {/if}
  {if !isset($id)}
    {assign var="id" value=$name}
  {/if}
  {if !isset($title)}
    {assign var="title" value="Type de compte"}
  {/if}
  {if !isset($placeholder)}
    {assign var="placeholder" value="Type de compte"}
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
  {if !isset($disablevalue2)}
    {assign var="disablevalue2" value=""}
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
  {foreach $liste_type_compte as $id => $ligne_type_compte}
    <option value="{$ligne_type_compte.id}"
      {if $ligne_type_compte.id == $defaultvalue} selected {/if}
      {if $disablevalue == $ligne_type_compte.id}
        disabled="disabled"
      {/if}
      {if $disablevalue2 == $ligne_type_compte.id}
        disabled="disabled"
      {/if}
    >{$ligne_type_compte.description}
    </option>
  {/foreach}
</select>
