<?php


namespace Drupal\mollo_utils\Utility;

interface iMolloUi{

}

class MolloUi {

  /**
   * @param array|null $icon_set
   * @param array|null $icon_variant
   *
   * @return string
   */
  public static function getIconPrefixFromIconSet(): string {

    $config = \Drupal::config('views_admintools.settings');
    $icon_set = $config->get('icon_set');
    $icon_variant = $config->get('icon_variant');

    switch ($icon_set) {
      case 'fontawesome': // 'Font Awesome 5'
        $icon_pre = $icon_variant . ' fa-';
        break;

      case 'bootstrap_3': // 'Bootstrap 3'
        $icon_pre = $icon_variant . ' glyphicon-';
        break;

      default:
        // 'drupal' is default
        $icon_pre = $icon_variant . ' ui-icon-';
        break;
    }
    return $icon_pre;
  }

  public static function getIconVariantOption()
  {
    return [
      '' => 'None',
      'fas' => 'fas',
      'far' => 'far',
      'fal' => 'fal',
      'fa' => 'fa',
      'glyphicon' => 'glyphicon',
      'ui-icon' => 'ui-icon'
    ];
  }

  public static function getIconSetOption()
  {
    return [
      'drupal' => 'Drupal / jQuery Ui',
      'fontawesome' => 'Font Awesome 5',
      'bootstrap_3' => 'Twitter Bootstrap 3'
    ];
  }

}
