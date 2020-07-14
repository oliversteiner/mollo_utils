(function($, Drupal, drupalSettings) {
  Drupal.behaviors.molloArtist = {
    attach(context, settings) {
      console.log("Mollo Artist");

        $('#mollo-modultemplate', context)
          .once('mollo-modultemplate')
          .each(() => {});

    },
  };
})(jQuery, Drupal, drupalSettings);
