<select name="{$control.name}" {if isset($_style)} style="{$_style|escape:"quotes"}"{/if}{if isset($_class)} class="{$_class}"{/if}{if isset($_readonly)} readonly{/if}{if isset($_disabled) and $_disabled == 1} disabled{/if}{if isset($_additional)}{$_additional}{/if}>
        {if isset($_addEmpty) && $_addEmpty}
        <option></option>
    {/if}

    {foreach from=$_values item=text key=value}
    <option value="{$value|escape:"quotes"}"{if (string) $value == (string) $control.value} selected="selected"{/if}>{$text|escape}</option>
    {/foreach}
</select>
