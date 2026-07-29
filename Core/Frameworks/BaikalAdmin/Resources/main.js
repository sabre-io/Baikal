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
      // TODO: Flash $(el) with CSS transitions
  }
}
