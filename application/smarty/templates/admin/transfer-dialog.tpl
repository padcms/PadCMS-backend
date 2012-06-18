<div class="copy-move-dialog">
    <div class="selects">
        {if !$message}
        <div class="client-select">
            <div class="form-item select-themed">
                <label>{'Client'|translate}</label>
                <div class="form-item-wrapper">
                    <select id="client-select" onchange="clientsList.onChange()">
                        <option value="0">{'Nothing selected'|translate}</option>
                        {foreach from=$clients item=client}
                            <option {if $client.id == $clientId}selected="selected"{/if} value="{$client.id}">{$client.title}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class="user-select">
            <div class="form-item select-themed">
                <label>{'User'|translate}</label>
                <div class="form-item-wrapper">
                    <select id="user-select" onchange="usersList.onChange()">
                        <option value="0">{'Nothing selected'|translate}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="app-select">
            <div class="form-item select-themed">
                <label>{'Application'|translate}</label>
                <div class="form-item-wrapper">
                    <select id="app-select" onchange="applicationsList.onChange()">
                        <option value="0">{'Nothing selected'|translate}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="issue-select">
            <div class="form-item select-themed">
                <label>{'Issue'|translate}</label>
                <div class="form-item-wrapper">
                    <select id="issue-select" onchange="issuesList.onChange()">
                        <option value="0">{'Nothing selected'|translate}</option>
                    </select>
                </div>
            </div>
        </div>
        {else}
        <script type="text/javascript">
            document.transferError = '{$message.message}';
        </script>
        <div class="ui-widget">
            <div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">
                <p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
                    <strong>
                        {'There are no places to copy/move object'|translate}
                    </strong>
                </p>
            </div>
        </div>
        {/if}
    </div>
    <div class="buttons">
        <input type="button" class="orange-but" id="transfer-button" value="" />
        <input type="button" class="orange-but" id="cancel-button" value="{'Cancel'|translate}" />
    </div>
</div>