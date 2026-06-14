$(document).ready(function() {
    $("[rel=tooltip]").tooltip();
    $(".popover-hover").popover({
        html: true
    });
    $(".popover-focus").popover({
        trigger: 'focus',
        html: true
    });
    $(".popover-focus-top").popover({
        trigger: 'focus',
        placement: 'top',
        html: true
    });
    $(".popover-focus-bottom").popover({
        trigger: 'focus',
        placement: 'bottom',
        html: true
    });
});

function copyToClipboard(el) {
    var range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
    document.execCommand("copy");
    sel.removeAllRanges();
    $(el).css({backgroundColor:"#75c753"});
    $(el).animate({backgroundColor:"transparent"}, 1500);
}
