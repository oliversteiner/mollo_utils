<?php

namespace Drupal\mollo_utils\Plugin\Block;

use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'clear_cache' block.
 *
 * @Block(
 *  id = "clear_cache",
 *  admin_label = @Translation("Clear Cache Block"),
 *   category = @Translation("Mollo"),
 * )
 */
class ClearCache extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array
  {

    $build = [];
    $build['#theme'] = 'mollo_clear_cache';
    $build['#attached']['library'][] =
      'mollo_utils/clear_cache';
    return $build;
  }


}
