<?php

namespace soho;

use soho\shView as view;
use FStudio\myConfig as config;
use soho\shI18n as i18n;
use soho\shRouting as routing;
use soho\shSession as session;

/**
 * Description of shController
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shController {

  private $template;
  private $module;
  private $format;
  private $args;

  /**
   *
   * @var config
   */
  private $config;

  /**
   *
   * @var routing
   */
  private $routing;

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

  public function __construct(config $config, routing $routing, session $session, i18n $i18n) {
    $this->args = array(
        'shConfig' => $config,
        'shRouting' => $routing,
        'shSession' => $session,
        'shI18n' => $i18n
    );
    $this->config = $config;
    $this->routing = $routing;
    $this->session = $session;
    $this->i18n = $i18n;
  }

  public function getConfig() {
    return $this->config;
  }

  public function getRouting() {
    return $this->routing;
  }

  public function getSession() {
    return $this->session;
  }

  public function getI18n() {
    return $this->i18n;
  }

  public function getArgs() {
    return $this->args;
  }

  public function getTemplate() {
    return $this->template;
  }

  public function getModule() {
    return $this->module;
  }

  public function getFormat() {
    return $this->format;
  }

  public function defineView($module, $template, $format) {
    $this->template = $template;
    $this->module = $module;
    $this->format = $format;
  }

}
