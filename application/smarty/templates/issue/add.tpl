{capture name=css}
  <link href="/css/colorpicker.css" rel="stylesheet" media="screen" type="text/css" />
{/capture}

{include file="includes/header.tpl"}

{capture name=js}
  <script type="text/javascript" src="/js/lib/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="/js/issue/upload.js"></script>
  <script type="text/javascript" src="/js/colorpicker/colorpicker.js"></script>
  <script type="text/javascript" src="/js/colorpicker/color-handle.js"></script>
  <script type="text/javascript">
    window.issueId = '{if isset($issue)}{$issue.primaryKeyValue}{/if}';
    window.appId = '{if isset($issue)}{$issue.appId}{/if}';
    colorPickerHandler.init($('div.with-color-picker'));
    CKEDITOR.replaceAll();
  </script>
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

      {if isset($issue) && $issue.errors}
        <div class="errors-block">
          <div class="error-top"></div>
          <div class="error-mid">
            <div class="inner">
              <h2 class="title">{'Errors'|translate}</h2>
              <ul>
                {foreach from=$issue.errors item=error}
                  <li>{$error|escape:"html"}</li>
                {/foreach}
              </ul>
            </div>
          </div>
          <div class="error-bot"></div>
        </div>
      {/if}

      <form method="POST" enctype="multipart/form-data" action="">
        <input type="hidden" name="form" value="{if isset($issue)}{$issue.name}{/if}"/>

        <div class="form-item{if isset($title) && $title.errors} error{/if}">
          <label>{if isset($title)}{$title.title|escape}{/if} <span>*</span></label>

          <div class="form-item-wrapper">{if isset($title)}{include file="Volcano/input.tpl" control=$title _class="form-text"}{/if}</div>
          <div class="clr"></div>
          <div class="description">
            {'Current issue title, will be used on the device when user will try to browse available issues. Take care, this name may be seen by your customers.'|translate}
          </div>
        </div>

        {if isset($subtitle)}
          <div class="form-item{if isset($subtitle) && $subtitle.errors} error{/if}">
            <label>{if isset($subtitle)}{$subtitle.title|escape}{/if} <span>*</span></label>
            <div
                class="form-item-wrapper">{if isset($subtitle)}{include file="Volcano/input.tpl" control=$subtitle _class="form-text"}{/if}</div>
            <div class="clr"></div>
          </div>
        {/if}

        {if isset($image)}
          <div class="form-item{if isset($image) && $image.errors} error{/if}">
            <label>{if isset($image)}{$image.title|escape}{/if} <span>*</span></label>

            <div class="{if isset($issue.imageUri)}form-item-image-wrapper{else}form-item-wrapper{/if}">
              {if isset($issue.imageUri)}<img height="150" width="270" src="{$issue.imageUri}">{/if}
              {if isset($image)}{include file="Volcano/input.tpl" control=$image _type="file" _class="form-text"}{/if}
            </div>
            <div class="clr"></div>
          </div>
        {/if}

        {if isset($author)}
          <div class="form-item{if isset($author) && $author.errors} error{/if}">
            <label>{if isset($author)}{$author.title|escape}{/if} <span>*</span></label>
            <div class="form-item-wrapper">{if isset($author)}{include file="Volcano/input.tpl" control=$author _class="form-text"}{/if}</div>
            <div class="clr"></div>
          </div>
        {/if}

        {if isset($excerpt)}
          <div class="form-item{if isset($excerpt) && $excerpt.errors} error{/if}">
            <label>{if isset($excerpt)}{$excerpt.title|escape}{/if} <span>*</span></label>
            <div
                class="textarea-wrapper">{if isset($excerpt)}{include file="Volcano/textarea.tpl" control=$excerpt _class="form-textarea"}{/if}</div>
            <div class="clr"></div>
          </div>
        {/if}

        {if isset($welcome)}
          <div class="form-item{if isset($welcome) && $welcome.errors} error{/if}">
            <label>{if isset($welcome)}{$welcome.title|escape}{/if}</label>
            <div
                class="textarea-wrapper">{if isset($welcome)}{include file="Volcano/textarea.tpl" control=$welcome _class="form-textarea"}{/if}</div>
            <div class="clr"></div>
          </div>
        {/if}

        <div class="form-item{if isset($number) && $number.errors} error{/if}">
          <label>{if isset($number)}{$number.title|escape}{/if} <span>*</span></label>

          <div
              class="form-item-wrapper">{if isset($number)}{include file="Volcano/input.tpl" control=$number _class="form-text"}{/if}</div>
          <div class="clr"></div>
          <div class="description">
            {'Alternative to the title, not shown to the end user. May be used for internal purposes, if your print version magazines uses numbers.'|translate}
          </div>
        </div>

        {if isset($welcome)}
          <div class="form-item{if isset($words) && $words.errors} error{/if}">
            <label>{if isset($words)}{$words.title|escape}{/if} <span>*</span></label>
            <div
                class="form-item-wrapper">{if isset($words)}{include file="Volcano/input.tpl" control=$words _class="form-text"}{/if}</div>
            <div class="clr"></div>
        </div>
        {/if}

        <div class="form-item{if isset($product_id) && $product_id.errors} error{/if}">
          <label>{if isset($product_id)}{$product_id.title|escape}{/if}</label>

          <div
              class="form-item-wrapper">{if isset($product_id)}{include file="Volcano/input.tpl" control=$product_id _class="form-text"}{/if}</div>
          <div class="clr"></div>
          <div class="description">
            {'Product ID like '|translate}
            com.adyax.padcms.issue_{if isset($issue) && $issue.primaryKeyValue}{$issue.primaryKeyValue}{else}ID{/if}
          </div>
        </div>

        {if isset($issue) && $issue.states && $issue.primaryKeyValue}
          <div class="form-item select-themed{if isset($state) && $state.errors} error{/if}">
            <label>{if isset($state)}{$state.title|escape}{/if} <span>*</span></label>
            <div class="form-item-wrapper">{if isset($state)}{include file="Volcano/select.tpl" control=$state _values=$issue.states _class="form-text"}{/if}</div>
            <div class="clr"></div>
            <div class="description">
              {'Issue state.'|translate}
            </div>
          </div>
        {/if}

        <div class="form-item{if isset($issue_color) && $issue_color.errors} error{/if}">
          <label>{if isset($issue_color)}{$issue_color.title|escape}{/if}</label>

          <div class="form-item-wrapper with-color-picker">
            {if isset($issue_color)}
              {include file="Volcano/input.tpl" control=$issue_color id="color" _class="form-text cpicker_fld" _additional='style="width:292px;"'}
            {/if}
            <div class="color-selector">
              <div style="background-color: #{if isset($issue_color)}{$issue_color.value}{/if};"/>
            </div>
          </div>
        </div>
        <div class="clr"></div>
        <div class="description"></div>
    </div>

    <div class="form-item{if isset($summary_color) && $summary_color.errors} error{/if}">
      <label>{if isset($summary_color)}{$summary_color.title|escape}{/if}</label>

      <div class="form-item-wrapper with-color-picker">
        {if isset($summary_color)}
          {include file="Volcano/input.tpl" control=$summary_color id="summary_color" _class="form-text cpicker_fld" _additional='style="width:292px;"'}
        {/if}
        <div class="color-selector">
          <div style="background-color: #{if isset($summary_color)}{$summary_color.value}{/if};"/>
        </div>
      </div>
    </div>
    <div class="clr"></div>
    <div class="description"></div>
  </div>

  <div class="form-item{if isset($pastille_color) && $pastille_color.errors} error{/if}">
    <label>{if isset($pastille_color)}{$pastille_color.title|escape}{/if}</label>

    <div class="form-item-wrapper with-color-picker">
      {if isset($pastille_color)}
        {include file="Volcano/input.tpl" control=$pastille_color id="pastille_color" _class="form-text cpicker_fld" _additional='style="width:292px;"'}
      {/if}
      <div class="color-selector">
        <div style="background-color: #{if isset($pastille_color)}{$pastille_color.value}{/if};"/>
      </div>
    </div>
  </div>
  <div class="clr"></div>
  <div class="description"></div>
</div>

{if isset($issue) && $issue.orientations}
  <div class="form-item select-themed{if isset($orientation) && $orientation.errors} error{/if}">
    <label>{if isset($orientation)}{$orientation.title|escape}{/if} <span>*</span></label>
    {if isset($issue) && $issue.primaryKeyValue}
      {assign var="orientation_disbaled" value="1"}
    {else}
      {assign var="orientation_disbaled" value="0"}
    {/if}
    <div
        class="form-item-wrapper">{if isset($orientation)}{include file="Volcano/select.tpl" control=$orientation _values=$issue.orientations _class="form-text" _disabled=$orientation_disbaled}{/if}</div>
  </div>
{/if}

<div class="form-item">
  <label>{'Issue type'|translate}</label>

  <div class="form-item-wrapper" id="ctype">
    <div>
      <input type="radio" name="type" value="enriched" id="radio_type_enriched"
             {if isset($type)}{if $type.value == 'enriched' || !$type.value}checked="checked"{/if}{/if}>
      <label for="radio_type_enriched">{'Based on Multiple PDF'|translate}</label>
    </div>
    <div>
      <input type="radio" name="type" value="simple" id="radio_type_simple"
             {if isset($type) && $type.value == 'simple'}checked="checked"{/if}>
      <label for="radio_type_simple">{'Based on one PDF'|translate}</label>
    </div>
  </div>
  <div class="clr"></div>
  <div class="description">
    {'Type of the issue. If your application is based on a PDF of an existing magazine, use "Based on one PDF". If you make your own iPad magazine with new contents use "Base on multiple PDF", you will access directly to the editor page.'|translate}
  </div>
</div>

{if isset($orientation) && $orientation.value == 'vertical'}
  <div class="form-item">
    <label>{'Horizontal PDF'|translate}</label>

    <div class="form-item-wrapper" id="ctype">
      <div>
        <input type="radio" name="pdf_type" value="none" id="radio1"
               {if $pdf_type.value == 'none' || !$pdf_type.value}checked="checked"{/if}>
        <label for="radio1">{'None'|translate}</label>
      </div>
      <div>
        <input type="radio" name="pdf_type" value="issue" id="radio1"
               {if $pdf_type.value == 'issue'}checked="checked"{/if}>
        <label for="radio1">{'One PDF'|translate}</label>
      </div>
      <div>
        <input type="radio" name="pdf_type" value="page" id="radio2"
               {if $pdf_type.value == 'page'}checked="checked"{/if}>
        <label for="radio2">{'One PDF per page'|translate}</label>
      </div>
      <div>
        <input type="radio" name="pdf_type" value="2pages" id="radio3"
               {if $pdf_type.value == '2pages'}checked="checked"{/if}>
        <label for="radio3">{'Two screens PDF per page'|translate}</label>
      </div>
    </div>
    <div class="clr"></div>
    <div class="description">
      {"Type of the horizontal version PDF. If your application offers a horizontal view, this PDF will be used. Usually it's your print version as it is. Three possibilities are offered : unique PDF, per page PDFs and 2 pages PDFs."|translate}
    </div>
  </div>
{/if}

<div class="block-clear cblock-buttons">
  {if isset($issue) && $issue.primaryKeyValue}
  {if empty($issue.last_revision) }
  <a href="javascript:void(0)" class="cbutton cbutton-green" onclick="window.ui.popupMesage('there_are_no_revisions')">
    {else}
    <a href="/page-map/show/rid/{$issue.last_revision}" class="cbutton cbutton-green">
      {/if}
      <span><span class="ico">{'Go to page editor'|translate}</span></span>
    </a>
    {/if}
    <input type="submit" class="orange-but" value="{'Save'|translate}"/>
</div>

</form>

{if isset($issue) && $issue.primaryKeyValue && $type.value == 'simple'}
  <h2>{'Vertical PDF file'|translate}</h2>
  <div id="simple-pdf-wrapper" class="static-pdf-wrapper">
    <div class="upload-btn">
      <form action="/issue/upload-simple-pdf/iid/{if isset($issue)}{$issue.primaryKeyValue}{/if}"
            class="simple-pdf-upload-form" method="post">
                                    <span class="file-wrapper">
                                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                        <input type="file" name="simple-pdf-file" class="simple-pdf-file"/>
                                    </span>
      </form>
    </div>

    <div class="pic-grid">
      <ul class="list gallery simple-pdf-gallery">
        {if isset($issue) && $issue.simplePdf}
          <li id="simple-pdf">
            <div class="data-item">
              <a rel="undefined" href="{if isset($issue)}{$issue.simplePdf.bigUri}{/if}" class="simple_pdf_image">
                <img height="96" width="72" src="{if isset($issue)}{$issue.simplePdf.smallUri}{/if}"
                     alt="{if isset($issue)}{$issue.simplePdf.name}{/if}">
              </a>
              <span title="{if isset($issue)}{$issue.simplePdf.name}{/if}"
                    class="name">{if isset($issue)}{$issue.simplePdf.nameShort}{/if}</span>
              <a class="close delete-simple-pdf-btn" title="Delete image" href="#"></a>
            </div>
          </li>
        {/if}
      </ul>
    </div>
  </div>
{/if}

{if isset($issue) && $issue.primaryKeyValue && $pdf_type.value != 'none' && $pdf_type.value != null}
  <h2>{'Horizontal PDF files'|translate}</h2>
  <div id="static-pdf-wrapper" class="static-pdf-wrapper">
    <div class="upload-btn">
      <form action="/issue/upload/iid/{if isset($issue)}{$issue.primaryKeyValue}{/if}" class="upload-form"
            method="post">
                                    <span class="file-wrapper">
                                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                        <input type="file" name="pdf-file" class="pdf-file"/>
                                    </span>
      </form>
    </div>
    <div class="upload-btn download"{if $issue.pdf|@count == 0} style="display:none;"{/if}>
      <a class="cbutton" href="/issue/download/iid/{$issue.primaryKeyValue}/aid/{$appId}">
        <span><span class="ico" style="font-size: 12px;">{'Download'|translate}</span></span>
      </a>
    </div>

    <div class="pic-grid">
      <ul class="list gallery horizontal-pdf-gallery">
        {foreach from=$issue.pdf item=file}
          <li id="static-pdf-{$file.id}">
            <div class="data-item">
              <a rel="undefined" href="{$file.bigUri}" class="single_image">
                <img height="96" width="72" src="{$file.smallUri}" alt="{$file.name}">
              </a>
              <span title="{$file.name}" class="name">{$file.nameShort}</span>
              <a class="close delete-btn" title="Delete image" href="#"></a>
            </div>
          </li>
        {/foreach}
      </ul>
    </div>
  </div>
{/if}

<!-- Vertical help page -->
{if isset($issue) && $issue.primaryKeyValue && isset($orientation) && $orientation.value == 'vertical'}
  <h2>{'Vertical help page'|translate}</h2>
  <div id="vertical-help-page-wrapper" class="static-pdf-wrapper">
    <div class="upload-btn">
      <form action="/issue/upload-help-page/iid/{$issue.primaryKeyValue}/type/vertical"
            class="vertical-help-page-upload-form" method="post">
                                    <span class="file-wrapper">
                                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                        <input type="file" name="vertical-help-page-file"
                                               class="vertical-help-page-file"/>
                                    </span>
      </form>
    </div>

    <div class="pic-grid">
      <ul class="list gallery vertical-help-page-gallery">
        {if $issue.verticalHelpPage}
          <li id="vertical-help-page">
            <div class="data-item">
              <a rel="undefined" href="{$issue.verticalHelpPage.bigUri}" class="vertical-help-page-image">
                <img height="96" width="72" src="{$issue.verticalHelpPage.smallUri}"
                     alt="{$issue.verticalHelpPage.name}">
              </a>
              <span title="{$issue.verticalHelpPage.name}" class="name">{$issue.verticalHelpPage.nameShort}</span>
              <a class="close delete-vertical-help-page-btn" title="Delete image" href="#"></a>
            </div>
          </li>
        {/if}
      </ul>
    </div>
  </div>
{/if}
<!-- Horizontal help page -->
{if isset($issue) && $issue.primaryKeyValue}
  <h2>{'Horizontal help page'|translate}</h2>
  <div id="horizontal-help-page-wrapper" class="static-pdf-wrapper">
    <div class="upload-btn">
      <form action="/issue/upload-help-page/iid/{$issue.primaryKeyValue}/type/horizontal"
            class="horizontal-help-page-upload-form" method="post">
                                    <span class="file-wrapper">
                                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                        <input type="file" name="horizontal-help-page-file"
                                               class="horizontal-help-page-file"/>
                                    </span>
      </form>
    </div>

    <div class="pic-grid">
      <ul class="list gallery horizontal-help-page-gallery">
        {if $issue.horizontalHelpPage}
          <li id="horizontal-help-page">
            <div class="data-item">
              <a rel="undefined" href="{$issue.horizontalHelpPage.bigUri}" class="horizontal-help-page-image">
                <img height="72" width="96" src="{$issue.horizontalHelpPage.smallUri}"
                     alt="{$issue.horizontalHelpPage.name}">
              </a>
              <span title="{$issue.horizontalHelpPage.name}" class="name">{$issue.horizontalHelpPage.nameShort}</span>
              <a class="close delete-horizontal-help-page-btn" title="Delete image" href="#"></a>
            </div>
          </li>
        {/if}
      </ul>
    </div>
  </div>
{/if}
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
