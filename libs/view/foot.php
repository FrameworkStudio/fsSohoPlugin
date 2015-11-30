  <?php use soho\shSession as session ?>
  <?php use soho\shConfig as config ?>
  <?php if(config::getScope() === 'dev'): ?>
  <div id="mvcMain" class="shfDevelopmentBar">
    <i class="fa fa-leaf mvcPointer"></i> SHF <?php echo config::getSohoFrameworkVersion() ?> |
    <i class="fa fa-dashboard"></i> <?php echo number_format((memory_get_usage() / 1048576), '3', '.', '\'') ?> MB |
    <i class="fa fa-clock-o"></i> <?php echo number_format((microtime(true) - $GLOBALS['timeIni']), '4', '.', '\'') ?> seg.
    <?php if (session::getInstance()->hasAttribute('mvcDbQuery')): ?>
    | <i class="fa fa-database"></i> <?php echo session::getInstance()->getAttribute('mvcDbQuery') ?>
    <?php session::getInstance()->deleteAttribute('mvcDbQuery') ?>
    <?php endif ?>
  </div>
  <?php endif ?>
  <?php if(config::getScope() === 'dev'): ?>
  <div id="mvcIcon" class="shfDevelopmentBar">
    <i class="fa fa-leaf mvcPointer"></i> SHF <?php echo config::getSohoFrameworkVersion() ?>
  </div>
  <?php endif ?>
</body>
</html>
