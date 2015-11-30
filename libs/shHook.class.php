<?php

namespace soho;

use FStudio\myConfig as config;
use soho\shCacheManager as cacheManager;

/**
 * Description of shHook
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shHook {

  private $listHooks;

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

  public function hooksIni() {
    if (!$this->listHooks) {
      $this->listHooks = $this->cache_manager->loadYaml($this->config->getPath() . 'config/hooks.yml', 'hooksYaml');
    }
    $this->loadHooksAndExecute($this->listHooks['ini'], ((isset($this->listHooks['configLoader'])) ? $this->listHooks['configLoader'] : null));
  }

  public function hooksEnd() {
    if (!$this->listHooks) {
      $this->listHooks = $this->cache_manager->loadYaml($this->config->getPath() . 'config/hooks.yml', 'hooksYaml');
    }
    $this->loadHooksAndExecute($this->listHooks['end'], ((isset($this->listHooks['configLoader'])) ? $this->listHooks['configLoader'] : null));
  }

  private function loadHooksAndExecute($listHooks, $filesToLoad = null) {
    if ($filesToLoad !== null and is_array($listHooks) and count($listHooks) > 0) {
      foreach ($listHooks as $hook) {
        if (isset($filesToLoad[$hook]) and is_array($filesToLoad[$hook])) {
          foreach ($filesToLoad[$hook] as $file) {
            require_once $this->config->getPath() . $file;
          }
        }
        $hookFileClass = $hook . 'Hook.class';
        $hookClass = $hook . 'Hook';
        require_once $this->config->getPath() . 'lib/hooks/' . $hookFileClass . '.php';
        $hookObj = '\\hook\\' . $hook . '\\' . $hookClass;
        $hookObj::hook();
      }
    }
  }

}
