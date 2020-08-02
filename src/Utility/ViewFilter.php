<?php


namespace Drupal\mollo_utils\Utility;


class ViewFilter {


  /**
   * @param $form
   *
   * @return mixed
   */
  public static function filter_mollo_event($form) {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'mollo_event')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $all = t('Alle'); // TODO add translation
    $options = ['' => '-- ' . $all . ' -- '];

    foreach ($nodes as $nid => $node) {
      $value = $node->label();
      $id = $node->id();

      if (isset($value)) {
        $options[$id] = $value;
      }
    }

    if (isset($form['event'])) {
      $form['event']['#type'] = 'select';
      $form['event']['#options'] = $options;
      $form['event']['#size'] = 1;
    }
    return $form;
  }

}
