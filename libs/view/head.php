<?php use soho\shConfig as config ?>
<?php use soho\shView as view ?>
<!DOCTYPE html>
<html lang="<?php echo config::getDefaultCulture() ?>">
  <head>
    <?php echo view::genTitle() ?>
    <?php echo view::genMetas() ?>
    <?php echo view::genFavicon() ?>
    <?php echo view::genStylesheet() ?>
    <?php echo view::genJavascript() ?>
  </head>
  <body>