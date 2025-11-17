jQuery(function ($) {
  // Init color pickers
  $(".color-field").wpColorPicker();

  let roibotFrame = null;

  $(document).on("click", ".roibot-media", function (e) {
    // Prevent form submission / page reload
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    const $btn = $(this);
    const target = $btn.data("target"); // e.g., #roibot_avatar1
    const preview = $btn.data("preview"); // e.g., #roibot_avatar1_preview

    if (!wp || !wp.media) {
      alert("WordPress media library not available.");
      return false;
    }

    // Reuse a single frame for speed
    if (!roibotFrame) {
      roibotFrame = wp.media({
        title: "Select image",
        button: { text: "Use this image" },
        library: { type: "image" },
        multiple: false,
      });
    }

    roibotFrame.off("select").on("select", function () {
      const a = roibotFrame.state().get("selection").first().toJSON();
      if (target) {
        $(target).val(a.url).trigger("change");
      }
      if (preview) {
        $(preview).attr("src", a.url).show();
      }
    });

    roibotFrame.open();
    return false;
  });

  let idx = $("#roibot-news-table tbody tr").length;

  // "+ Add item" — ডুপ্লিকেট bind ঠেকাতে আগে off, পরে on (namespaced)
  $(document)
    .off("click.roibot", "#roibot-add-news")
    .on("click.roibot", "#roibot-add-news", function (e) {
      e.preventDefault();
      const tpl = $("#tmpl-roibot-news-row").html().replace(/{{index}}/g, idx++);
      $("#roibot-news-table tbody").append(tpl);
    });

  // "Remove" — নতুন অ্যাড হওয়া রোতেও কাজ করবে
  $(document)
    .off("click.roibot", ".link-delete")
    .on("click.roibot", ".link-delete", function (e) {
      e.preventDefault();
      $(this).closest("tr").remove();
    });
});
