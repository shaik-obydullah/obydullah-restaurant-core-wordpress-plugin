/**
 * Opening Hours Admin Script
 *
 * @package ObydullahRestaurantCore
 * @since   1.0.0
 * @author  Shaik Obydullah
 *
 * Handles the opening hours repeater:
 * - Add new hour rows
 * - Remove existing rows
 * Uses localised strings from obirc_opening_hours.
 */

jQuery(document).ready(function ($) {
  $("#obirc-add-hours-row").on("click", function () {
    var newRow =
      '<div class="obirc-hours-row">' +
      '<input type="text" name="obirc_hours_day[]" class="obirc-hours-day" placeholder="' +
      obirc_opening_hours.dayPlaceholder +
      '">' +
      '<input type="text" name="obirc_hours_time[]" class="obirc-hours-time" placeholder="' +
      obirc_opening_hours.timePlaceholder +
      '">' +
      '<button type="button" class="button obirc-remove-row">' +
      obirc_opening_hours.removeText +
      "</button>" +
      "</div>";
    $("#obirc-hours-repeater").append(newRow);
  });
  $(document).on("click", ".obirc-remove-row", function () {
    $(this).closest(".obirc-hours-row").remove();
  });
});
