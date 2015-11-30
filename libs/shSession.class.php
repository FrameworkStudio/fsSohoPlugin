<?php

namespace soho;

/**
 * Description of sessionClass
 *
 * @author Julian Lasso <ingeniero.julianlasso@gmail.com>
 */
class shSession {

  const PREF = 'sohoPlugin';

  public function getModule() {
    return ($this->hasFlash(self::PREF . 'Module')) ? $this->getFlash(self::PREF . 'Module') : false;
  }

  public function setModule($module) {
    $this->setFlash(self::PREF . 'Module', $module);
  }

  public function getAction() {
    return ($this->hasFlash(self::PREF . 'Action')) ? $this->getFlash(self::PREF . 'Action') : false;
  }

  public function setAction($action) {
    $this->setFlash(self::PREF . 'Action', $action);
  }

  public function getAttribute($attribute) {
    return (isset($_SESSION[self::PREF . 'Attribute'][$attribute])) ? $_SESSION[self::PREF . 'Attribute'][$attribute] : false;
  }

  public function setAttribute($attribute, $value) {
    $_SESSION[self::PREF . 'Attribute'][$attribute] = $value;
  }

  public function hasAttribute($attribute) {
    return isset($_SESSION[self::PREF . 'Attribute'][$attribute]);
  }

  public function deleteAttribute($attribute) {
    unset($_SESSION[self::PREF . 'Attribute'][$attribute]);
  }

  public function getFlash($flash) {
    return (isset($GLOBALS[self::PREF . 'Flash'][$flash])) ? $GLOBALS[self::PREF . 'Flash'][$flash] : false;
  }

  public function setFlash($flash, $value) {
    $GLOBALS[self::PREF . 'Flash'][$flash] = $value;
  }

  public function hasFlash($flash) {
    return isset($GLOBALS[self::PREF . 'Flash'][$flash]);
  }

  public function getCredentials() {
    return ($this->hasAttribute(self::PREF . 'Credentials')) ? $this->getAttribute(self::PREF . 'Credentials') : false;
  }

  public function setCredential($credential) {
    $_SESSION[self::PREF . 'Credentials'][] = $credential;
  }

  /**
   *
   * @param array $credentials
   */
  public function setCredentials($credentials) {
    $this->setAttribute(self::PREF . 'Credentials', $credentials);
  }

  public function hasCredential($credential) {
    if ($this->hasAttribute(self::PREF . 'Credentials')) {
      return (array_search($credential, $this->getAttribute(self::PREF . 'Credentials'), 'true') === false) ? false : true;
    }
    return false;
  }

  public function deleteCredentials() {
    unset($_SESSION[self::PREF . 'Credentials']);
  }

  /**
   * Define si usuario está o no autenticado en el sistema
   * @param boolean $authenticate
   */
  public function setUserAuthenticate($authenticate) {
    $this->setAttribute(self::PREF . 'UserAuthenticate', $authenticate);
  }

  public function isUserAuthenticated() {
    return $this->getAttribute(self::PREF . 'UserAuthenticate');
  }

  /**
   * Devuelve el formato a usar definido en el routing (html, json, xml, pdf, js)
   * @return string
   */
  public function getFormatOutput() {
    return $this->getFlash(self::PREF . 'FormatOutput');
  }

  public function setFormatOutput($format_output) {
    return $this->setFlash(self::PREF . 'FormatOutput', $format_output);
  }

  public function getLoadFiles() {
    return $this->getFlash(self::PREF . 'LoadFiles');
  }

  public function setLoadFiles($load_files) {
    return $this->setFlash(self::PREF . 'LoadFiles', $load_files);
  }

  /**
   *
   * @return array from Exception|PDOException
   */
  public function getError($key = null) {
    $answer = $_SESSION[self::PREF . 'Error'];
    if ($key !== null) {
      $answer = $answer[$key];
      unset($_SESSION[self::PREF . 'Error'][$key]);
    } else {
      unset($_SESSION[self::PREF . 'Error']);
    }
    return $answer;
  }

  /**
   *
   * @param Exception|PDOException $error
   */
  public function setError($error, $key = null) {
    if ($key !== null) {
      $_SESSION[self::PREF . 'Error'][$key] = $error;
    } else {
      $_SESSION[self::PREF . 'Error'][] = $error;
    }
  }

  public function hasError($key = null) {
    if ($key !== null) {
      $rsp = (isset($_SESSION[self::PREF . 'Error'][$key])) ? true : false;
    } else {
      $rsp = (isset($_SESSION[self::PREF . 'Error']) and count($_SESSION[self::PREF . 'Error']) > 0) ? true : false;
    }
    return $rsp;
  }

  public function deleteError($key) {
    unset($_SESSION[self::PREF . 'Error'][$key]);
  }

  public function deleteErrorStack() {
    unset($_SESSION[self::PREF . 'Error']);
  }

  /**
   *
   * @return array
   */
  public function getInformation() {
    $answer = $_SESSION[self::PREF . 'Information'];
    unset($_SESSION[self::PREF . 'Information']);
    return $answer;
  }

  /**
   *
   * @param string $information
   */
  public function setInformation($information) {
    $_SESSION[self::PREF . 'Information'][] = $information;
  }

  public function hasInformation() {
    return (isset($_SESSION[self::PREF . 'Information']) and count($_SESSION[self::PREF . 'Information']) > 0) ? true : false;
  }

  public function deleteInformationStack() {
    unset($_SESSION[self::PREF . 'Information']);
  }

  /**
   *
   * @return array
   */
  public function getSuccess() {
    $answer = $_SESSION[self::PREF . 'Success'];
    unset($_SESSION[self::PREF . 'Success']);
    return $answer;
  }

  /**
   *
   * @param string $success
   */
  public function setSuccess($success) {
    $_SESSION[self::PREF . 'Success'][] = $success;
  }

  public function hasSuccess() {
    return (isset($_SESSION[self::PREF . 'Success']) and count($_SESSION[self::PREF . 'Success']) > 0) ? true : false;
  }

  public function deleteSuccessStack() {
    unset($_SESSION[self::PREF . 'Success']);
  }

  /**
   *
   * @return array
   */
  public function getWarning() {
    $answer = $_SESSION[self::PREF . 'Warning'];
    unset($_SESSION[self::PREF . 'Warning']);
    return $answer;
  }

  /**
   *
   * @param string $warning
   */
  public function setWarning($warning) {
    $_SESSION[self::PREF . 'Warning'][] = $warning;
  }

  public function hasWarning() {
    return (isset($_SESSION[self::PREF . 'Warning']) and count($_SESSION[self::PREF . 'Warning']) > 0) ? true : false;
  }

  public function deleteWarningStack() {
    unset($_SESSION[self::PREF . 'Warning']);
  }

  public function getFirstCall() {
    return $this->getAttribute(self::PREF . 'FirstCall');
  }

  public function setFirstCall($first_call) {
    $this->setAttribute(self::PREF . 'FirstCall', $first_call);
  }

  public function hasFirstCall() {
    return $this->hasAttribute(self::PREF . 'FirstCall');
  }

  public function getUserId() {
    return $this->getAttribute(self::PREF . 'UserId');
  }

  public function setUserId($id) {
    $this->setAttribute(self::PREF . 'UserId', $id);
  }

  public function hasUserId() {
    return $this->hasAttribute(self::PREF . 'UserName');
  }

  public function getUserName() {
    return $this->getAttribute(self::PREF . 'UserName');
  }

  public function setUserName($name_user) {
    $this->setAttribute(self::PREF . 'UserName', $name_user);
  }

  public function getCache($cache) {
    return $_SESSION[self::PREF . 'Cache'][$cache];
  }

  public function setCache($cache, $value) {
    $_SESSION[self::PREF . 'Cache'][$cache] = $value;
  }

  public function hasCache($cache) {
    return (isset($_SESSION[self::PREF . 'Cache'][$cache])) ? true : false;
  }

  public function deleteCache($cache = null) {
    if ($cache !== null) {
      unset($_SESSION[self::PREF . 'Cache'][$cache]);
    } else {
      unset($_SESSION[self::PREF . 'Cache']);
    }
  }

  public function getDefaultCulture() {
    return $this->getAttribute(self::PREF . 'DefaultCulture');
  }

  public function setDefaultCulture($default_culture) {
    $this->setAttribute(self::PREF . 'DefaultCulture', $default_culture);
  }

  public function hasDefaultCulture() {
    return $this->hasAttribute(self::PREF . 'DefaultCulture');
  }

}
