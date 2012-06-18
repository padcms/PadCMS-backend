
<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">{'Sound'|translate}</h3>

<div class="cont">

    <div id="edit-pdf-wrapper" class="form-item">

        <div class="upload-pic">
            <label>{$field.allowedExtensions}</label>
            <div class="upload-btn">
                <form action="/field-sound/upload" class="upload-form-sound" method="post">
                    <span class="file-wrapper">
                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                        <input type="file" name="resource" class="resource-sound"/>
                    </span>
                </form>
            </div>
        </div>

        <div class="data-item">

            <div class="picture">
                {if $field.element && $field.element.smallUri && $field.element.bigUri}
                <a class="single_image" href="{$field.element.bigUri}">
                   <img alt="{$field.element.fileName}" src="{$field.element.smallUri}"/>
                </a>
                {elseif $field.element && $field.element.smallUri}
                <img alt="Default image" src="{$field.element.smallUri}"/>
                {else}
                <img alt="Default image" src="{$field.defaultImageUri}"/>
                {/if}
            </div>

            <span title="{$field.element.fileName}" class="name">{$field.element.fileNameShort}</span>

            <a class="close" href="/field/delete/key/resource/element/{$field.element.id}" {if !$field.element.smallUri}style="display:none;"{/if}></a>

        </div>
    </div>

</div>
