<script type="text/javascript" language="javascript">
    $(document).ready(function(){
        $(".historique tr:even").css("background-color", "#D8F6F6");
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

    .today
    {
       background-color: #F7F3A8;
    }

    td
    {
        padding: 10px !important;
    }
</style>

<div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>">
    <h1>Matériels sortis</h1>
    <?php echo link_to(image_tag('uploads/'.$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')), 'http://'.$file->getUrl()) ?>
    <div id="test"></div>
    <div id="menuEmprunt" style="">
        <p><a href="<?php echo url_for('stat/index') ?>">Accueil</a></p>
        <p><a href="<?php echo url_for('stat/categ') ?>">Catégorie</a></p>
        <p><a href="<?php echo url_for('stat/produit') ?>">Produit</a></p>
        <p><a href="<?php echo url_for('stat/sortie') ?>">Sorties</a></p>
        <p><a href="<?php echo url_for('stat/historique') ?>">Historique</a></p>
        <p><a href="<?php echo url_for('emprunt/index') ?>">Retour emprunt</a></p>
    </div>
</div>

<table class="ui-dialog ui-widget ui-widget-content ui-corner-all relative historique " style="width: auto;margin-left: 50px;">
    <caption>
        <tr>
            <th>Emprunteur</th>
            <th>Produit</th>
            <th>Administrateur</th>
            <th>Date emprunt</th>
            <th>Date prévu</th>
        </tr>
    </caption>

    <?php
        foreach($lend_pager->getResults() as $lend)
        {
           $objLend = LendQuery::create()->filterById($lend->getIdLend())->findOne();
            
           if($objLend->getRetour() == '1')
           {
               $objPro = ProductQuery::create()->filterById($lend->getIdProduct())->findOne();
               $objUser = UserQuery::create()
                        ->useLendQuery()
                            ->filterById($lend->getIdLend())
                        ->endUse()
                        ->findOne();
               $objAdmin = AdminQuery::create()
                       ->useLendQuery()
                           ->filterById($lend->getIdLend())
                       ->endUse()
                       ->findOne();

               if($objPro->getRetourPrevu() != null)
               {
                   if($objPro->getRetourPrevu() == date("Y-m-d") && $objLend->getRetourDate() == null)
                   {
                       echo "<tr class='today'><td>".$objUser->getName()."</td><td>".$objPro->getName()."( ".$objPro->getBarcode()." )</td><td>".$objAdmin->getName()."</td><td>".LendQuery::dateFR($objLend->getCreatedAt())."</td>";
                       echo "<td>".LendQuery::date($objPro->getRetourPrevu())."</td></tr>";
                   }
                   else
                   {
                       echo "<tr><td>".$objUser->getName()."</td><td>".$objPro->getName()."( ".$objPro->getBarcode()." )</td><td>".$objAdmin->getName()."</td><td>".LendQuery::dateFR($objLend->getCreatedAt())."</td>";
                       echo "<td>".LendQuery::date($objPro->getRetourPrevu())."</td></tr>";
                   }
               }
               else
                   echo "<tr><td>".$objUser->getName()."</td><td>".$objPro->getName()."( ".$objPro->getBarcode()." )</td><td>".$objAdmin->getName()."</td><td>".LendQuery::dateFR($objLend->getCreatedAt())."</td><td></td></tr>";
           }
        }
    ?>
</table>

<div id="pagination">
   <?php if ($lend_pager->haveToPaginate()): ?>

      <?php echo link_to(image_tag("first.png", array("title" => "Première page", "alt" => "Première page")), 'stat/sortie?page=1') ?>
      <?php echo link_to(image_tag("previous.png", array("title" => "Précédent", "alt" => "Précédent")), 'stat/sortie?page='.$lend_pager->getPreviousPage()) ?>

      <?php foreach ($lend_pager->getLinks() as $page): ?>

         <?php echo link_to_unless($page == $lend_pager->getPage(), $page, 'stat/sortie?page='.$page) ?>
         <?php echo ($page != $lend_pager->getCurrentMaxLink()) ? '-' : '' ?>

      <?php endforeach; ?>

      <?php echo link_to(image_tag("next.png", array("title" => "Suivant", "alt" => "Suivant")), 'stat/sortie?page='.$lend_pager->getNextPage()) ?>
      <?php echo link_to(image_tag("last.png", array("title" => "Dernière page", "alt" => "Dernière page")), 'stat/sortie?page='.$lend_pager->getLastPage()) ?>

   <?php endif; ?>
</div>