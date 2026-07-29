$(document).ready(function() {
    $(".popover-hover").popover({
        html: true
    });

    $(document).on("click", ".copy-to-clipboard", function() {
        copyToClipboard(this);
    });
});

async function copyToClipboard(el) {
    const type = "text/plain";
    const clipboardItemData = {
        [type]: $(el).text(),
    };
    const clipboardItem = new ClipboardItem(clipboardItemData);
    await navigator.clipboard.write([clipboardItem]);
    flash(el);
}

function flash(el) {
    el.style.transition = "none";
    el.style.backgroundColor = "#75c753";
    void el.offsetWidth;
    el.style.transition = "background-color 1500ms ease";
    el.style.backgroundColor = "transparent";
    el.addEventListener("transitionend", function handler() {
        el.style.transition = "";
        el.style.backgroundColor = "";
        el.removeEventListener("transitionend", handler);
    });
}
