<script type="text/javascript">window.pid = '{$editor.pid}';</script>
<script type="text/javascript">document.pid = '{$editor.pid}';</script>

<div id="page-panel" class="page-panel">
    {include file="includes/editor/page.tpl" page=$page}
</div>

<div class="accordion-panel">
    <div id="accordion">
        {foreach from=$editor.fields item=item}
            <div id="field-{$item.type}">
                {php}
                  $aItem = $this->get_template_vars('item');
                  $sVar  = 'field_' . $aItem['type'];
                  $this->assign('fieldData', $this->get_template_vars($sVar));
                {/php}

                {include file="includes/field/`$item.type`.tpl" field=$fieldData template=$item.template_title}

            </div>
        {/foreach}
    </div>
</div>
