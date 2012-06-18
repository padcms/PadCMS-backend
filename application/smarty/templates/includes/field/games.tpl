<input type="hidden" name="field-id" value="{$field.fieldId}" />
{capture name=js}
    <script type="text/javascript">window.field_id = '{$field.fieldId}';</script>
    <script type="text/javascript">document.field_id = '{$field.fieldId}';</script>
{capture}

<h3 class="head">{'Games'|translate}</h3>

<div class="cont">
    <div id="edit-top-wrapper" class="form-item">
        <label>{'Game'|translate}</label>
        <div class="game-type">
            <select name="game_type" id="type-selector" onchange="fieldGames.typeSelected();">
                <option value='0'>Select game</option>
                {foreach from=$field.game_types key=key item=type}
                    <option value="{$key}" {if $key eq $field.current_game_type} selected {/if}>{$type}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div id="edit-top-wrapper" class="form-item edit-game-button">
        <a id="page-additional-data-btn" class="cbutton" href="#">
            <span>{'Edit'|translate}</span>
        </a>
    </div>
</div>
