<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">{'Gallery'|translate}</h3>

<div class="cont">
    <div id="galleries-tabs">
        <ul>
        {foreach from=$field.galleries item=gallery key=gallery_id}
            <li><a href="#gallery-{$gallery_id}">{$gallery_id}</a></li>
        {/foreach}
        </ul>

        {foreach from=$field.galleries item=gallery key=gallery_id}
            <div id ="gallery-{$gallery_id}">
                <div id="edit-pic-widget-wrapper" class="form-item">
                    <label>{$field.allowedExtensions}</label>
                    <div class="upload-btn">
                        <form action="/field-gallery/upload" class="upload-form-gallery-{$gallery_id}" method="post">
                            <span class="file-wrapper">
                                <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                <input type="hidden" name="gallery" value="{$gallery_id}">
                                <input type="file" name="resource" class="resource-gallery-{$gallery_id}"/>
                            </span>
                        </form>
                    </div>
                    <span class="green"></span>
                    <span class="green bold"></span>
                    <div class="pic-grid">
                        <ul class="gallery gallery-{$gallery_id}">
                                {foreach from=$gallery item=element}
                                <li id="element-{$element.id}">
                                    <div class="data-item">

                                        <div class="picture">
                                            {if $element && $element.smallUri && $element.bigUri}
                                            <a class="single_image" href="{$element.bigUri}" rel="{$field.fieldTypeTitle}">
                                            <img alt="{$element.fileName}" src="{$element.smallUri}"/>
                                            </a>
                                            {else}
                                            <img alt="{$element.fileName}" src="{$element.smallUri}"/>
                                            {/if}
                                        </div>

                                        <span title="{$element.fileName}" class="name">{$element.fileNameShort}</span>

                                        <a class="close delete-btn" title="{'Delete image'|translate}" href="#"></a>

                                    </div>
                                </li>
                                {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
</div>