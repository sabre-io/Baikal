$(document).ready(function() {
    $(".popover-hover").popover({
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
