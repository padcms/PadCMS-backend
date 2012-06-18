{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block">

<div id="content-header">
    <div id="content-header-inner">
        <div class="breadcrumb">{$httpResponceCode} {$message|translate}</div>
        <br />
        Error #{$errorCode}
        <br/>
        {if $description}
            <div class="textarea-wrapper"><textarea rows="20" cols="80">{$description}</textarea></div>
        {/if}
    </div>
</div>

</div>
</div>

{include file="includes/footer.tpl"}