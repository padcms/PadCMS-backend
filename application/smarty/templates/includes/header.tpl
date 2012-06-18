<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <title>{'PadCMS'|translate} - {$controller}</title>

        <link rel="icon" type="image/png" href="{$baseUrl}/favicon.gif" />

        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

        <link href="{$baseUrl}/css/html-elements.css" type="text/css" rel="stylesheet" />
        <link href="{$baseUrl}/css/style.css" type="text/css" rel="stylesheet" />
        <link href="{$baseUrl}/css/custom.css" type="text/css" rel="stylesheet" />

        <link href="{$baseUrl}/js/lib/jquery/jquery-ui/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" />
        <link href="{$baseUrl}/js/lib/jquery/select-box/jquery.selectBox.css" rel="stylesheet" type="text/css" />
        <link href="{$baseUrl}/js/lib/jquery/fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
        <link href="{$baseUrl}/js/lib/jquery/jstree/themes/default/style.css" rel="stylesheet" type="text/css" />
        <link href="{$baseUrl}/css/layout.css" rel="stylesheet" media="screen" type="text/css" />
        <link href="{$baseUrl}/css/colorpicker.css" rel="stylesheet" media="screen" type="text/css" />

        {if isset($smarty.capture.css)}{$smarty.capture.css}{/if}

        {literal}
        <!--[if !IE 7]>
        <style type="text/css">
        #wrap {display:table;height:100%}
        </style>
        <![endif]-->
        {/literal}

    </head>
    <body>
        <div id="page" {if $controller == 'auth' || $controller == 'devices' || $action == 'add' || $action == 'download'}class="page-simple"{/if}>
             <div id="page-inner">
                <!-- header -->
                <div id="header">
                    <div id="header-inner" class="clear-block">
                        <!-- #header-blocks -->
                        <div id="header-blocks">
                            {if $userInfo.client}
                            <span class="name">
                                <span>{$userInfo.client_title}</span>
                            </span>
                            {/if}

                            {if $userInfo}
                            <div class="header-links">
                                    {if $userInfo.is_admin}
                                        <ul>
                                            <li class="client-mike">{$userInfo.first_name} {$userInfo.last_name}</li>
                                            <li><a href="/devices">{'Devices'|translate}</a></li>
                                            <li class="last"><a href="/auth/logout">{'Logout'|translate}</a></li>
                                        </ul>
                                    {else}
                                        <ul>
                                            <li class="client-mike">{$userInfo.first_name} {$userInfo.last_name}</li>
                                            <li class="last"><a href="/auth/logout">{'Logout'|translate}</a></li>
                                        </ul>
                                    {/if}
                            </div>
                            {/if}
                        </div>
                        <!-- /#header-blocks -->
                    </div>
                </div> <!-- /#header-inner, /#header -->
