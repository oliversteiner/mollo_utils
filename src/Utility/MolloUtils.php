<?php

namespace Drupal\mollo_utils\Utility;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 *
 */
class MolloUtils {

  /**
   * @param string $vid
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermsByID(string $vid): array {
    $term_list = [];
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    foreach ($terms as $term) {
      $term_list[(integer) $term->tid] = (string) $term->name;
    }
    return $term_list;
  }

  /**
   * @param $term_id
   *
   * @return mixed
   */
  public static function getTermNameByID($term_id): string {
    $term = Term::load($term_id);
    if (!empty($term)) {
      return $term->getName();
    }
    return '';
  }

  /**
   * @param $term_id
   *
   * @return string
   */
  public static function getTermIconByID($term_id): string {
    $term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($term_id);
    $value = $term->get('field_mollo_icon')->getValue();
    if ($value) {
      $icon_name = $value[0]['icon_name'];
      $style = $value[0]['style'];

      return $style . ' fa-' . $icon_name;
    }
    return '';
  }

  /**
   * @param $term_name
   * @param $vid
   * @param bool $create
   *
   * @return int
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermIDByName($term_name, $vid, $create = TRUE): int {
    $tid = 0;

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    foreach ($terms as $term) {
      if ($term->name === $term_name) {
        $tid = $term->tid;
        break;
      }
    }

    // Create new Term.
    if ($tid === 0 && $create === TRUE) {
      try {
        $new_term = Term::create([
          'name' => $term_name,
          'vid' => $vid,
        ])->save();
        $tid = $new_term;
      }
      catch (EntityStorageException $e) {
      }
    }

    return $tid;
  }

  /**
   * @param $vid
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermsByName($vid): array {
    $term_list = [];
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    foreach ($terms as $term) {
      $term_list[$term->name] = $term->tid;
    }
    return $term_list;
  }

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param string $field_name
   * @param null $term_list_name
   * @param bool | string $force_array
   *
   * @return boolean | string | array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getFieldValue(
    $node,
    $field_name,
    $term_list_name = NULL,
    $force_array = FALSE
  ) {
    $result = FALSE;
    $term_list = [];

    if ($term_list_name && is_string($term_list_name)) {
      $term_list = self::getTermsByID($term_list_name);
    }

    $field_name = self::validateNode($node, $field_name);

    try {
      if (
        $field_name &&
        $node->hasField($field_name) &&
        $node->get($field_name)
      ) {
        $value = $node->get($field_name)->getValue();
        $type = $node
          ->get($field_name)
          ->getFieldDefinition()
          ->getType();

        // Single Item.
        if (count($value) === 1) {
          // Value Field.
          if ($value && $value[0] && isset($value[0]['value'])) {
            $result = $value[0]['value'];
          }

          // Target Field.
          if ($value && $value[0] && isset($value[0]['target_id'])) {
            $result = $value[0]['target_id'];
          }

          // Duration Field.
          if ($value && $value[0] && isset($value[0]['duration'])) {
            $result = $value[0]['duration'];
          }

          // Time Date.
          if ($type === 'datetime') {
            $result = $node->get($field_name)->date->getTimestamp();
          }

          // Value is Taxonomy Term.
          if ($term_list) {
            if ($term_list && $term_list[$result]) {
              $result = $term_list[$result];
            }
            else {
              $message = "No Term found with id {$result} in Taxonomy {$term_list}";
              \Drupal::logger('mollo_utils')->notice($message);
            }
          }

          if ($force_array === TRUE) {
            $arr[] = $result;
            $result = $arr;
          }
          if ($force_array === 'full') {
            $term = [];
            $term['id'] = (int) $value[0]['target_id'];
            $term['name'] = $term_list[$value[0]['target_id']];

            $arr[] = $term;
            $result = $arr;
          }
        }

        // Multiple Items.
        $i = 0;
        if (count($value) > 1) {
          foreach ($value as $item) {
            // Standart Field.
            if (isset($item['value'])) {
              $result[$i] = $item['value'];
            }

            // Target Field and Termlist
            // Value is Taxonomy Term.
            if ($term_list_name) {
              if ($term_list_name && $term_list[$item['target_id']]) {
                if ($force_array === 'full') {
                  $term = [];
                  $term['id'] = (int) $item['target_id'];
                  $term['name'] = $term_list[$item['target_id']];
                  $result[$i] = $term;
                }
                else {
                  $result[$i] = $term_list[$item['target_id']];
                }
              }
              else {
                $result[$i] = FALSE;
                $message = "No Term found with id {$result} in Taxonomy {$term_list_name}";
                \Drupal::logger('mollo_utils')->notice($message);
              }
            }
            elseif (isset($item['target_id'])) {
              $result[$i] = $item['target_id'];
            }
            $i++;
          }
        }

        // No Items.
        if ($force_array && count($value) === 0) {
          $result = [];
        }
      }
    }
    catch (\Exception $e) {
      throw new \RuntimeException(
        'field_name (' . $field_name . ') Error \r' . $e
          );
    }

    return $result;
  }

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param string $field_name
   *
   * @return boolean | string | array
   */
  public static function getBoolean($node, $field_name) {
    $result = FALSE;

    try {
      if (!is_object($node)) {
        throw new \RuntimeException(
          'The $node Parameter is not a valid drupal entity.' .
          ' (Field: ' .
          $field_name .
          ' Node:' .
          $node .
          ')'
        );
      }

      if (!is_string($field_name)) {
        throw new \RuntimeException('field_name must be a string');
      }
    }
    catch (\Exception $e) {
      throw new \RuntimeException(
        '$field_name must be a string.' .
        ' (Field: ' .
        $field_name .
        ' Node:' .
        $node->getType() .
        ') ' .
        $e
          );
    }

    try {
      if ($node->hasField($field_name) && $node->get($field_name)->value) {
        $value = $node->get($field_name)->getValue();

        $result = $value ? TRUE : FALSE;
      }
    }
    catch (\Exception $e) {
      throw new \RuntimeException(
        'field_name (' . $field_name . ') not found \r' . $e
          );
    }

    return $result;
  }

  /**
   * @param $node_or_node_id
   *
   * @return bool
   */
  public static function getToken($node_or_node_id) {
    $field_name = 'field_mollo_token';

    if (is_numeric($node_or_node_id)) {
      $entity = Node::load($node_or_node_id);
    }
    else {
      $entity = $node_or_node_id;
    }

    if ($entity->get($field_name)) {
      $value = $entity->get($field_name)->getValue();

      // Value Field.
      if ($value && $value[0] && isset($value[0]['value'])) {
        return $value[0]['value'];
      }
    }
    return FALSE;
  }

  /**
   * @return string
   * @throws \Exception
   */
  public static function generateToken(): string {
    return random_bytes(20);
  }

  /**
   * @param \Drupal\node\Entity\NodeInterface | Node $node
   * @param string $field_name
   *
   * @return boolean | string | array
   */
  public static function getAudioFieldValue($node, $field_name) {
    $result = [];

    $field_name = 'field_' . $field_name;

    // Url to audio file.
    $url = '';
    $file_name = '';
    $mime_type = '';

    if (!$node->get($field_name)->isEmpty()) {
      // Media.
      $media_entity = $node->get($field_name)->entity;
      $name = $media_entity->label();
      $mid = $media_entity->id();

      // Media -> Audio.
      $media_field = $media_entity
        ->get('field_media_audio_file')
        ->first()
        ->getValue();
      $tid = $media_field['target_id'];

      // Media -> Audio -> File.
      if ($tid) {
        $file = File::load($tid);
        if ($file) {
          $file_name = $file->getFilename();
          $uri = $file->getFileUri();
          $url = file_create_url($uri);
          $mime_type = $file->getMimeType();
        }

        $result = [
          'mid' => $mid,
          'tid' => $tid,
          'media_link' => $url,
          'mime_type' => $mime_type,
          'name' => $name,
          'file_name' => $file_name,
        ];
      }
    }

    return $result;
  }

  /**
   * @param $img_id_or_file
   * @param $image_style_id
   * @param bool $dont_create
   *
   * @return array|Image
   */
  public static function createImageStyle(
    $img_id_or_file,
    $image_style_id,
    $dont_create = FALSE
  ) {
    $image = [];
    $image_style = ImageStyle::load($image_style_id);

    if ($img_id_or_file && $img_id_or_file instanceof FileInterface) {
      $file = $img_id_or_file;
    }
    else {
      $file = File::load($img_id_or_file);
    }

    if ($file && $image_style) {
      $file_image = \Drupal::service('image.factory')->get($file->getFileUri());
      /** @var \Drupal\Core\Image\Image $image */

      if ($file_image->isValid()) {
        $image_uri = $file->getFileUri();
        $destination = $image_style->buildUrl($image_uri);

        if (!file_exists($destination)) {
          if (!$dont_create) {
            $image_style->createDerivative($image_uri, $destination);
          }
        }

        $file_size = filesize($image_uri);
        $file_size_formatted = format_size($file_size);
        [$width, $height] = getimagesize($image_uri);

        $image['url'] = $destination;
        $image['uri'] = $image_uri;
        $image['file_size'] = $file_size;
        $image['file_size_formatted'] = $file_size_formatted;
        $image['width'] = $width;
        $image['height'] = $height;
      }
    }
    return $image;
  }

  /**
   * Return number of Items in multivalued Field.
   *
   * @param \Drupal\node\Entity\Node $node
   * @param string $field_name
   * @param null $term_list_name
   * @param bool | string $force_array
   *
   * @return boolean | string | array
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function countItems($node, $field_name) {
    $field_name = self::validateNode($node, $field_name);

    try {
      if ($node->hasField($field_name) && $node->get($field_name)) {
        $value = $node->get($field_name)->getValue();

        // Single Item.
        if (count($value) === 1) {
          return 1;
        }

        // Multiple Items.
        if (count($value) > 1) {
          $count = 0;
          foreach ($value as $item) {
            if (isset($item['value'])) {
              $count++;
            }
            elseif (isset($item['target_id'])) {
              $count++;
            }
          }
          return $count;
        }
      }
    }
    catch (\Exception $e) {
      throw new \RuntimeException(
        'field_name (' . $field_name . ') Error \r' . $e
          );
    }
    return 0;
  }

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param string $field_name
   *
   * @return string
   */
  public static function validateNode(Node $node, string $field_name): string {
    $default_fields = ['body'];

    try {
      if (!is_object($node)) {
        throw new \RuntimeException(
          'The $node Parameter is not a valid drupal entity.' .
          ' (Field: ' .
          $field_name .
          ' Node:' .
          $node .
          ')'
        );
      }

      if (!is_string($field_name)) {
        throw new \RuntimeException('field_name must be a string');
      }

      if (is_string($field_name)) {
        // Check for 'body'.
        if (!in_array($field_name, $default_fields, FALSE)) {
          // Check for 'field_field_NAME'.
          $pos = strpos($field_name, 'field_');

          if ($pos === FALSE) {
            $field_name = 'field_' . $field_name;
          }
        }
      }
    }
    catch (\Exception $e) {
      throw new \RuntimeException(
        '$field_name must be a string.' .
        ' (Field: ' .
        $field_name .
        ' Node:' .
        $node .
        ') ' .
        $e
          );
    }
    return $field_name;
  }

  /**
   * @param $nid
   *
   * @return bool
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function publishToggle($nid) {

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $entity = $storage->load($nid);

    if (!isset($entity)) {
      return FALSE;
    }

    // Reverse the published status.
    if ($entity->isPublished()) {
      $entity->setUnpublished();
      $status = FALSE;
    }
    else {
      $entity->setPublished();
      $status = TRUE;

    }

    $entity->save();

    return $status;
  }

  /**
   * @param $nid
   *
   * @return string
   */
  public static function getTitleFromNodeId($nid): string {

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $entity = $storage->load($nid);
    $title = "";
    if (!isset($entity)) {
      return FALSE;
    }

    // Reverse the published status.
    if ($entity->label()) {
      $title = $entity->label();
    }

    return $title;
  }

  /**
   * @param $str
   * @param array $replace
   * @param string $delimiter
   *
   * @return bool|mixed|string
   *
   *   http://cubiq.org/the-perfect-php-clean-url-generator
   */
  public static function toAscii($str, array $replace = [], string $delimiter = '-'): mixed {
    if (!empty($replace)) {
      $str = str_replace((array) $replace, ' ', $str);
    }
    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace('/[^a-zA-Z0-9_|+ -]/', '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace('/[_|+ -]+/', $delimiter, $clean);

    return $clean;
  }

  /**
   * @param string $vid
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermList(string $vid): array {
    $term_list = [];
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    foreach ($terms as $term) {
      $term_list[] = ['id' => $term->tid, 'name' => $term->name];

    }

    return $term_list;
  }

  /**
   *
   */
  public static function getListOfTerms(string $vid): array {
    return self::getTermList($vid);
  }

  /**
   *
   */
  public static function getTemplatePath($module): string {
    return \Drupal::service('extension.list.module')->getPath($module) .
      '/templates/';
  }

}
