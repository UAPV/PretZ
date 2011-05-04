<script type="text/javascript" language="javascript">
  $(document).ready(function(){

  var date = $("#date").val();
  var mois = $("#mois").val();
  var annee = $("#annee").val();
  $.jqplot.config.enablePlugins = true;

  $("#date").change(function()
  {
    var date = $("#date").val();
    var id = $("#listeCateg").val();
    $("#conteneur").html("");
    ajaxDateOneCateg(date,id,'<?php echo url_for("stat/ajaxWeekCateg")?>');
  });

  $("#mois").change(function()
  {
    var mois = $("#mois").val();
    var id = $("#listeCateg").val();
    $("#conteneur").html("");
    ajaxMoisOneCateg(mois,id,'<?php echo url_for("stat/ajaxMoisCateg")?>');
  });

  $("#annee").change(function()
  {
    var id = $("#listeCateg").val();
    $("#conteneur").html("");
    var annee = $("#annee").val();
    ajaxAnneeOneCateg(annee,id,'<?php echo url_for("stat/ajaxAnneeCateg")?>');
  });

  $("#laps").change(function()
  {
    var laps = $("#laps").val();
    if(laps != "vide")
    {
        var id = $("#listeCateg").val();
        $("#choixDate").hide();
        $("#choixMois").hide();
        $("#choixAnnee").hide();

        $("#conteneur").html("");

        if ( laps == 'semaine')
        {
          $("#choixDate").show();
          var date = $("#date").val();
          ajaxDateOneCateg(date,id,'<?php echo url_for("stat/ajaxWeekCateg")?>');
        }
        else if ( laps == 'mois')
        {
          $("#choixMois").show();
          var mois = $("#mois").val();
          ajaxMoisOneCateg(mois,id,'<?php echo url_for("stat/ajaxMoisCateg")?>');
        }
        else
        {
          $("#choixAnnee").show();
          var annee = $("#annee").val();
          ajaxAnneeOneCateg(annee,id,'<?php echo url_for("stat/ajaxAnneeCateg")?>');
        }
    }
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

<div id="graphe" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
   <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
        <span id='pro'>
            Catégorie : 
            <?php

                $pr = new sfWidgetFormPropelChoice(array(
                      'model' => 'Category'
                ));

                echo $pr->render('listeCateg');
            ?>
        </span>

        <span id="choixLaps">Laps de temps :
            <?php
                $w = new sfWidgetFormSelect(array(
                        'multiple' => false,
                        'choices'  => array('vide' => ' ','semaine' => 'semaine', 'mois' => 'mois', 'annee' => 'annee')
                    ));
                echo $w->render('laps','vide');
            ?>
        </span>

        <span id="choixDate">Semaine :

            <?php
            $oneweek = 60*60*24*7;
            $premier_jour = mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"));
            $d = $premier_jour;

               for($i=0;$i<5;$i++)
               {
                    $tabKey[$i] = date("d-m-Y",$d);
                    $tab[$i] = "du ".date("d-m-Y",$d)." au ".date("d-m-Y", $d + $oneweek);
                    $d -= $oneweek;
               }

            $w = new sfWidgetFormSelect(array(
                    'multiple' => false,
                    'choices'  => array($tabKey[0] => $tab[0],$tabKey[1] => $tab[1],$tabKey[2] => $tab[2],$tabKey[3] => $tab[3],$tabKey[4] => $tab[4]),
                ));
            echo $w->render('date');
            ?>
        </span>

            <span id="choixMois">Mois :

            <?php
            $year = date('Y');
            $w = new sfWidgetFormSelect(array(
                    'multiple' => false,
                    'choices'  => array('1' => 'janvier '.$year, '2' => 'février '.$year, '3' => 'mars '.$year, '4' => 'avril '.$year, '5' => 'mai '.$year, '6' => 'juin '.$year, '7' => 'juillet '.$year, '8' => 'aout '.$year, '9' => 'septembre '.$year, '10' => 'octobre '.$year, '11' => 'novembre '.$year, '12' => 'decembre '.$year),
                ));
            echo $w->render('mois');
            ?>
            </span>
            <span id="choixAnnee">Année :

            <?php
            $oneweek = 60*60*24*7;
            $premier_jour = mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"));
            $d = $premier_jour;

               for($i=0;$i<5;$i++)
               {
                    $tabKey[$i] = date("d-m-Y",$d);
                    $tab[$i] = "du ".date("d-m-Y",$d)." au ".date("d-m-Y", $d + $oneweek);
                    $d -= $oneweek;
               }

            $w = new sfWidgetFormSelect(array(
                    'multiple' => false,
                    'choices'  => array($year => $year, $year-1 => $year-1),
                ));
            echo $w->render('annee');
            ?>
            </span>
    </div>
    <div id="conteneur"></div>
</div>