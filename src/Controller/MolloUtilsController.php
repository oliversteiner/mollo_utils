<?php

/**
 * @file
 * Contains \Drupal\mollo_utils\Controller\MolloUtilsController.
 */

namespace Drupal\mollo_utils\Controller;


use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AnnounceCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\mollo_utils\Utility\Helper;
use Drupal\mollo_utils\Utility\MolloUi;
use http\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\JsonResponse;

class MolloUtilsController {

  /**
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function clearCache() {

    $config = \Drupal::config('views_admintools.settings');
    $icon_pre = MolloUi::getIconPrefixFromIconSet();


    // $icon_success = $config->get('icon_success');
    // $icon_warning = $config->get('icon_warning');
    // $icon_error = $config->get('icon_error');

    $icon_info = 'info-circle';
    $icon_success = 'check';
    $icon_warning = 'exclamation-triangle';
    $icon_error = 'exclamation-circle';
    $icon_clear_cache = 'fa-sync-alt';


    $response = new AjaxResponse();
    $error_message = '';


    try {

      drupal_flush_all_caches();
      $message = t('Cache cleared.');
      \Drupal::messenger()->addMessage($message);


    } catch (Exception $e) {
      $error_message = $e->getMessage();
      $status = FALSE;
      // Debug Info
      \Drupal::messenger()->addError($error_message);

    }
    $status = TRUE; // debug


    if ($status) {
      $message = t('Caches cleared.');
      $message_status = 'success';
      $icon =
        sprintf('
 <div class="mollo-button-%s mollo-button-clear-cache">
    <span class="mollo-button-icon">
    <i class="%s%s"></i>
    </span>
  </div>', $message_status, $icon_pre, $icon_success);
    }
    else {
      $message = t('Could not clear cache');
      $message_status = 'error';

      $icon =
        sprintf('
 <div class="mollo-button-%s mollo-button-clear-cache">
    <span class="mollo-button-icon">   <i class="%s%s"></i></span>
  </div>', $message_status, $icon_pre, $icon_error);
    }

    // Button icon replace
    $response->addCommand(
      new ReplaceCommand('.mollo-button-clear-cache', $icon)
    );




    // Message
    $response->addCommand(
      new ReplaceCommand(
        '.ajax-container' . '',
        sprintf('<div class="mollo-message-clear-cache mollo-%s">%s</div>', $message_status, $message)
      )
    );

    // Reload Page
    $user_id = \Drupal::currentUser()->id();

    $response->addCommand(
      new RedirectCommand('/user/' . $user_id)
    );

    return $response;
  }


}
