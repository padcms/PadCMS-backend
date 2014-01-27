<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">Video</h3>
<div class="cont">
    <div id="edit-video-type-wrapper" class="form-item">
        <label>{'Video Type'|translate}</label>
        <div id="video-type-select" class="radio-checks">
            <span class="radio">
                <input type="radio" name="partner" {if !isset($field.elements) || !$field.isStream}checked{/if} value="file" onchange="fieldVideo.onChangeType('file'); return true;"/> {'File'|translate}
            </span>
            <span class="radio">
                <input type="radio" name="partner" {if isset($field.elements) && $field.isStream}checked{/if} value="stream" onchange="fieldVideo.onChangeType('stream');  return true;"  /> {'Stream'|translate}
            </span>
        </div>
    </div>

    <div id="video-type-file" class="video-type-video form-item" {if isset($field.elements) && $field.isStream}style="display:none;"{/if}>
        <div id="edit-pic-widget-wrapper" class="form-item">
                <label>{$field.allowedExtensions}</label>
                <div class="upload-btn">
                    <form action="/field-video/upload" class="upload-form-video" method="post">
                        <span class="file-wrapper">
                            <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                            <input type="file" name="resource" class="resource-video"/>
                        </span>
                    </form>
                </div>

            {*<div class="data-item">*}

                {*<div class="picture">*}
                    {*{if isset($field.element) && $field.element.smallUri && $field.element.bigUri}*}
                    {*<a class="single_image" href="{$field.element.bigUri}">*}
                       {*<img alt="{$field.element.fileName}" src="{$field.element.smallUri}"/>*}
                    {*</a>*}
                    {*{elseif isset($field.element) && $field.element.smallUri}*}
                    {*<img alt="Default image" src="{$field.element.smallUri}"/>*}
                    {*{else}*}
                    {*<img alt="Default image" src="{$field.defaultImageUri}"/>*}
                    {*{/if}*}
                {*</div>*}

                {*<span title="{if isset($field.element)}{$field.element.fileName}{/if}" class="name">{if isset($field.element)}{$field.element.fileNameShort}{/if}</span>*}

                {*<a class="close" href="/field/delete/key/resource/element/{if isset($field.element)}{$field.element.id}{/if}" {if !isset($field.element) || !$field.element.smallUri}style="display:none;"{/if}></a>*}

            {*</div>*}

            <span class="green"></span>
            <span class="green bold"></span>
            <div class="pic-grid">
                <ul class="gallery">
                    {if isset($field.elements)}
                        {foreach from=$field.elements item=element name=video}
                            {if !$element.stream}
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

                                        <div class="actions">
                                            <a class="{if $element.loop}action-2{else}action-2-disabled{/if} enable-loop-btn"
                                               href="#" title="{'Enable loop'|translate}"></a>
                                            <a class="{if $element.ui}action-2{else}action-2-disabled{/if} disable-ui-btn"
                                               href="#" title="{'Disable UI'|translate}"></a>
                                        </div>

                                        <span title="{$element.fileName}" class="name">{$element.fileNameShort}</span>

                                        <a class="close delete-btn" title="{'Delete video'|translate}" href="#"></a>

                                    </div>
                                </li>
                            {/if}
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    </div>

    <div id="video-type-stream" class="video-type-stream form-item" {if !isset($field.elements) || !$field.isStream}style="display:none;"{/if}>
        <label>{'Stream URL'|translate}</label>
        <div class="clear"></div>
        <div id="edit-width-wrapper" class="form-item new-stream-item">
            <div class="form-item-wrapper stream-url-wrapper">
                <div class="sort-weight"></div>
                <input id="0" type="text" class="form-text new-stream" value="" />
            </div>
            <a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">Save</span></span></a>
            <div class="clear"></div>
        </div>
        <ul class="stream-sort">
            {foreach from=$field.elements item=element name=video}
            {if $element.stream}
                <li id="stream-{$element.id}">
                    <div id="edit-width-wrapper" class="form-item stream-item">
                        <div class="form-item-wrapper stream-url-wrapper">
                            <div class="sort-weight"></div>
                            <input id="{$element.id}" type="text" class="form-text" value="{if $element.stream}{$element.stream}{/if}" />
                        </div>
                        <a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">Save</span></span></a>
                        <a class="close delete-btn" title="{'Delete video'|translate}" href="#"></a>
                        <div class="clear"></div>
                        <div class="actions">
                            <a class="{if $element.loop}action-2{else}action-2-disabled{/if} enable-loop-btn"
                               href="#" title="{'Enable loop'|translate}"></a>
                            <a class="{if $element.ui}action-2{else}action-2-disabled{/if} disable-ui-btn"
                               href="#" title="{'Disable UI'|translate}"></a>
                        </div>
                    </div>
                </li>
            {/if}
            {/foreach}
        </ul>
    </div>
</div>

