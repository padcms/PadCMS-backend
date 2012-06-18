
<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">Video</h3>
<div class="cont">
    <div id="edit-video-type-wrapper" class="form-item">
        <label>{'Video Type'|translate}</label>
        <div id="video-type-select" class="radio-checks">
            <span class="radio">
                <input type="radio" name="partner" {if !isset($field.element) || !$field.element.stream}checked{/if} value="file" onchange="fieldVideo.onChangeType('file'); return true;"/> {'File'|translate}
            </span>
            <span class="radio">
                <input type="radio" name="partner" {if isset($field.element) && $field.element.stream}checked{/if} value="stream" onchange="fieldVideo.onChangeType('stream');  return true;"  /> {'Stream'|translate}
            </span>
        </div>
    </div>

    <div id="video-type-file" class="video-type-video form-item" {if isset($field.element) && $field.element.stream}style="display:none;"{/if}>
        <div id="edit-pdf-wrapper" class="form-item">

            <div class="upload-pic">
                <label>{$field.allowedExtensions}</label>
                <div class="upload-btn">
                    <form action="/field-video/upload" class="upload-form-video" method="post">
                        <span class="file-wrapper">
                            <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                            <input type="file" name="resource" class="resource-video"/>
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

    <div id="video-type-stream" class="video-type-stream form-item" {if !isset($field.element) || !$field.element.stream}style="display:none;"{/if}>
        <div id="edit-width-wrapper" class="form-item">
            <label>{'Stream URL'|translate}</label>
            <div class="form-item-wrapper">
                <input type="text" class="form-text" value="{if isset($field.element)}{$field.element.stream}{/if}" />
            </div>
            <a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">Save</span></span></a>
        </div>
    </div>

</div>

