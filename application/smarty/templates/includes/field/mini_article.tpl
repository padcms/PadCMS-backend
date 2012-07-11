<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">{'Mini article'|translate}</h3>

<div class="cont">
    <div id="edit-pic-widget-wrapper" class="form-item">
        <label>{$field.allowedExtensions}</label>
        <div class="upload-btn">
            <form action="/field-mini-art/upload" class="upload-form-mini-art" method="post">
                <span class="file-wrapper">
                    <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                    <input type="file" name="resource" class="resource-mini-art"/>
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

                                <a class="{if $element.thumbnail}action-2{else}action-2-disabled{/if} add-thumbnail-btn"
                                href="#" title="{'Add thumbnail'|translate}" rel="{$element.thumbnail}"></a>

                                <a class="{if $element.thumbnailSelected}action-2{else}action-2-disabled{/if} add-thumbnail-selected-btn"
                                href="#" title="{'Add selected thumbnail'|translate}" rel="{$element.thumbnailSelected}"></a>

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
