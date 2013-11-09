{capture name=css}

{/capture}

{include file="includes/header.tpl"}

{capture name=js}

{/capture}

<div id="main">
<div id="main-inner" class="clear-block">

<div id="content-header">
  <div id="content-header-inner">
    {include file=includes/breadcrumbs.tpl}
  </div>
</div>

<div id="content">
<div id="content-inner">
<div id="content-area">
  <div class="cblock-form cblock-signin cblock-add-app">
    <div class="cblock-form-mandatory">{'Mandatory fields'|translate} *</div>
    <div class="cblock-form-inner">

      {if isset($subscription) && $subscription.errors}
        <div class="errors-block">
          <div class="error-top"></div>
          <div class="error-mid">
            <div class="inner">
              <h2 class="title">{'Errors'|translate}</h2>
              <ul>
                {foreach from=$subscription.errors item=error}
                  <li>{$error|escape:"html"}</li>
                {/foreach}
              </ul>
            </div>
          </div>
          <div class="error-bot"></div>
        </div>
      {/if}

      <form method="POST" enctype="multipart/form-data" action="">
        <input type="hidden" name="form" value="{if isset($subscription)}{$subscription.name}{/if}"/>

        <div class="form-item{if isset($button_title) && $button_title.errors} error{/if}">
          <label>{if isset($button_title)}{$button_title.title|escape}{/if} <span>*</span></label>

          <div class="form-item-wrapper">
              {if isset($button_title)}{include file="Volcano/input.tpl" control=$button_title _class="form-text"}{/if}
          </div>
          <div class="clr"></div>
        </div>

        <div class="form-item{if isset($itunes_id) && $itunes_id.errors} error{/if}">
            <label>{if isset($itunes_id)}{$itunes_id.title|escape}{/if}</label>

            <div class="form-item-wrapper">
                {if isset($itunes_id)}{include file="Volcano/input.tpl" control=$itunes_id _class="form-text"}{/if}
            </div>
            <div class="clr"></div>
        </div>

        <div class="form-item{if isset($google_id) && $google_id.errors} error{/if}">
            <label>{if isset($google_id)}{$google_id.title|escape}{/if}</label>

            <div class="form-item-wrapper">
                {if isset($google_id)}{include file="Volcano/input.tpl" control=$google_id _class="form-text"}{/if}
            </div>
            <div class="clr"></div>
        </div>

          <div class="block-clear cblock-buttons">
            <input type="submit" class="orange-but" value="{'Save'|translate}"/>
          </div>

</form>

</div>
</div>
</div>
</div>
</div>

<div id="content-foot">
  <div id="content-foot-inner"></div>
</div>
</div>
</div>
{include file="includes/footer.tpl"}
