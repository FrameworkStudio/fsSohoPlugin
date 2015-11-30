<?php

namespace soho;

use FStudio\myConfig as config;
use soho\shCacheManager as cacheManager;

/**
 * Description of i18nClass
 *
 * @author julianlasso
 */
class shI18n {

  protected $culture;

  /**
   *
   * @var config
   */
  private $config;

  /**
   *
   * @var cacheManager
   */
  private $cache_manager;

  public function __construct(config $config, cacheManager $cache_manager) {
    $this->config = $config;
    $this->cache_manager = $cache_manager;
  }

  /**
   *
   * @param integer $message
   * @param string $culture [optional]
   * @param string $dictionary [optional]
   * @param array $vars [optional] $vars[':nombre'] = 'NOMBRE';
   * @return string
   */
  public function __($message, $culture = null, $dictionary = 'default', $vars = array()) {
    if ($culture === null) {
      $culture = $this->getCulture();
    }
    $__ymlCulture = $this->cache_manager->loadYaml($this->config->getPath() . 'libs/i18n/' . $culture . '.yml', 'sohoPluginI18nYaml' . $culture);
    $rsp = '';
    if (count($vars) > 0) {
      $keys = array_keys($vars);
      $values = array_values($vars);
      $rsp = str_replace($keys, $values, $__ymlCulture['dictionary'][$dictionary][$message]);
    } else {
      $rsp = $__ymlCulture['dictionary'][$dictionary][$message];
    }
    return $rsp;
  }

  public function getCulture() {
    return $this->culture;
  }

  public function setCulture($culture) {
    $this->culture = $culture;
  }

}
