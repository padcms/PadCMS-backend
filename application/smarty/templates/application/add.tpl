{include file="includes/header.tpl"}

{capture name=js}
<script type="text/javascript" src="/js/application/add.js"></script>
<script type="text/javascript" src="/js/lib/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    CKEDITOR.replace('message_for_readers');
    CKEDITOR.replace('long_intro');
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
            <div class="cblock-form cblock-signin cblock-add-app error-detected">
                <div class="cblock-form-mandatory">{'Mandatory fields'|translate} *</div>
                <div class="cblock-form-inner">

                    {if isset($application) && $application.errors}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Errors'|translate}</h2>
                                <ul>
                                    {foreach from=$application.errors item=error}
                                    <li>{$error|escape:"html"}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}

                    <form method="POST" enctype="multipart/form-data" action="">
                        <input type="hidden" name="form" value="{if isset($application)}{$application.name}{/if}"/>

                        <div class="form-item{if isset($title) && $title.errors} error{/if}">
                            <label>{if isset($title)}{$title.title|escape}{/if} <span>*</span></label>
                            <div class="form-item-wrapper">
                                {if isset($title) && empty($application.primaryKeyValue)}{include file="Volcano/input.tpl" control=$title _class="form-text"}
                                {elseif isset($title) && !empty($application.primaryKeyValue)}{include file="Volcano/input.tpl" control=$title _class="form-text"}{/if}
                            </div>
                            <div class="clr"></div>
                            <div class="description">
                                {'A short title for your application, used internally only. Example : '|translate}
                                <i>{'Wired :)'|translate}</i>
                            </div>
                        </div>

                        <div class="form-item select-themed{if isset($type) && $type.errors} error{/if}">
                          <label>{if isset($type)}{$type.title|escape}{/if} <span>*</span></label>
                          <div class="form-item-wrapper">
                              {if isset($type) && empty($application.primaryKeyValue)}{include file="Volcano/select.tpl" control=$type _values=$application.types _class="form-text"}
                              {elseif isset($type) && !empty($application.primaryKeyValue)}{include file="Volcano/select.tpl" control=$type _values=$application.types _class="form-text"}{/if}
                          </div>
                          <div class="clr"></div>
                          <div class="description">
                              {'Application type.'|translate}
                          </div>
                        </div>

                        {if isset($newsstand_cover_image)}
                            <div class="form-item{if isset($newsstand_cover_image) && $newsstand_cover_image.errors} error{/if}">
                                <label>{if isset($newsstand_cover_image)}{$newsstand_cover_image.title|escape}{/if}</label>

                                <div class="{if isset($application.imageUri)}form-item-image-wrapper{else}form-item-wrapper{/if}">
                                    {if isset($application.imageUri)}<img src="{$application.imageUri}">{/if}
                                    {if isset($newsstand_cover_image)}{include file="Volcano/input.tpl" control=$newsstand_cover_image _type="file" _class="form-text"}{/if}
                                </div>
                                <div class="clr"></div>
                            </div>
                        {/if}

                        {if !empty($application.primaryKeyValue)}
                        <div class="form-item{if isset($product_id) && $product_id.errors} error{/if}">
                            <label>{if isset($product_id)}{$product_id.title|escape}{/if}</label>
                            <div class="form-item-wrapper">{if isset($product_id)}{include file="Volcano/input.tpl" control=$product_id _class="form-text"}{/if}</div>
                            <div class="clr"></div>
                            <div class="description">
                                {'Product ID like '|translate} com.adyax.padcms.PRODUCT_NAME
                            </div>
                        </div>

                        <div class="form-item{if isset($previe) && $preview.errors} error{/if}">
                            <label>{if isset($preview)}{$preview.title|escape}{/if}</label>
                            <div class="form-item-wrapper">{if isset($preview)}{include file="Volcano/input.tpl" control=$preview _class="form-text"}{/if}</div>
                            <div class="clr"></div>
                            <div class="description">
                                {'Specify the number of pages, available for preview. If you specify 0, the preview function will be disabled.'|translate}
                            </div>
                        </div>

                        <div class="form-item{if isset($description) && $description.errors} error{/if}">
                            <label>{if isset($description)}{$description.title|escape}{/if} <span>*</span></label>
                            <div class="textarea-wrapper">{if isset($description)}{include file="Volcano/textarea.tpl" control=$description _rows=3 _cols=45 _class="form-textarea" _additional="title='"|cat:'Application description ...'|translate|cat:"'"}{/if}</div>
                            <div class="clr"></div>
                            <div class="description">
                                {'Very usefull if you have many applications and title is not clear enough. Example : '|translate}
                                <i>{'A nice magazine about life-style, decoration and cooking...'|translate}</i>
                            </div>
                        </div>

                        {if isset($long_intro)}
                            <div class="form-item{if isset($long_intro) && $long_intro.errors} error{/if}">
                                <label>{if isset($long_intro)}{$long_intro.title|escape}{/if}</label>
                                <div
                                     class="textarea-wrapper">{if isset($long_intro)}{include file="Volcano/textarea.tpl" control=$long_intro _class="form-textarea"}{/if}</div>
                                <div class="clr"></div>
                            </div>
                        {/if}

                        <div class="form-item{if isset($message_for_readers) && $message_for_readers.errors} error{/if}">
                            <label>{if isset($message_for_readers)}{$message_for_readers.title|escape}{/if}</label>
                            <div class="textarea-wrapper">{if isset($message_for_readers)}{include file="Volcano/textarea.tpl" control=$message_for_readers _rows=3 _cols=45 _class="form-textarea" _additional="title='"|cat:'For our readers ...'|translate|cat:"'"}{/if}</div>
                            <div class="clr"></div>
                        </div>
                        <div class="form-item{if isset($share_message) && $share_message.errors} error{/if}">
                            <label>{if isset($share_message)}{$share_message.title|escape}{/if}</label>
                            <div class="textarea-wrapper">{if isset($share_message)}{include file="Volcano/textarea.tpl" control=$share_message _rows=3 _cols=45 _class="form-textarea" _additional="title='"|cat:'Share message ...'|translate|cat:"'"}{/if}</div>
                            <div class="clr"></div>
                        </div>
                        <div class="form-item{if isset($application_notification_google) && $application_notification_google.errors} error{/if}">
                            <label>{if isset($application_notification_google)}{$application_notification_google.title|escape}{/if}</label>
                            <div class="textarea-wrapper">{if isset($application_notification_google)}{include file="Volcano/textarea.tpl" control=$application_notification_google _rows=3 _cols=45 _class="form-textarea" _additional="title='"|cat:'Notification Google ...'|translate|cat:"'"}{/if}</div>
                            <div class="clr"></div>
                        </div>
                        <div class="form-item{if isset($application_email) && $application_email.errors} error{/if}">
                            <label>{if isset($application_email)}{$application_email.title|translate|escape}{/if}</label>
                            <div class="form-item-wrapper">{if isset($application_email)}{include file="Volcano/input.tpl" control=$application_email _class="form-text"}{/if}</div>
                        </div>

                        <h2>{'Push notification settings'|translate}</h2>
                        <div id="push-tabs">
                            <ul>
                                <li><a href="#tab-1">{'Apple'|translate}</a></li>
                                <li><a href="#tab-2">{'Boxcar'|translate}</a></li>
                            </ul>
                            <div id="tab-1">
                                <h4>{'Apple notification'|translate}</h4>
                                <div class="form-item{if isset($push_apple_enabled) && $push_apple_enabled.errors} error{/if}">
                                    <label>{if isset($push_apple_enabled)}{$push_apple_enabled.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($push_apple_enabled)}{include file="Volcano/checkbox.tpl" control=$push_apple_enabled _class="form-text"}{/if}</div>
                                </div>
                            </div>
                            <div id="tab-2">
                                <h4>{'Boxcar notification'|translate}</h4>
                                <div class="form-item{if isset($push_boxcar_enabled) && $push_boxcar_enabled.errors} error{/if}">
                                    <label>{if isset($push_boxcar_enabled)}{$push_boxcar_enabled.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($push_boxcar_enabled)}{include file="Volcano/checkbox.tpl" control=$push_boxcar_enabled _class="form-text"}{/if}</div>
                                </div>
                                <div class="form-item{if isset($push_boxcar_provider_key) && $push_boxcar_provider_key.errors} error{/if}">
                                    <label>{if isset($push_boxcar_provider_key)}{$push_boxcar_provider_key.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($push_boxcar_provider_key)}{include file="Volcano/input.tpl" control=$push_boxcar_provider_key _class="form-text"}{/if}</div>
                                </div>
                                <div class="form-item{if isset($push_boxcar_provider_secret) && $push_boxcar_provider_secret.errors} error{/if}">
                                    <label>{if isset($push_boxcar_provider_secret)}{$push_boxcar_provider_secret.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($push_boxcar_provider_secret)}{include file="Volcano/input.tpl" control=$push_boxcar_provider_secret _class="form-text"}{/if}</div>
                                </div>
                                <div class="form-item{if isset($push_boxcar_name) && $push_boxcar_name.errors} error{/if}">
                                    <label>{if isset($push_boxcar_name)}{$push_boxcar_name.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($push_boxcar_name)}{include file="Volcano/input.tpl" control=$push_boxcar_name _class="form-text"}{/if}</div>
                                </div>
                                <div class="form-item{if isset($push_boxcar_icon) && $push_boxcar_icon.errors} error{/if}">
                                    <label>{if isset($push_boxcar_icon)}{$push_boxcar_icon.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($push_boxcar_icon)}{include file="Volcano/input.tpl" control=$push_boxcar_icon _class="form-text"}{/if}</div>
                                </div>
                            </div>
                        </div>
                        <h2>{'Notification settings'|translate}</h2>
                        <div id="share-tabs">
                            <ul>
                                <li><a href="#tab-3">iOS</a></li>
                                <li><a href="#tab-4">Android</a></li>
                            </ul>
                            <div id="tab-3">
                                <h4>{'Email notification for iOS'|translate}</h4>
                                <div class="form-item{if isset($nt_email_ios) && $nt_email_ios.errors} error{/if}">
                                    <label>{if isset($nt_email_ios)}{$nt_email_ios.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($nt_email_ios)}{include file="Volcano/input.tpl" control=$nt_email_ios _class="form-text"}{/if}</div>
                                </div>

                                <div class="form-item{if isset($nm_email_ios) && $nm_email_ios.errors} error{/if}">
                                    <label>{if isset($nm_email_ios)}{$nm_email_ios.title|translate|escape}{/if}</label>
                                    <div class="textarea-wrapper">{if isset($nm_email_ios)}{include file="Volcano/textarea.tpl" control=$nm_email_ios _rows=3 _cols=45 _class="form-textarea"}{/if}</div>
                                </div>

                                <h4>{'Twitter notification for iOS'|translate}</h4>
                                <div class="form-item{if isset($nm_twitter_ios) && $nm_twitter_ios.errors} error{/if}">
                                    <label>{if isset($nm_twitter_ios)}{$nm_twitter_ios.title|translate|escape}{/if}</label>
                                    <div class="textarea-wrapper">{if isset($nm_twitter_ios)}{include file="Volcano/textarea.tpl" control=$nm_twitter_ios _rows=3 _cols=45 _class="form-textarea"}{/if}</div>
                                </div>

                                <h4>{'Facebook notification for iOS'|translate}</h4>
                                <div class="form-item{if isset($nm_fbook_ios) && $nm_fbook_ios.errors} error{/if}">
                                    <label>{if isset($nm_fbook_ios)}{$nm_fbook_ios.title|translate|escape}{/if}</label>
                                    <div class="textarea-wrapper">{if isset($nm_fbook_ios)}{include file="Volcano/textarea.tpl" control=$nm_fbook_ios _rows=3 _cols=45 _class="form-textarea"}{/if}</div>
                                </div>
                            </div>
                            <div id="tab-4">
                                <h4>{'Email notification for Android'|translate}</h4>
                                <div class="form-item{if isset($nt_email_android) && $nt_email_android.errors} error{/if}">
                                    <label>{if isset($nt_email_android)}{$nt_email_android.title|translate|escape}{/if}</label>
                                    <div class="form-item-wrapper">{if isset($nt_email_android)}{include file="Volcano/input.tpl" control=$nt_email_android _class="form-text"}{/if}</div>
                                </div>
                                <div class="form-item{if isset($nm_email_android) && $nm_email_android.errors} error{/if}">
                                    <label>{if isset($nm_email_android)}{$nm_email_android.title|translate|escape}{/if}</label>
                                    <div class="textarea-wrapper">{if isset($nm_email_android)}{include file="Volcano/textarea.tpl" control=$nm_email_android _rows=3 _cols=45 _class="form-textarea"}{/if}</div>
                                </div>

                                <h4>{'Twitter notification for Android'|translate}</h4>
                                <div class="form-item{if isset($nm_twitter_android) && $nm_twitter_android.errors} error{/if}">
                                    <label>{if isset($nm_twitter_android)}{$nm_twitter_android.title|translate|escape}{/if}</label>
                                    <div class="textarea-wrapper">{if isset($nm_twitter_android)}{include file="Volcano/textarea.tpl" control=$nm_twitter_android _rows=3 _cols=45 _class="form-textarea"}{/if}</div>
                                </div>

                                <h4>{'Facebook notification for Android'|translate}</h4>
                                <div class="form-item{if isset($nm_fbook_android) && $nm_fbook_android.errors} error{/if}">
                                    <label>{if isset($nm_fbook_android)}{$nm_fbook_android.title|translate|escape}{/if}</label>
                                    <div class="textarea-wrapper">{if isset($nm_fbook_android)}{include file="Volcano/textarea.tpl" control=$nm_fbook_android _rows=3 _cols=45 _class="form-textarea"}{/if}</div>
                                </div>
                            </div>
                        </div>
                        {/if}
                        <div class="block-clear cblock-buttons">
                            <input type="submit" class="orange-but" value="{'Save'|translate}" />
                        </div>

                    </form>

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
