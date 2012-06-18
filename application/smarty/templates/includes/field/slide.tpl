<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">{'Slideshow'|translate}</h3>

<div class="cont">
    <div id="edit-pic-widget-wrapper" class="form-item">
        <label>{$field.allowedExtensions}</label>
        <div class="upload-btn">
            <form action="/field-slide/upload" class="upload-form-slide" method="post">
                <span class="file-wrapper">
                    <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                    <input type="file" name="resource" class="resource-slide"/>
                </span>
            </form>
        </div>
        <span class="green"></span>
        <span class="green bold"></span>
        <div class="pic-grid">
            <ul class="gallery">
                {if isset($field.elements)}
                    {foreach from=$field.elements item=element name=gallery}
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

                            <div class="actions">

                                <a class="{if $element.video}action-1{else}action-1-disabled{/if} add-video-btn"
                                href="#" title="{'Add video'|translate}" rel="{$element.video}"></a>

                            </div>

                            <a class="close delete-btn" title="{'Delete image'|translate}" href="#"></a>

                        </div>
                    </li>
                    {/foreach}
                {/if}
            </ul>
        </div>
    </div>
</div>