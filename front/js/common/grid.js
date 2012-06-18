$(function() {
    // Initialize BS_Grid pager
    $('div.pager select, div.rpt-pager select').change(function(){
        window.location = 
            $('input', $(this).parent().parent().parent()).val()
            + $('option:selected', this).val();
    });
});