<script type="text/javascript" language="javascript">
  $(document).ready(function()
  {
      var date = $("#date").val();
      $("#maxJokers").hide();

      $.jqplot.config.enablePlugins = true;

      ajaxDate(date,'<?php echo url_for("stat/ajax")?>');
      refreshJ('<?php echo url_for("stat/ajaxrefreshJoker")?>');
      refreshP('<?php echo url_for("stat/ajaxrefreshProduct")?>');

      $("#date").change(function()
      {
        var date = $("#date").val();
        $("#conteneur").html("");
        ajaxDate(date,'<?php echo url_for("stat/ajax")?>');
      });
  })
</script>

<?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne(); ?>
<style type="text/css">
    .ui-widget-header
    {
       background-color: <?php echo $file->getColorMessage() ?>;
    }
</style>

<div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>">
    <h1>Statistiques</h1>
    <?php echo link_to(image_tag('uploads/'.$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')), 'http://'.$file->getUrl()) ?>
    <div id="test"></div>
    <div id="menuEmprunt">
        <p><a href="<?php echo url_for('stat/index') ?>">Accueil</a></p>
        <p><a href="<?php echo url_for('stat/categ') ?>">Catégorie</a></p>
        <p><a href="<?php echo url_for('stat/produit') ?>">Produit</a></p>
        <p><a href="<?php echo url_for('stat/sortie') ?>">Sorties</a></p>
        <p><a href="<?php echo url_for('stat/historique') ?>">Historique</a></p>
        <p><a href="<?php echo url_for('emprunt/index') ?>">Retour emprunt</a></p>
    </div>
</div>

<div id="graphe" style="width: 850px;" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
            Choisissez une semaine :  
          <?php
            $oneweek = 60*60*24*7;
            $premier_jour = mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"));
            $d = $premier_jour;

               for($i=0;$i<5;$i++)
               {
                    $tabKey[$i] = date("Y-m-d",$d)."+".date("Y-m-d", $d + $oneweek);
                    $tab[$i] = "du ".date("d-m-Y",$d)." au ".date("d-m-Y", $d + $oneweek);
                    $d -= $oneweek;
               }


            $w = new sfWidgetFormSelect(array(
                    'multiple' => false,
                    'choices'  => array($tabKey[0] => $tab[0],$tabKey[1] => $tab[1],$tabKey[2] => $tab[2],$tabKey[3] => $tab[3],$tabKey[4] => $tab[4]),
                ));
            echo $w->render('date');
          ?>
        </div>
    <div id="conteneur"></div>
</div>

<div id="pie">
    <div id="pieChart"></div>
    <div id="pieChart2"></div>
    <div style="clear:both;"></div>
</div>    
    
<div id="alert">
    <div id="maxProduct"></div>
    <div id="maxJokers"></div>
</div>