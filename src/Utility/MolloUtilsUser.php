<?php


namespace Drupal\mollo_utils\Utility;


use Drupal\user\Entity\User;
use Exception;

class MolloUtilsUser
{

  /**
   * @return mixed
   */
  public static function greetingMessageToUser(): string
  {
    /* Greeting Message */
    $user_name = \Drupal::currentUser()->getDisplayName();

    if ($user_name && is_string($user_name)) {
      $user_name = ucfirst($user_name);

      /* Text */
      $greeting = t("Good Day");
      $numeric_date = date("G");
      if ($numeric_date >= 0 && $numeric_date <= 11) {
        $greeting = t("Good Morning");
      } else {
        if ($numeric_date >= 12 && $numeric_date <= 17) {
          $greeting = t("Good Day");
        } else {
          if ($numeric_date >= 18 && $numeric_date <= 23) {
            $greeting = t("Good Evening");
          }
        }
      }
      return $greeting . ', ' . $user_name;
    }
    return '';
  }

  public static function userFullName($user_id = false): string
  {
    if ($user_id) {
      $user = User::load($user_id);
    } else {
      $user = User::load(\Drupal::currentUser()->id());
    }

    if ($user && !empty($user)) {
      $first_name = self::getFieldValue($user, 'field_first_name');
      $last_name = self::getFieldValue($user, 'field_last_name');
      // return $user_id;
         return $first_name . ' ' . $last_name;
    }
    return '';

  }

  public static function getFieldValue($user, $field_name)
  {
    try {
      if (
        $field_name &&
        $user->hasField($field_name) &&
        $user->get($field_name)
      ) {
        $value = $user->get($field_name)->getValue();
        if ($value && $value[0] && isset($value[0]['value'])) {
          return $value[0]['value'];
        }
      }
    } catch (Exception $e) {
      return '';
    }

  }

}


