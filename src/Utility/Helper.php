<?php

namespace Drupal\mollo_utils\Utility;

use Drupal\node\Entity\Node;

/**
 * Outdated
 * Use the "MolloUtils" class instead
 * This document is for backward compatibility only
 */
class Helper {

  /**
   * @param $vid
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermsByID($vid): array {

    return MolloUtils::getTermsByID($vid);
  }

  /**
   * @param $term_id
   *
   * @return mixed
   */
  public static function getTermNameByID($term_id): string {
    return MolloUtils::getTermNameByID($term_id);

  }

  /**
   * @param $term_id
   *
   * @return string
   */
  public static function getTermIconByID($term_id): string {
    return MolloUtils::getTermIconByID($term_id);
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
    return MolloUtils::getTermIDByName($term_name, $vid, $create);

  }

  /**
   * @param $vid
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTermsByName($vid): array {
    return MolloUtils::getTermsByName($vid);

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
    return MolloUtils::getFieldValue($node,
      $field_name,
      $term_list_name,
      $force_array);

  }

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param string $field_name
   *
   * @return boolean | string | array
   */
  public static function getBoolean($node, $field_name) {
    return MolloUtils::getBoolean($node, $field_name);

  }

  /**
   * @return string
   * @throws \Exception
   */
  public static function generateToken(): string {
    return MolloUtils::generateToken();
  }

  /**
   * @param string $field_name
   *
   * @return boolean | string | array
   */
  public static function getAudioFieldValue($node, $field_name) {
    return MolloUtils::getAudioFieldValue($node, $field_name);

  }

  /**
   * @param $img_id_or_file
   * @param $image_style_id
   * @param bool $dont_create
   *
   */
  public static function createImageStyle(
    $img_id_or_file,
    $image_style_id,
    $dont_create = FALSE
  ) {
    return MolloUtils::createImageStyle($img_id_or_file,
      $image_style_id,
      $dont_create);

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
    return MolloUtils::countItems($node, $field_name);

  }

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param string $field_name
   *
   * @return string
   */
  public static function validateNode(Node $node, string $field_name): string {
    return MolloUtils::validateNode($node, $field_name);

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
    return MolloUtils::publishToggle($nid);

  }

  /**
   * @param $nid
   *
   * @return string
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTitleFromNid($nid): string {
    return MolloUtils::getTitleFromNodeId($nid);

  }

  /**
   *
   */
  public static function getToken($node_or_node_id) {
    return MolloUtils::getToken($node_or_node_id);
  }

}
