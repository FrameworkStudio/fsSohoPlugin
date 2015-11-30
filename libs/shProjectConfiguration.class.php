<?php

namespace soho;

use FStudio\myConfig as config;

/**
 * The current soho version.
 */
define('SOHO_PLUGIN_VERSION', '1.0.0');

/**
 * Description of shProjectConfiguration
 *
 * @author julianlasso
 */
class shProjectConfiguration {

  /**
   *
   * @var config
   */
  protected $config;

  public function __construct(config $config) {
    $this->config = $config;
  }

  /**
   *
   */
  public function autoLoad() {
    $urlBase = 'libs/plugins/fsSohoPlugin/';
    $classes = array(
        'libs/shController.class.php',
        'libs/shAction.class.php',
        'libs/shActions.class.php',
        'libs/shCacheManager.class.php',
        'libs/shCamelCase.class.php',
        'libs/shComponent.class.php',
        'libs/shAction.class.php',
        'libs/shActions.class.php',
        'libs/shDispatch.class.php',
        'libs/shHook.class.php',
        'libs/shI18n.class.php',
        'libs/shRequest.class.php',
        'libs/shRouting.class.php',
        'libs/shSession.class.php',
        'libs/shView.class.php',
        //'libs/tableBaseClass.php',
        'yaml-2.7.7/Yaml.php',
        'yaml-2.7.7/Parser.php',
        'yaml-2.7.7/Inline.php',
    );
    foreach ($classes as $class) {
      require_once $this->config->getPath() . $urlBase . $class;
    }
  }

}
