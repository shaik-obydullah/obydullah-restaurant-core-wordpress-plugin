/**
 * Footer Settings Admin Script
 *
 * @package ObydullahRestaurantCore
 * @since   1.0.0
 * @author  Shaik Obydullah
 *
 * Handles the footer quick links repeater:
 * - Add new link rows
 * - Remove existing rows
 * Uses localised strings from obircFooterL10n.
 */

jQuery(document).ready(function ($) {
  // Add new link row using the hidden counter
  $("#obirc-add-footer-link").on("click", function () {
    var counter = $("#obirc-footer-link-count");
    var newIndex = parseInt(counter.val());
    var newRow =
      '<div class="obirc-footer-link-row obirc-repeater-row" data-index="' +
      newIndex +
      '">' +
      '<input type="text" name="obirc_footer_links[' +
      newIndex +
      '][text]" class="obirc-link-text" placeholder="' +
      obircFooterL10n.linkTextPlaceholder +
      '">' +
      '<input type="text" name="obirc_footer_links[' +
      newIndex +
      '][url]" class="obirc-link-url" placeholder="' +
      obircFooterL10n.urlPlaceholder +
      '">' +
      '<button type="button" class="button obirc-remove-row">' +
      obircFooterL10n.removeText +
      "</button>" +
      "</div>";
    $("#obirc-footer-links-repeater").append(newRow);
    counter.val(newIndex + 1);
  });

  // Remove row
  $(document).on("click", ".obirc-remove-row", function () {
    $(this).closest(".obirc-footer-link-row").remove();
  });
});
