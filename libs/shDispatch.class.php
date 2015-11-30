<?php

namespace soho;

use soho\shController as controller;
use FStudio\myConfig as config;
use soho\shRouting as routing;
use soho\shRequest as request;
use soho\shSession as session;
use soho\shI18n as i18n;
use soho\shView as view;
use soho\shHook as hook;
use Exception;

/**
 * Description of shDispatch
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shDispatch {

  /**
   *
   * @var routing
   */
  private $routing;

  /**
   *
   * @var request
   */
  private $request;

  /**
   *
   * @var session
   */
  private $session;

  /**
   *
   * @var i18n
   */
  private $i18n;

  /**
   *
   * @var hook
   */
  private $hook;

  /**
   *
   * @var view
   */
  private $view;

  /**
   *
   * @var config
   */
  private $config;

  /**
   *
   * @var controller
   */
  private $controller;

  public function __construct(config $config, view $view, routing $routing, request $request, session $session, i18n $i18n, hook $hook) {
    $this->config = $config;
    $this->view = $view;
    $this->routing = $routing;
    $this->request = $request;
    $this->session = $session;
    $this->i18n = $i18n;
    $this->hook = $hook;
    if (!$this->session->hasFirstCall()) {
      $this->session->setFirstCall(true);
    }
  }

  public function main($module = null, $action = null) {
    try {
      if (isset($GLOBALS['task']) === true) {
        require_once $this->config->getPath() . 'libs/plugins/fsSohoPlugin/libs/task/task.php';
      } else {
        $this->i18n->setCulture($this->config->getDefaultCulture());
        $this->routing->registerModuleAndAction($module, $action);
        $this->hook->hooksIni();
        $this->loadModuleAndAction();
        $this->hook->hooksEnd();
        $this->renderView();
      }
    } catch (Exception $exc) {
      $this->session->setFlash('exc', $exc);
      $this->routing->forward('@FStudio_exception');
    }
  }

  private function loadModuleAndAction() {
    $controllerFolder = $this->session->getModule();
    $controllerFile = $controllerFolder . 'Actions';
    $action = 'execute' . ucfirst($this->session->getAction());
    $controllerFileAction = $this->session->getAction() . 'Action';
    if ($this->checkFile($controllerFolder, $controllerFile)) {
      $this->includeFileAndInitialize($controllerFolder, $controllerFile);
      if (method_exists($this->controller, $action) === true) {
        $this->executeAction($action);
      } else if ($this->checkFile($controllerFolder, $controllerFileAction)) {
        $this->includeFileAndInitialize($controllerFolder, $controllerFileAction);
        $this->executeAction('execute');
      } else {
        throw new Exception($this->i18n->__(00001, null, 'errors'), 00001);
      }
    } elseif ($this->checkFile($controllerFolder, $controllerFileAction)) {
      $this->includeFileAndInitialize($controllerFolder, $controllerFileAction);
      $this->executeAction('execute');
    } else {
      throw new Exception($this->i18n->__(00001, null, 'errors'), 00001);
    }
  }

  private function checkFile($controllerFolder, $controllerFile) {
    return is_file($this->config->getPath() . 'controller/' . $controllerFolder . '/' . $controllerFile . '.class.php');
  }

  private function includeFileAndInitialize($controllerFolder, $controllerFile) {
    include_once $this->config->getPath() . 'controller/' . $controllerFolder . '/' . $controllerFile . '.class.php';
    $this->controller = new $controllerFile($this->config, $this->routing, $this->session, $this->i18n);
  }

  private function executeAction($action) {
    if (method_exists($this->controller, 'preExecute')) {
      $this->controller->preExecute($this->request);
    }
    $this->controller->$action($this->request);
    if (method_exists($this->controller, 'postExecute')) {
      $this->controller->postExecute($this->request);
    }
    //$controller->setArgs((array) $controller);
  }

  private function renderView() {
    $this->view->renderHTML($this->controller->getModule(), $this->controller->getTemplate(), $this->controller->getFormat(), array_merge($this->controller->getArgs(), (array) $this->controller));
  }

}
