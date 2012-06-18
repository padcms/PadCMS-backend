$(function() {
    $('h2.content-title').hover(over, out)
    $('div.cblock-clients').hover(function() {
        over.call($(this).prev('h2'));
    },
    function() {
        out.call($(this).prev('h2'));
    });
})

function out () {
    $(this).removeClass('content-title-d');
    $(this).addClass('content-title-a');
}

function over () {
    $(this).removeClass('content-title-a');
    $(this).addClass('content-title-d');
}