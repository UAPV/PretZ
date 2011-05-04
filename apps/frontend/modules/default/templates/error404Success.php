<?php //decorate_with(dirname(__FILE__).'/defaultLayout.php') ?>

<style type="text/css">
    .sfTMessageContainer {
        border: 2px solid red;
        -moz-border-radius: 3px;
        background-color:#F9BBBB;
        -moz-box-shadow: 2px 2px 0px 2px #ccc;
    }
</style>


<div class="sfTMessageContainer sfTAlert">
      <div class="sfTMessageWrap">
  <?php echo image_tag('/sf/sf_default/images/icons/cancel48.png', array('alt' => 'page not found', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>

    <h1>Oops! La page n'existe pas </h1>
    <h3>Vous vous êtes peut-être trompé d'adresse ?:)</h3>
  </div>
</div>
