<?php

namespace soho;

use soho\shSession as session;
use FStudio\myConfig as config;
use Symfony\Component\Yaml\Yaml;
use PDOException as exception;

/**
 * Description of cacheManagerClass
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shCacheManager {
  
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
  
  public function __construct(config $config, session $session) {
    $this->config = $config;
    $this->session = $session;
  }

  /**
   * Carga un archivo yml y lo convierte en un array, el resultado es almacenado
   * en cache con el nombre indicado en $index
   * @param string $yaml Dirección del archivo yml a convertir en array
   * @param string $index Nombre a utilizar en Cache para almacenar el resultado
   * @return array Resultado de la conversión del archivo yml indicado a un array
   * @throws \PDOException
   */
  public function loadYaml($yaml, $index) {
    try {
      if (($this->session->hasCache($index) and $this->config->getScope() === 'prod') or ( $this->session->hasCache($index) and $this->config->getScope() === 'dev')) {
        $answer = $this->session->getCache($index);
      } else {
        $answer = Yaml::parse(file_get_contents($yaml));
        $this->session->setCache($index, $answer);
        if ($this->config->getScope() === 'dev') {
          $this->session->setFlash(session::PREF . 'CacheFlag', true);
        }
      }
      return $answer;
    } catch (exception $exc) {
      throw $exc;
    }
  }

  public function __destruct() {
    if ($this->session->hasFlash(session::PREF . 'CacheFlag') === true and $this->config->getScope() === 'dev') {
      $this->session->deleteCache();
    }
  }

}
