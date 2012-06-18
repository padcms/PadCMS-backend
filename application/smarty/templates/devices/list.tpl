{include file="includes/header.tpl"}

<div id="main">
<div id="main-inner" class="clear-block">

<div id="content-header">
    <div id="content-header-inner">
      <div class="breadcrumb">
          <a href="/client/list">{'Client list'|translate}</a> &gt; {'Devices'|translate}
      </div>
    </div>
</div>

<div id="content" class="rpt">
    <div id="content-inner">
        <div id="content-area">
            <div class="cblock-form cblock-signin cblock-add-app">
                
                <div class="cblock-form-inner">
                {* **************************************************** *}
                    <form action="" method="POST">
                        <input type="hidden" name="form" value="{$filter.name}"/>
                        <fieldset id="date-range" class="collapsible">
                        <legend onclick="toggleFieldset(this);">{'Filter'|translate}</legend>
                        <div>
                          <p>
                            <label>{$identifer.title}:</label>
                            {include file="Volcano/input.tpl" control=$identifer}
                          </p>
                          <p>
                            <label>{$linked.title}:</label>
                            {include file="Volcano/checkbox.tpl" control=$linked}
                          </p>
                        </div>
                        </fieldset>
                        
                        <div class="item-buttons">
                            <input type="submit" value="{'Apply'|translate}" class="filter-submit" name="submit">
                        </div>
                    </form>
                    {* **************************************************** *}
                    <div class="grid">
                        <div class="tb-main">
                            <table>
                                <tbody>
                                    <tr class="thead">
                                        <th colspan="5">
                                          <div class="tb-title">
                                              <div class="tb-title-inner">
                                                  <span class="left">{'Devices list'|translate}</span>
                                                  <span class="right"><a class="active" href="/devices/edit">{'Add device'|translate}</a></span>
                                               </div>
                                          </div>
                                        </th>
                                    </tr>
                                    <tr class="thead">
                                        <th>{include file="Volcano/sorter.tpl" grid=$grid name="created" text="Created"}</th>
                                        <th>{include file="Volcano/sorter.tpl" grid=$grid name="identifer" text="Identifer"}</th>
                                        <th>{include file="Volcano/sorter.tpl" grid=$grid name="user" text="User"}</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    {foreach from=$grid.rows item=item}
                                        <tr>
                                            <td class="name">{$item.created}</td>
                                            <td class="name">{$item.identifer}</td>
                                            <td class="name">{$item.user}</td>
                                            <td class="name"><a href="/devices/edit/id/{$item.id}">{'Edit'|translate}</a></td>
                                            <td class="name"><a href="/devices/delete/id/{$item.id}" class="cbutton-delete">{'Delete'|translate}</a></td>
                                        </tr>
                                    {foreachelse}
                                        {'There are no devices'|translate}
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                        {if $grid.rows}
                            {include file="includes/elements/pager_report.tpl" control=$pager}
                        {/if}
                    </div>
                {* **************************************************** *}
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
