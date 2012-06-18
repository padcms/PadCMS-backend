
<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">HTML</h3>
<div class="cont">
    <!-- Template type (rotation || touch) -->
    <div id="edit-template-type-wrapper" class="form-item">
        <label>{'Template Type'|translate}</label>
        <div id="template-type-select" class="radio-checks">
            <span class="radio">
                <input type="radio" name="template-type" {if !isset($field.element) || !$field.element.touch || !$field.element}checked{/if} value="rotation" onchange="fieldHtml.onChangeTemplateType('rotation'); return true;"/> {'Rotation'|translate}
            </span>
            <span class="radio">
                <input type="radio" name="template-type" {if isset($field.element) && $field.element.touch}checked{/if} value="touch" onchange="fieldHtml.onChangeTemplateType('touch');  return true;"  /> {'Touch'|translate}
            </span>
        </div>
    </div>

    <!-- HTML archive loader -->
    <div id="edit-html-type-wrapper" class="form-item">
        <label>{'HTML Page Type'|translate}</label>
        <div id="html-type-select" class="radio-checks">
            <span class="radio">
                <input type="radio" name="html-type" {if !isset($field.element) || !$field.element.html_url || !$field.element}checked{/if} value="html_archive" onchange="fieldHtml.onChangeType('html_archive'); return true;"/> {'File'|translate}
            </span>
            <span class="radio">
                <input type="radio" name="html-type" {if isset($field.element) && $field.element.html_url}checked{/if} value="html_url" onchange="fieldHtml.onChangeType('html_url');  return true;"  /> {'URL'|translate}
            </span>
        </div>
    </div>

    <div id="html-type-file" class="html-type-file form-item" {if isset($field.element) && $field.element.html_url}style="display:none;"{/if}>
        <div id="edit-pdf-wrapper" class="form-item">

            <div class="upload-pic">
                <label>{$field.allowedExtensions}</label>
                <div class="upload-btn">
                    <form action="/field-html/upload" class="upload-form-html" method="post">
                        <span class="file-wrapper">
                            <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                            <input type="file" name="resource" class="resource-html"/>
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
    </div>

    <div id="html-type-url" class="html-type-url form-item" {if !isset($field.element) || !$field.element.html_url}style="display:none;"{/if}>
        <div id="edit-width-wrapper" class="form-item">
            <label>{'URL'|translate}</label>
            <div class="form-item-wrapper">
                <input type="text" class="form-text" value="{if isset($field.element)}{$field.element.html_url}{/if}" />
            </div>
            <a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">Save</span></span></a>
        </div>
    </div>

</div>

