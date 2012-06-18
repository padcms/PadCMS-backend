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
            <div class="cblock-form cblock-signin cblock-add-app">
                <div class="cblock-form-inner">

                    <table class="user-profile">
                        <tr><td class="right-col">{'Login:'|translate}</td><td>{$userProfile.login}</td></tr>
                        <tr><td class="right-col">{'First name:'|translate}</td><td> {$userProfile.first_name}</td></tr>
                        <tr><td class="right-col">{'Last name:'|translate}</td><td> {$userProfile.last_name}</td></tr>
                        <tr><td class="right-col">{'Email:'|translate}</td><td> {$userProfile.email}</td></tr>
                        <tr>
                        {if $userProfile.title}
                            <td class="right-col">{'Client name:'|translate}</td><td> {$userProfile.title}</td>
                        {else}
                            <td class="right-col">{'Status:'|translate}</td><td> Admin</td>
                        {/if}
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="content-foot">
    <div id="content-foot-inner">
    </div>
</div>

</div>
</div>

{include file="includes/footer.tpl"}
