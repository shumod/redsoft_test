<?php

namespace Drupal\user_clock\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\user\UserInterface;

/**
 * Class UserClockController.
 */
class UserClockController extends ControllerBase {

  /**
   * Custom user access to clocks
   *
   * @param \Drupal\user\UserInterface $user
   */
  public function access(UserInterface $user) {
    $current_user = \Drupal::currentUser();
    $result = AccessResult::forbidden();

    if($current_user->hasPermission('view all users clocks')){
      $result = AccessResult::allowed();
    }
    else if(
      $current_user->hasPermission('view my clock')
      && $current_user->id() === $user->id()
    ){
      $result = AccessResult::allowed();
    }

    return $result;
  }
  
  /**
   * Build clock page
   *
   * @return string
   *   Return user clock.
   */
  public function build(UserInterface $user) {
    /* @var DrupalDateTime $user_datetime */
    $user_datetime = $this->getCurrentUserDateTime();
    /* @var DateFormatter $formatter */
    $formatter = \Drupal::service('date.formatter');

    $link = Link::fromTextAndUrl(
      $this->t('Refresh'),
      Url::fromRoute(
        'user_clock.current_time',
        [],
        ['attributes' => [
          'class' => 'use-ajax'
        ]])
    );

    $output = [
      '#type' => 'markup',
      '#markup' =>
        '<div id="user-clock">'. $formatter->format($user_datetime->getTimestamp(), 'html_datetime') . '</div>'
        . $link->toString(),
    ];

    return $output;
  }

  /**
   * Set current time Ajax command
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function setCurrentTimeAjax() {
    $response = new AjaxResponse();
    $selector = '#user-clock';

    /* @var DrupalDateTime $user_datetime */
    $user_datetime = $this->getCurrentUserDateTime();
    /* @var DateFormatter $formatter */
    $formatter = \Drupal::service('date.formatter');

    $response->addCommand(new ReplaceCommand(
      $selector,
      '<div id="user-clock">'
        . $formatter->format($user_datetime->getTimestamp(), 'html_datetime') .
      '</div>'
    ));
    return $response;
  }

  /**
   * Current user DateTimeObject
   *
   * @return static
   */
  public function getCurrentUserDateTime() {
    $current_user = \Drupal::currentUser();
    $server_time = \Drupal::time()->getCurrentTime();

    return DrupalDateTime::createFromTimestamp(
      $server_time,
      $current_user->getTimeZone()
    );
  }
  
  /**
   * Return user clock page title
   *
   * @param \Drupal\user\UserInterface $user
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public function title(UserInterface $user) {
    return $this->t('Clock by :name', [':name' => $user->getAccountName()]);
  }

}
