{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block">

<div id="content-header">
    <div id="content-header-inner">
        <a href="/devices/list">{'Devices'|translate}</a> &gt;
        {if $device.primaryKeyValue}
            {'Edit device'|translate}
        {else}
            {'Add device'|translate}
        {/if}
    </div>
</div>

<div id="content">
    <div id="content-inner">
        <div id="content-area">
            <div class="cblock-form cblock-signin cblock-add-app">
                <div class="cblock-form-mandatory">{'Mandatory fields'|translate} *</div>
                <div class="cblock-form-inner">

                    {if $device.errors}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Errors'|translate}</h2>
                                <ul>
                                    {foreach from=$device.errors item=error}
                                    <li>{$error|escape:"html"}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}

                    <form method="POST" enctype="multipart/form-data" action="">
                        <input type="hidden" name="form" value="{$device.name}"/>

                        <div class="form-item{if $identifer.errors} error{/if}">
                            <label>{$identifer.title|translate|escape} <span>*</span></label>
                            <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$identifer _class="form-text"}</div>
                            <div class="clr"></div>
                            <div class="description">
                                {'Device identificator.'|translate}
                            </div>
                        </div>

                        <div class="form-item select-themed{if $user.errors} error{/if}">
                            <label>{$user.title|translate|escape}</label>
                            <div class="form-item-wrapper">{include file="Volcano/select.tpl" control=$user _values=$device.users _class="form-text"}</div>
                            <div class="clr"></div>
                            <div class="description">
                                {'User linked to this device.'|translate}
                            </div>
                        </div>

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
