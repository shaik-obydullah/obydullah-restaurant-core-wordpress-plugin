/**
 * About Page Admin Script
 *
 * @package ObydullahRestaurantCore
 * @since   1.0.0
 * @author  Shaik Obydullah
 *
 * Handles the about page slider repeater:
 * - Add new slide rows
 * - Remove slide rows
 * - WordPress media uploader for images
 * - Toggle remove button visibility via CSS class
 */

jQuery(document).ready(function ($) {
  var slideCounter = parseInt($("#obirc-about-slide-count").val()) || 0;

  $("#obirc-add-about-slide").on("click", function () {
    var newIndex = slideCounter;
    var newRow = `
      <div class="obirc-slide-row" data-index="${newIndex}">
        <p>
          <label>${obircAboutL10n.titlePlaceholder}</label><br>
          <input type="text" name="obirc_about_slides[${newIndex}][title]" class="widefat">
        </p>
        <p>
          <label>${obircAboutL10n.subtitlePlaceholder}</label><br>
          <input type="text" name="obirc_about_slides[${newIndex}][subtitle]" class="widefat">
        </p>
        <div class="slide-image-wrapper">
          <label>${obircAboutL10n.imagePlaceholder}</label><br>
          <input type="hidden" name="obirc_about_slides[${newIndex}][image]" class="slide-image-url">
          <div class="image-preview"></div>
          <button type="button" class="button select-slide-image">${obircAboutL10n.selectImage}</button>
          <button type="button" class="button remove-slide-image hidden">${obircAboutL10n.removeImage}</button>
        </div>
        <button type="button" class="button obirc-remove-slide-row">${obircAboutL10n.removeText}</button>
      </div>
    `;
    $("#obirc-about-slides-repeater").append(newRow);
    slideCounter++;
    $("#obirc-about-slide-count").val(slideCounter);
  });

  $(document).on("click", ".obirc-remove-slide-row", function () {
    $(this).closest(".obirc-slide-row").remove();
    reindexSlides();
  });

  function reindexSlides() {
    var newCounter = 0;
    $("#obirc-about-slides-repeater .obirc-slide-row").each(function (index) {
      $(this).attr("data-index", index);
      $(this)
        .find('input[name*="[title]"]')
        .attr("name", "obirc_about_slides[" + index + "][title]");
      $(this)
        .find('input[name*="[subtitle]"]')
        .attr("name", "obirc_about_slides[" + index + "][subtitle]");
      $(this)
        .find('input[name*="[image]"]')
        .attr("name", "obirc_about_slides[" + index + "][image]");
      newCounter = index + 1;
    });
    $("#obirc-about-slide-count").val(newCounter);
    slideCounter = newCounter;
  }

  $(document).on("click", ".select-slide-image", function (e) {
    e.preventDefault();
    var button = $(this);
    var wrapper = button.closest(".slide-image-wrapper");

    var frame = wp.media({
      title: obircAboutL10n.selectImage,
      button: { text: obircAboutL10n.selectImage },
      multiple: false,
    });

    frame.on("select", function () {
      var attachment = frame.state().get("selection").first().toJSON();
      wrapper.find(".slide-image-url").val(attachment.url);
      wrapper
        .find(".image-preview")
        .html(
          '<img src="' + encodeURI(attachment.url) + '" class="preview-thumb">',
        );
      wrapper.find(".remove-slide-image").removeClass("hidden");
    });

    frame.open();
  });

  // Remove image
  $(document).on("click", ".remove-slide-image", function (e) {
    e.preventDefault();
    var wrapper = $(this).closest(".slide-image-wrapper");
    wrapper.find(".slide-image-url").val("");
    wrapper.find(".image-preview").empty();
    $(this).addClass("hidden");
  });
});
