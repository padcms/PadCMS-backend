{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block">

<div id="main">
    <div id="main-inner">
        <div class="minH"></div>
        <div id="content">
            <div id="content-inner">
                <div class="block-center-content" style="float:none; margin: 0 auto;">
                    <h2>{'Adding new client'|translate}</h2>
                    <a href="/admin">{'Back to client list'|translate}</a>

                    <div class="block-contacter">
                        {if $client.errors}
                        <div class="block-errors-msgs">
                            <div class="bg-1">
                                <div class="bg-2">
                                    <h6>{'Error'|translate}</h6>
                                    <ul>
                                        {foreach from=$client.errors item=error}
                                        <li>{$error|escape:"html"}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {/if}

                        <form method="POST" enctype="multipart/form-data" action="">
                            <input type="hidden" name="form" value="{$client.name}"/>
                            <div class="form-item">
                                <label>{$title.title|escape} *</label>
                                <div class="div-input">{include file="Volcano/input.tpl" control=$title}</div>
                            </div>
                            <input type="submit" value="Add" class="form-submit" />
                        </form>
                    </div>
                </div>
                <div class="endcol"></div>
            </div>
        </div>
        <div class="endcol"></div>
    </div>
</div>

</div>
</div>

{include file="includes/footer.tpl"}
