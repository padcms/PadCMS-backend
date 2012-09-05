<input type="hidden" name="field-id" value="{$field.fieldId}" />

<h3 class="head">{'HTML5'|translate}</h3>

<div class="cont">
    <div id="edit-top-wrapper" class="form-item">
        <label>{'Body'|translate}</label>
        <div class="game-type">
            <select name="html5_body" id="body-selector" onChange="fieldHtml5.bodySelected();">
                <option value=0>HTML5 ?</option>
                {foreach from=$field.select_body key=key item=body}
                    <option value="{$key}" {if isset($field.html5_body)}{if $key eq $field.html5_body} selected {/if}{/if}>{$body}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div id="edit-top-wrapper" class="form-item" style="height:auto;">
    <div id="html5-dialog-post-code-preview">wadsdsf</div>
        <ul style="list-style:none; padding:0px;" id="options">
            <li id="code">
                <div id="edit-pdf-wrapper" class="form-item">
                    <div class="upload-pic">
                        <label>{$field.allowedExtensions}</label>
                        <div class="upload-btn">
                            <form action="/field-html5/upload" class="upload-form-html5" method="post">
                                <span class="file-wrapper">
                                    <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                    <input type="file" name="resource" class="resource-html5"/>
                                </span>
                            </form>
                        </div>
                    </div>

                    <div class="data-item">
                        <div class="picture">
                            {if isset($field.smallUri)}
                            <img alt="Default image" src="{$field.smallUri}"/>
                            {else}
                            <img alt="Default image" src="{$field.defaultImageUri}"/>
                            {/if}
                        </div>

                        <span title="{if isset($field.element)}{$field.element.fileName}{/if}" class="name">{if isset($field.element)}{$field.element.fileNameShort}{/if}</span>

                        <a class="close" href="/field/delete/key/resource/element/{if isset($field.element)}{$field.element.id}{/if}" {if !isset($field.element) || !$field.element.smallUri}style="display:none;"{/if}></a>
                    </div>
                </div>
            </li>
            <li id="google_maps">
                <div id="edit-top-wrapper" class="form-item">
                    <label>{'Link to the map display'|translate}</label>
                    <div class="form-item-wrapper">
                        <input type="text" class="form-text" name="google_link_to_map" value="{if isset($field.google_link_to_map)}{$field.google_link_to_map}{/if}" />
                    </div>
                </div>
            </li>
            <li id="rss_feed">
                <div id="edit-top-wrapper" class="form-item">
                    <label>{'Feed link'|translate}</label>
                    <div class="form-item-wrapper">
                        <input type="text" class="form-text" name="rss_link" value="{if isset($field.rss_link)}{$field.rss_link}{/if}" />
                    </div>
                </div>
                <div id="edit-top-wrapper" class="form-item">
                    <label>{'Number of streams to be displayed'|translate}</label>
                    <div class="form-item-wrapper">
                        <input type="text" class="form-text" name="rss_link_number" value="{if isset($field.rss_link_number)}{$field.rss_link_number}{/if}" />

                    </div>
                </div>
            </li>
            <li id="facebook_like">
                <div id="edit-top-wrapper" class="form-item">
                    <label>{'Name / Page'|translate}</label>
                    <div class="form-item-wrapper">
                        <input type="text" class="form-text" name="facebook_name_page" value="{if isset($field.facebook_name_page)}{$field.facebook_name_page}{/if}" />
                    </div>
                </div>
            </li>
            <li id="twitter">
                <div id="edit-top-wrapper" class="form-item">
                    <label>{'Account'|translate}</label>
                    <div class="form-item-wrapper">
                        <input type="text" class="form-text" name="twitter_account" value="{if isset($field.twitter_account)}{$field.twitter_account}{/if}" />
                    </div>
                </div>
                <div id="edit-top-wrapper" class="form-item">
                    <label>{'Number of tweets to be displayed'|translate}</label>
                    <div class="form-item-wrapper">
                        <input type="text" class="form-text" name="twitter_tweet_number" value="{if isset($field.twitter_tweet_number)}{$field.twitter_tweet_number}{/if}" />
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div id="edit-top-wrapper" class="form-item">
        <a id="page-additional-data-btn" class="cbutton" href="#">
            <span>{'Save'|translate}</span>
        </a>
    </div>
</div>
