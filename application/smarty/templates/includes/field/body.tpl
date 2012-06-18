
<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">{'Body'|translate}</h3>

<div class="cont">

    <div id="edit-pdf-wrapper" class="form-item">

        <div class="upload-pic">
            <label>{$field.allowedExtensions}</label>
            <div class="upload-btn">
                <form action="/field-body/upload" class="upload-form-body" method="post">
                    <span class="file-wrapper">
                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                        <input type="file" name="resource" class="resource-body"/>
                    </span>
                </form>
            </div>
        </div>

        <div class="data-item">

            <div class="picture">
                {if isset($field.element) && $field.element.smallUri && $field.element.bigUri}
                    <a class="single_image" href="{$field.element.bigUri}">
                    <img alt="{$field.element.fileName}" src="{$field.element.smallUri}"/>
                    </a>
                {elseif isset($field.element) && $field.element.smallUri}
                    <img alt="Default image" src="{$field.element.smallUri}"/>
                {else}
                    <img alt="Default image" src="{$field.defaultImageUri}"/>
                {/if}
            </div>

            <span title="{if isset($field.element)}{$field.element.fileName}{/if}" class="name">{if isset($field.element)}{$field.element.fileNameShort}{/if}</span>

            <a class="close" href="/field/delete/key/resource/element/{if isset($field.element)}{$field.element.id}{/if}" {if !isset($field.element) || !$field.element.smallUri}style="display:none;"{/if}></a>

        </div>

    </div>

    {if $template != 'fixed_illustration_article'}
    <div id="edit-top-wrapper" class="form-item">
        <label>{'Top position'|translate}</label>
        <div class="form-item-wrapper">
            <input type="text" class="form-text" value="{if isset($field.element)}{$field.element.top}{/if}" />
        </div>
        <a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
    </div>

    {*<div id="edit-width-wrapper" class="form-item">
        <label>{'Width'|translate}</label>
        <div class="form-item-wrapper">
            <input type="text" class="form-text" value="{$field.element.width}" />
        </div>
        <a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
    </div>*}

    {*<div id="edit-align-wrapper" class="form-item">
        <label>{'Alignment'|translate}</label>
        <select onchange="fieldBody.onChangeAlign(this);">
            <option value="left"  {if $field.element.alignment == 'left'}selected="selected"{/if}>{'Left'|translate}</option>
            <option value="right" {if $field.element.alignment == 'right'}selected="selected"{/if}>{'Right'|translate}</option>
        </select>
    </div>*}

    {/if}

    <div id="edit-has-photo-wrapper" class="form-item">
        <label>{'Has photo gallery link'|translate}</label>
        <div class="checks">
            <input type="checkbox" onchange="fieldBody.onChangeHasPhotoGallery(this);" name="partner" value="" {if isset($field.element) && $field.element.hasPhotoGalleryLink}checked="checked"{/if} />
        </div>
    </div>

    {if $template == 'fixed_illustration_article_touchable'}
    <div id="edit-has-photo-wrapper" class="form-item">
        <label>{'Show top layer'|translate}</label>
        <div class="checks">
            <input type="checkbox" onchange="fieldBody.onChangeShowTopLayer(this);" name="partner" value="" {if isset($field.element) && $field.element.showTopLayer}checked="checked"{/if} />
        </div>
    </div>
    {/if}

</div>