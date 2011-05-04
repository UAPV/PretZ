<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Espace administration</title>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <?php use_stylesheet('admin.css') ?>
    <?php use_stylesheet('main.css') ?>
    <?php use_javascript('jquery.js') ?>
    <?php include_javascripts() ?>
    <?php include_stylesheets() ?>
    <?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne(); ?>
  </head>
  <body>
  <script type="text/javascript">
      function redirect()
      {
          $(location).attr('href',"http://pretz.univ-avignon.fr/emprunt/accueil");
      }
  </script>
  <div class="contentText" style="background-color: <?php echo $file->getBackColor(); ?>;">
    <div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>;">
        <h1>Espace admin</h1>
        <a href='<?php echo 'http://'.$file->getUrl() ?>'><?php echo image_tag("uploads/".$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')); ?></a>

        <div id="menuAdmin">
            <p><?php echo link_to('Accueil', 'main') ?></p>
            <p><?php echo link_to('Categories', 'category') ?></p>
            <p><?php echo link_to('Produits', 'product') ?></p>
            <p><?php echo link_to('Services', 'service') ?></p>
            <p><?php echo link_to('Options', 'options') ?></p>
            <p><?php echo link_to('Liste etudiants', 'liste') ?></p>

            <p><?php echo link_to('Membres & Droits', 'Administrateurs') ?></p>
            <p style="cursor: pointer;" onclick="javascript:redirect();">Retour aux emprunts</p>
        </div>
    </div>
      
      <div id="content">
        <?php echo $sf_content ?>
      </div>
   </div>
</body>
</html>
