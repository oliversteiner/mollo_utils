<?php


namespace Drupal\mollo_utils\Utility;


use Drupal\user\Entity\User;

class MolloUtilsUser {

  /**
   * @return mixed
   */
  public static function greetingMessageToUser(): string {
    /* Greeting Message */
    $user_name = \Drupal::currentUser()->getDisplayName();

    if ($user_name && is_string($user_name)) {
      $user_name = ucfirst($user_name);

      /* Text */
      $greeting = t("Good Day");
      $numeric_date = date("G");
      if ($numeric_date >= 0 && $numeric_date <= 11) {
        $greeting = t("Good Morning");
      }
      else {
        if ($numeric_date >= 12 && $numeric_date <= 17) {
          $greeting = t("Good Day");
        }
        else {
          if ($numeric_date >= 18 && $numeric_date <= 23) {
            $greeting = t("Good Evening");
          }
        }
      }
      return $greeting . ', ' . $user_name;
    }
    return '';
  }

}
