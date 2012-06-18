{include file="includes/header.tpl"}

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

                    {if $user.errors}
                    <div class="errors-block">
                        <div class="error-top"></div>
                        <div class="error-mid">
                            <div class="inner">
                                <h2 class="title">{'Errors'|translate}</h2>
                                <ul>
                                    {foreach from=$user.errors item=error}
                                    <li>{$error|escape:"html"}</li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <div class="error-bot"></div>
                    </div>
                    {/if}

                    <form method="POST" enctype="multipart/form-data" action="">
                        <input type="hidden" name="form" value="{$user.name}"/>

                        <div class="form-item">

                            <div class="form-item{if $login.errors} error{/if}">
                                <label>{$login.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$login _class="form-text"}</div>
                            </div>

                            <div class="form-item{if $first_name.errors} error{/if}">
                                <label>{$first_name.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$first_name _class="form-text"}</div>
                            </div>

                            <div class="form-item{if $last_name.errors} error{/if}">
                                <label>{$last_name.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$last_name _class="form-text"}</div>
                            </div>

                            <div class="form-item{if $email.errors} error{/if}">
                                <label>{$email.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$email _class="form-text"}</div>
                            </div>

                            <div class="form-item{if $password.errors} error{/if}">
                                <label>{$password.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$password _class="form-text" _type='password'}</div>
                            </div>


                            <div class="form-item{if $repeat_password.errors} error{/if}">
                                <label>{$repeat_password.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$repeat_password _class="form-text" _type='password'}</div>
                            </div>

                            <div class="block-clear cblock-buttons">
                                <input type="submit" class="orange-but" value="{'Save'|translate}" />
                            </div>

                        </div>
                    </form>

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
