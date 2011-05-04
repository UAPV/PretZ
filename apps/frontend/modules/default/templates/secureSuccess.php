<?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne(); ?>

<style type="text/css">
    .ui-widget-header
    {
        background-color: <?php echo $file->getColorMessage() ?>;
    }
</style>
                
<div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>; height:120px;">
    <h1> Impossible d'accéder à la page demandée</h1>
</div>

<div style="margin-top: 200px; margin-left: 350px; border: 2px solid red;" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Vous n'avez pas les droits</div>

    <dl class="sfTMessageInfo">
      <dt>Des droits sont nécessaires pour accéder à cette page.</dt>

      <dt>Que voulez-faire maintenant ?</dt>
      <dd>
        <ul class="sfTIconList" style="padding-top: 20px;">
          <li class="sfTLinkMessage">
              <a href="<?php echo url_for("authentication/logout")?>">Revenir à la page d'accueil</a>
          </li>
        </ul>
      </dd>
           <?php echo image_tag('/sf/sf_default/images/icons/lock48.png', array('alt' => 'credentials required', 'class' => 'sfTMessageIcon', 'size' => '48x48')) ?>

    </dl>
</div>
