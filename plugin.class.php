<?php

namespace fsSohoPlugin;

use FStudio\fsPlugin;
use FStudio\myConfig as config;

class plugin extends fsPlugin {

  public function __construct(config $config) {
    parent::__construct($config);
    require_once $config->getPath() . 'libs/plugins/fsSohoPlugin/libs/shProjectConfiguration.class.php';
    $project = new \soho\shProjectConfiguration($config);
    $project->autoLoad();

    $request = new \soho\shRequest(filter_input_array(INPUT_POST), filter_input_array(INPUT_GET), $_REQUEST, filter_input_array(INPUT_COOKIE), $_FILES, filter_input_array(INPUT_SERVER), filter_input_array(INPUT_ENV));
    $session = new \soho\shSession();
    $cache_manager = new \soho\shCacheManager($config, $session);
    $hook = new \soho\shHook($config, $cache_manager);
    $i18n = new \soho\shI18n($config, $cache_manager);
    $view = new \soho\shView($config, $session, $cache_manager);
    $routing = new \soho\shRouting($session, $request, $config, $i18n, $cache_manager);

    $dispatch = new \soho\shDispatch($config, $view, $routing, $request, $session, $i18n, $hook);
    $routing->setDispatch($dispatch);
    $dispatch->main();
    exit();
  }

}
