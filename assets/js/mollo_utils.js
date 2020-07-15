(function($, Drupal, drupalSettings) {
  Drupal.behaviors.molloArtist = {
    attach(context, settings) {
      console.log("Mollo Artist");

        $('#mollo-utils', context)
          .once('mollo-utils')
          .each(() => {});

    },
  };
})(jQuery, Drupal, drupalSettings);
