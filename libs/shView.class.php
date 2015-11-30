<?php

namespace soho;

use FStudio\myConfig as config;
use soho\shSession as session;
use soho\shCacheManager as cacheManager;

/**
 * Description of shView
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shView {

  /**
   *
   * @var config
   */
  private $config;

  /**
   *
   * @var session
   */
  private $session;

  /**
   *
   * @var cacheManager
   */
  private $cache_manager;

  public function __construct(config $config, session $session, cacheManager $cache_manager) {
    $this->config = $config;
    $this->session = $session;
    $this->cache_manager = $cache_manager;
  }

  public function includeHandlerMessage() {
    include_once $this->config->getPath() . 'libs/vendor/soho/view/handlerMessage.php';
  }

  public function getMessageError($key) {
    include $this->config->getPath() . 'libs/vendor/soho/view/messageError.php';
  }

  public function includePartial($partial, $variables = null) {
    if ($variables !== null and is_array($variables) and count($variables) > 0) {
      extract($variables);
    }
    include_once $this->config->getPath() . 'view/' . $partial . '.php';
  }

  public function includeComponent($module, $component, $variables = array()) {
    include_once $this->config->getPath() . 'controller/' . $module . '/' . $component . 'Component.class.php';
    $componentClass = $component . 'Component';
    $objComponent = new $componentClass($variables);
    $objComponent->component();
    $objComponent->setArgs((array) $objComponent);
    $objComponent->renderComponent();
  }

  public function genMetas() {
    $module = $this->session->getModule();
    $action = $this->session->getAction();
    $metas = '';
    $includes = $this->cache_manager->loadYaml($this->config->getPath() . 'config/view.yml', 'viewYaml');
    if (isset($includes['all']['meta'])) {
      foreach ($includes['all']['meta'] as $include) {
        $metas .= '<meta ' . $include . '>';
      }
    }

    if (isset($includes['all']['link'])) {
      foreach ($includes['all']['link'] as $include) {
        $metas .= '<link ' . $include . '>';
      }
    }

    if (isset($includes[$module][$action]['meta'])) {
      $this->session->setFlash('meta' . $module . '.' . $action, true);
      foreach ($includes[$module][$action]['meta'] as $include) {
        if (is_array($include) === true and $this->session->hasFlash('meta' . $include[0]) === false) {
          $this->session->setFlash('meta' . $include[0], true);
          $entity = explode('.', $include[0]);
          $metas = $this->genMetaLink($includes, $entity, $metas, 'meta');
        } else if (is_array($include) === false) {
          $metas .= '<meta ' . $include . '>';
        }
      }
    }

    if (isset($includes[$module][$action]['link'])) {
      $this->session->setFlash('link' . $module . '.' . $action, true);
      foreach ($includes[$module][$action]['link'] as $include) {
        if (is_array($include) === true and $this->session->hasFlash('link' . $include[0]) === false) {
          $this->session->setFlash('link' . $include[0], true);
          $entity = explode('.', $include[0]);
          $metas = $this->genMetaLink($includes, $entity, $metas, 'link');
        } else if (is_array($include) === false) {
          $metas .= '<link ' . $include . '>';
        }
      }
    }

    return $metas;
  }

  private function genMetaLink($includes, $entity, $metaLink, $label) {
    foreach ($includes[$entity[0]][$entity[1]][$label] as $include) {
      if (is_array($include) === true and $this->session->hasFlash($label . $include[0]) === false) {
        $this->session->setFlash($label . $include[0], true);
        $entity2 = explode('.', $include[0]);
        $metaLink = $this->genMetaLink($includes, $entity2, $metaLink, $label);
      } else if (is_array($include) === false) {
        $metaLink .= '<' . $label . ' ' . $include . '>';
      }
    }
    return $metaLink;
  }

  public function genStylesheet() {
    $module = $this->session->getModule();
    $action = $this->session->getAction();
    $stylesheet = '';
    $includes = $this->cache_manager->loadYaml($this->config->getPath() . 'config/view.yml', 'viewYaml');
    foreach ($includes['all']['stylesheet'] as $include) {
      $stylesheet .= '<link rel="stylesheet" href="' . $this->config->getUrlBase() . 'css/' . $include . '">';
    }
    if (isset($includes[$module][$action]['stylesheet'])) {
      $this->session->setFlash('css' . $module . '.' . $action, true);
      foreach ($includes[$module][$action]['stylesheet'] as $include) {
        if (is_array($include) === true and $this->session->hasFlash('css' . $include[0]) === false) {
          $this->session->setFlash('css' . $include[0], true);
          $entity = explode('.', $include[0]);
          $stylesheet = $this->genStylesheetLink($includes, $entity, $stylesheet);
        } else if (is_array($include) === false) {
          $stylesheet .= '<link rel="stylesheet" href="' . $this->config->getUrlBase() . 'css/' . $include . '">';
        }
      }
    }
    return $stylesheet;
  }

  private function genStylesheetLink($includes, $entity, $stylesheet) {
    foreach ($includes[$entity[0]][$entity[1]]['stylesheet'] as $include) {
      if (is_array($include) === true and $this->session->hasFlash('css' . $include[0]) === false) {
        $this->session->setFlash('css' . $include[0], true);
        $entity2 = explode('.', $include[0]);
        $stylesheet = $this->genStylesheetLink($includes, $entity2, $stylesheet);
      } else if (is_array($include) === false) {
        $stylesheet .= '<link rel="stylesheet" href="' . $this->config->getUrlBase() . 'css/' . $include . '">';
      }
    }
    return $stylesheet;
  }

  public function genJavascript() {
    $module = $this->session->getModule();
    $action = $this->session->getAction();
    $javascript = '';
    $includes = $this->cache_manager->loadYaml($this->config->getPath() . 'config/view.yml', 'viewYaml');
    foreach ($includes['all']['javascript'] as $include) {
      $javascript .= '<script src="' . $this->config->getUrlBase() . 'js/' . $include . '"></script>';
    }
    if (isset($includes[$module][$action]['javascript'])) {
      $this->session->setFlash('js' . $module . '.' . $action, true);
      foreach ($includes[$module][$action]['javascript'] as $include) {
        if (is_array($include) === true and $this->session->hasFlash('js' . $include[0]) === false) {
          $this->session->setFlash('js' . $include[0], true);
          $entity = explode('.', $include[0]);
          $javascript = $this->genJavascriptLink($includes, $entity, $javascript);
        } else if (is_array($include) === false) {
          $javascript .= '<script src="' . $this->config->getUrlBase() . 'js/' . $include . '"></script>';
        }
      }
    }
    return $javascript;
  }

  private function genJavascriptLink($includes, $entity, $javascript) {
    foreach ($includes[$entity[0]][$entity[1]]['javascript'] as $include) {
      if (is_array($include) === true and $this->session->hasFlash('js' . $include[0]) === false) {
        $this->session->setFlash('js' . $include[0], true);
        $entity2 = explode('.', $include[0]);
        $javascript = $this->genJavascriptLink($includes, $entity2, $stylesheet);
      } else if (is_array($include) === false) {
        $javascript .= '<script src="' . $this->config->getUrlBase() . 'js/' . $include . '"></script>';
      }
    }
    return $javascript;
  }

  /**
   * Funcion publica que incluye un favicon en las vistas del sistema
   * @author Leonardo Betancourt Caicedo <leobetacai@gmail.com>
   * @return string
   */
  public function genFavicon() {
    $includes = $this->cache_manager->loadYaml($this->config->getPath() . 'config/view.yml', 'viewYaml');
    $favicon = '<link rel="icon" href="' . $this->config->getUrlBase() . 'img/' . $includes['all']['favicon'] . '" type="image/x-icon">';
    return $favicon;
  }

  /**
   * Funcion diseñada para integrar un titulo a cada vista de el sistema de el portal
   * @author Leonardo Betancourt Caicedo <leobetacai@gmail.com>
   * @return string
   */
  public function genTitle() {
    $module = $this->session->getModule();
    $action = $this->session->getAction();
    $title = '';
    $includes = $this->cache_manager->loadYaml($this->config->getPath() . 'config/view.yml', 'viewYaml');
    if (isset($includes[$module][$action]['title'])) {
      $title = '<title>' . $includes[$module][$action]['title'] . '</title>';
    } else if (isset($includes['all']['title'])) {
      $title = '<title>' . $includes['all']['title'] . '</title>';
    }
    return $title;
  }

  public function renderComponent($component, $arg = array()) {
    if (isset($component)) {
      if (count($arg) > 0) {
        extract($arg);
      }
      include $this->config->getPath() . "view/$component.php";
    }
  }

  public function renderHTML($module, $template, $typeRender, $arg = array()) {
    if (isset($module) and isset($template)) {
      if (count($arg) > 0) {
        extract($arg);
      }
      switch ($typeRender) {
        case 'html':
          header($this->config->getHeaderHtml());
          //include_once $this->config->getPath() . 'lib/vendor/soho/view/head.php';
          include_once $this->config->getPath() . "view/$module/$template.html.php";
          //include_once $this->config->getPath() . 'lib/vendor/soho/view/foot.php';
          break;
        case 'json':
          header($this->config->getHeaderJson());
          include_once $this->config->getPath() . "view/$module/$template.json.php";
          break;
        case 'pdf':
          //header($this->config->getHeaderPdf());
          include_once $this->config->getPath() . "view/$module/$template.pdf.php";
          break;
        case 'javascript':
          header($this->config->getHeaderJavascript());
          include_once $this->config->getPath() . "view/$module/$template.js.php";
          break;
        case 'xml':
          header($this->config->getHeaderXml());
          include_once $this->config->getPath() . "view/$module/$template.xml.php";
          break;
        case 'excel2003':
          header($this->config->getHeaderExcel2003());
          include_once $this->config->getPath() . "view/$module/$template.xls.php";
          break;
        case 'excel2007':
          header($this->config->getHeaderExcel2007());
          include_once $this->config->getPath() . "view/$module/$template.xlsx.php";
          break;
      }
    }
  }

}
