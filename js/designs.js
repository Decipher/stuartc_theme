if (Drupal.jsEnabled) {
  $(document).ready(function() {
    $('.views-field-field-header-image-fid').hover(
      function() {
        $(this).find('.view-screenshot').fadeIn();
      },
      function() {
        $(this).find('.view-screenshot').fadeOut();
      }
    );
  });
}
