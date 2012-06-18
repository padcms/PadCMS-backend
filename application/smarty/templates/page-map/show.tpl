{include file="page-map/includes/header.tpl"}

<div id="page" class="map-editor">
    <div id="page-inner">

        <script type="text/javascript">
            document.rid  = '{$rid}';
            document.root = '{$root}';

            window.root = '{$root}';
            window.rid  = '{$rid}';

            var is_admin = {$userInfo.is_admin} ? true : false;
        </script>

        <div style="display: none;" id="pageTemplate">
            <div class="page-wraper {$orientation}">
                <div class="page-outer {$orientation}">
                    <div class="page-body {$orientation}">
                        <div class="vert-expand"></div>
                        <div class="horiz-expand"></div>
                        <div class="page-inner {$orientation}"></div>
                        <div class="horiz-expand"></div>
                        <div class="vert-expand"></div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="popup-info">
                    <div class="top"></div>
                    <div class="mid">
                        <div class="title">
                            <span class="titre"></span>
                            <a class="ico trash" style="display: none"></a>
                        </div>
                        <div class="text">
                            <span class="cat-name"></span>
                            <span class="page-name"></span>
                        </div>
                    </div>
                    <div class="bot"></div>
                </div>
            </div>
        </div>

        <div class="top-panel">
            {include file=includes/breadcrumbs.tpl}
            <div id="wrappersize"></div>
        </div>

        <div class="map-panel">
            <div class="page-map-main">
                <div class="page-map-inner">
                    <div id="page-map-wrap-a" {if $panel_place == 'left'}class="page-map-toggle-class"{/if}>

                        <div id="slider-v" style="height:200px;"></div>

                        <div id="slider-h"></div>

                        <div id="page-map-wrap-b">
                            <div id="page-map-wrap-c">
                                <div id="page-map-wrapper">
                                    <table id="page-map" class="{$orientation}">
                                        <tbody>
                                            <tr>
                                                <td class="page expandable" background="{$rootItem.thumbnailUri}">
                                                    <div class="page-wraper {$orientation}">
                                                        <div class="page-outer {$orientation}">
                                                            <div class="page-body {$orientation}">
                                                                {if $rootItem.tpl.has_top}
                                                                    {if !$rootItem.has_top}
                                                                        <div class="vert-expand">
                                                                            <a class="add top" id="top-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {else}
                                                                        <div class="vert-expand vert-dots">
                                                                            <a class="expand top" id="top-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {/if}
                                                                {/if}

                                                                {if $rootItem.tpl.has_left}
                                                                    {if !$rootItem.has_left}
                                                                        <div class="horiz-expand">
                                                                            <a class="add left" id="left-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {else}
                                                                        <div class="horiz-expand horiz-dots">
                                                                            <a class="expand left" id="left-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {/if}
                                                                {/if}

                                                                <div class="page-inner {$orientation}" id="page-{$rootItem.id}">[{$rootItem.id}]</div>

                                                                {if $rootItem.tpl.has_right}
                                                                    {if !$rootItem.has_right}
                                                                        <div class="horiz-expand">
                                                                            <a class="add right" id="right-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {else}
                                                                        <div class="horiz-expand horiz-dots">
                                                                            <a class="expand right" id="right-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {/if}
                                                                {/if}

                                                                {if $rootItem.tpl.has_bottom}
                                                                    {if !$rootItem.has_bottom}
                                                                        <div class="vert-expand">
                                                                            <a class="add bottom" id="bottom-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {else}
                                                                        <div class="vert-expand vert-dots">
                                                                            <a class="expand bottom" id="bottom-{$rootItem.id}" href="#">#</a>
                                                                        </div>
                                                                    {/if}
                                                                {/if}

                                                                <div class="clear"></div>
                                                            </div>
                                                        </div> <!-- /.page-outer, /.page-body -->

                                                        <!-- PAGE INFO -->
                                                        <div class="popup-info">
                                                            <div class="top"></div>
                                                            <div class="mid">
                                                                <div class="title">
                                                                    <span class="titre">{$rootItem.tpl_title}</span>
                                                                </div>
                                                                <div class="text">
                                                                    <span class="cat-name"><b>#ID</b>: {$rootItem.id}</span>
                                                                    <span class="page-name"><b>{'Title'|translate}</b>: {$rootItem.title}</span>
                                                                </div>
                                                            </div>
                                                            <div class="bot"></div>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="page-editor-main side-panel {if $panel_place == 'left'}side-panel-left{/if}"></div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- /#page, /#page-inner -->

<div id="add-file-dialog">
    <div class="cont-popup">
        <div class="add-video">
            <span class="label">{'Current file' | translate}:
                <span class="current-file"></span>
                <a href="#" class="delete-file"></a>
            </span>

            <div class="form-item">
                <div class="status"></div>
                <div class="upload-btn">
                    <form action="/field/upload-extra" class="upload-form-extra" method="post">
                        <span class="file-wrapper">
                            <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                            <input type="file" name="resource" class="resource-extra"/>
                        </span>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="page-map/includes/toc-dialog.tpl"}

{include file="page-map/includes/select-pdf-page-dialog.tpl"}

{include file="page-map/includes/footer.tpl"}