<?php


namespace Drupal\mollo_utils\Utility;


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
      $greeting = "Guten Tag";
      $numeric_date = date("G");
      if ($numeric_date >= 0 && $numeric_date <= 11) {
        $greeting = "Guten Morgen";
      }
      else {
        if ($numeric_date >= 12 && $numeric_date <= 17) {
          $greeting = "Guten Tag";
        }
        else {
          if ($numeric_date >= 18 && $numeric_date <= 23) {
            $greeting = "Guten Abend";
          }
        }
      }
      return $greeting . ', ' . $user_name;
    }
    return '';
  }

}
