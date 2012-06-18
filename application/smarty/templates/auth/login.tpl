{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block">

<div class="clear-block" id="main-inner">

    <div id="content-header">
        <div id="content-header-inner">
            <div class="breadcrumb">
                {'Sign In'|translate}
            </div>
        </div>
    </div>


    <div id="content">
        <div id="content-inner">
            <div id="content-area">
                <div class="cblock-form cblock-signin error-detected">
                    <div class="cblock-form-mandatory">{'Mandatory fields'|translate} *</div>
                    <div class="cblock-form-inner">

                        {if $component.errors}
                        <div class="errors-block">
                            <div class="error-top"></div>
                            <div class="error-mid">
                                <div class="inner">
                                    <h2 class="title">{'Errors'|translate}</h2>
                                    <ul>
                                        {foreach from=$component.errors item=error}
                                        <li>{$error|escape:"html"}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                            <div class="error-bot"></div>
                        </div>
                        {/if}

                        <form method="post" id="siteAddForm" enctype="multipart/form-data" action="">
                            <input type="hidden" name="form" value="{$component.name}"/>

                            <div class="form-item">
                                <label>{$login.title|translate} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$login _class="form-text"}</div>
                            </div>

                            <div class="form-item">
                                <label>{$password.title|escape} <span>*</span></label>
                                <div class="form-item-wrapper">{include file="Volcano/input.tpl" control=$password _class="form-text" _type="password"}</div>
                            </div>

                            <div class="block-clear cblock-buttons">
                                <input type="submit" class="orange-but" value="Sign In" />
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
</div>

{include file="includes/footer.tpl"}
