<?php

namespace soho;

use soho\shSession as session;
use soho\shRequest as request;
use soho\shDispatch as dispatch;
use FStudio\myConfig as config;
use soho\shI18n as i18n;
use soho\shCacheManager as cacheManager;
use Exception;

/**
 * Description of shRouting
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shRouting {

  /**
   *
   * @var session
   */
  private $session;

  /**
   *
   * @var request
   */
  private $request;

  /**
   *
   * @var dispatch
   */
  private $dispatch;

  /**
   *
   * @var config
   */
  private $config;

  /**
   *
   * @var i18n
   */
  private $i18n;

  /**
   *
   * @var cacheManager
   */
  private $cache_manager;

  public function __construct(session $session, request $request, config $config, i18n $i18n, cacheManager $cache_manager,  dispatch $dispatch = null) {
    $this->session = $session;
    $this->request = $request;
    $this->config = $config;
    $this->i18n = $i18n;
    $this->cache_manager = $cache_manager;
    $this->dispatch = $dispatch;
  }
  
  public function setDispatch(dispatch $dispatch) {
    $this->dispatch = $dispatch;
  }

  /**
   * $module = '@default_index';
   * o
   * $module = 'default'; $action = 'index';
   *
   * @param string $module
   * @param string $action [optional]
   * @return array
   */
  public function validateRouting($module, $action = null) {
    $yamlRouting = $this->cache_manager->loadYaml($this->config->getPath() . 'config/routing.yml', 'routingYaml');
    if (preg_match('/^@\w+/', $module) === 1 and $action === null) {
      if (!isset($yamlRouting[substr($module, 1)])) {
        throw new exception('La ruta "' . $module . '" no está definida');
      } else {
        $answer = $yamlRouting[substr($module, 1)];
      }
    } else {
      $flag = true;
      foreach ($yamlRouting as $routing) {
        if ($routing['param']['module'] === $module and $routing['param']['action'] === $action) {
          $flag = false;
          $answer = $routing;
          break;
        }
      }
      if ($flag === true) {
        throw new Exception('El módulo "' . $module . '" y acción "' . $action . '"no está definido');
      }
    }
    return $answer;
  }

  /**
   * $module = '@default_index';
   * $action = array('id' => 12);
   * o
   * $module = 'default'; $action = 'index';
   *
   * @param string $module
   * @param string|array $action [optional]
   */
  public function forward($module, $action = null) {
    if (preg_match('/^@\w+/', $module) === 1) {
      $routing = $this->validateRouting($module);
      $module = $routing['param']['module'];
      $action = $routing['param']['action'];
    } else {
      $routing = $this->validateRouting($module, $action);
    }
    $this->dispatch->main($module, $action);
    exit();
  }

  public function getUrlCss($css) {
    return $this->config->getUrl() . 'css/' . $css;
  }

  public function getUrlImg($image) {
    return $this->config->getUrl() . 'img/' . $image;
  }

  public function getUrlJs($javascript) {
    return $this->config->getUrl() . 'js/' . $javascript;
  }

  /**
   * $module = '@default_index';
   * $action = array('id' => 12);
   * o
   * $module = 'default'; $action = 'index';
   *
   * $variabls = array('id' => 12);
   * @param string $module
   * @param string|array $action [optional]
   * @param array $variables [optional]
   */
  public function getUrlWeb($module, $action = null, $variables = null) {
    if (preg_match('/^@\w+/', $module) === 1) {
      $routing = $this->validateRouting($module);
      $module = $routing['param']['module'];
      $variables = $this->genVariables($action);
      $action = $routing['param']['action'];
    } else {
      $routing = $this->validateRouting($module, $action);
    }
    return $this->config->getUrl() . $this->config->getIndexFile() . $routing['url'] . $this->genVariables($variables);
  }

  /**
   * $module = '@default_index';
   * $action = array('id' => 12);
   * o
   * $module = 'default'; $action = 'index';
   *
   * $variabls = array('id' => 12);
   * @param string $module
   * @param string|array $action [optional]
   * @param array $variables [optional]
   */
  public function redirect($module, $action = null, $variables = null) {
    if (preg_match('/^@\w+/', $module) === 1) {
      $routing = $this->validateRouting($module);
      $module = $routing['param']['module'];
      $variables = $this->genVariables($action);
      $action = $routing['param']['action'];
      header('Location: ' . $this->getUrlWeb($module, $action, $variables));
      exit();
    } else {
      header('Location: ' . $this->getUrlWeb($module, $action, $variables));
      exit();
    }
  }

  public function registerModuleAndAction($module = null, $action = null) {
    if ($module !== null and $action !== null) {
      $yamlRouting = $this->cache_manager->loadYaml($this->config->getPath() . 'config/routing.yml', 'routingYaml');
      $flag = false;
      foreach ($yamlRouting as $routing) {
        if ($module === $routing['param']['module'] and $action === $routing['param']['action']) {
          $this->session->setModule($routing['param']['module']);
          $this->session->setAction($routing['param']['action']);
          $this->session->setLoadFiles(((isset($routing['load'])) ? $routing['load'] : null));
          $this->session->setFormatOutput($routing['param']['format']);
          $flag = true;
          break;
        }
      }
      if ($flag === false) {
        throw new Exception($this->i18n->__(00002, null, 'errors'), 00002);
      }
      return true;
    } elseif ($this->request->hasServer('PATH_INFO')) {
      $data = explode('/', $this->request->getServer('PATH_INFO'));
      if (($data[0] === '' and ! isset($data[1])) or ( $data[0] === '' and $data[1] === '')) {
        $this->registerDefaultModuleAndAction();
      } else {
        $url = '/^(';
        foreach ($data as $key => $value) {
          $url .= (($value === '' and $key === 0)) ? '' : $value;
          $url .= (isset($data[($key + 1)])) ? '\/' : '';
        }
        $url .= ')/';
        $yamlRouting = $this->cache_manager->loadYaml($this->config->getPath() . 'config/routing.yml', 'routingYaml');
        $flag = false;
        foreach ($yamlRouting as $routing) {
          if (preg_match($url, $routing['url']) === 1) {
            $this->session->setModule($routing['param']['module']);
            $this->session->setAction($routing['param']['action']);
            $this->session->setLoadFiles(((isset($routing['load'])) ? $routing['load'] : null));
            $this->session->setFormatOutput($routing['param']['format']);
            $flag = true;
            break;
          }
        }
        if ($flag === false) {
          throw new Exception($this->i18n->__(00002, null, 'errors'), 00002);
        }
        return true;
      }
    } else {
      $this->registerDefaultModuleAndAction();
    }
  }

  private function registerDefaultModuleAndAction() {
    $yamlRouting = $this->cache_manager->loadYaml($this->config->getPath() . 'config/routing.yml', 'routingYaml');
    $this->session->setModule($yamlRouting['homepage']['param']['module']);
    $this->session->setAction($yamlRouting['homepage']['param']['action']);
    $this->session->setLoadFiles(((isset($yamlRouting['homepage']['load'])) ? $yamlRouting['homepage']['load'] : false));
    $this->session->setFormatOutput($yamlRouting['homepage']['param']['format']);
  }

  /**
   *
   * @param array $variables
   * @return boolean|string
   */
  private function genVariables($variables) {
    $answer = false;
    if (is_array($variables)) {
      $answer = '?';
      foreach ($variables as $key => $value) {
        $answer .= $key . '=' . $value . '&';
      }
      $answer = substr($answer, 0, (strlen($answer) - 1));
    } else {
      $answer = $variables;
    }
    return $answer;
  }

}
