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

    .sousTab
    {
       width: 100%;
       background: transparent;
    }

    .sousTab tr
    {
        height: 10px;
        border-bottom: 1px solid #ccc;
    }

    .sousTab td
    {
        border: none !important;
    }
</style>

<div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>">
    <h1>Historique</h1>
    <?php echo link_to(image_tag('uploads/'.$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')), 'http://'.$file->getUrl()) ?>
    <div id="test"></div>
    <div id="menuEmprunt">
        <p><a href="<?php echo url_for('stat/index') ?>">Accueil</a></p>
        <p><a href="<?php echo url_for('stat/categ') ?>">Categorie</a></p>
        <p><a href="<?php echo url_for('stat/produit') ?>">Produit</a></p>
        <p><a href="<?php echo url_for('stat/sortie') ?>">Sorties</a></p>
        <p><a href="<?php echo url_for('stat/historique') ?>">Historique</a></p>
        <p><a href="<?php echo url_for('emprunt/index') ?>">Retour emprunt</a></p>
    </div>
</div>

<table class="ui-dialog ui-widget ui-widget-content ui-corner-all relative historique" style="font-size: 1em;">
    <caption>
        <tr>
            <th>Emprunteur</th>
            <th>Administrateur</th>
            <th>Produits</th>
            <th>Fini</th>
        </tr>
    </caption>

    <?php
        foreach($lend_pager->getResults() as $lend)
        {
           $tabProduct = ProductQuery::create()->useLendProductQuery()->filterByIdLend($lend->getId())->endUse()->find();

           $objUser = UserQuery::create()
                         ->filterById($lend->getIdUser())
                    ->findOne();

           $objAdmin = AdminQuery::create()->filterById($lend->getIdAdmin())->findOne();


           echo "<tr><td>".$objUser->getName()."</td><td>".$objAdmin->getName()."</td><td>";
           echo "<table class='sousTab'>";
           foreach($tabProduct as $objPro)
           {
                if($objPro->getRetourPrevu() != NULL)
                        echo "<tr class='today'>";
                else
                        echo "<tr>";
                echo "<td class='nameProd'>".$objPro->getName()."</td>";
                $monLendProd = LendProductQuery::create()->filterByIdLend($lend->getId())->filterByIdProduct($objPro->getId())->findOne();
                if($monLendProd->getState() == '0')
                        echo "<td class='date'>".LendQuery::dateFR($monLendProd->getDateRetour())."</td><td class='img'><img src='/images/ok.png' alt='' /></td>";
                else
                        echo "<td class='date'></td><td class='img'><img src='/images/cross.png' alt='' /></td>";
           }
           echo "</tr></table></td>";

           if($lend->getRetour() == '1')
                echo "<td class='fini'><img src='/images/cross.png' /></td></tr>";
           else
                echo "<td class='fini'><img src='/images/ok.png' /></td></tr>";
         }
    ?>
</table>
<div id="pagination">
   <?php if ($lend_pager->haveToPaginate()): ?>

      <?php echo link_to(image_tag("first.png", array("title" => "Première page", "alt" => "Première page")), 'stat/historique?page=1') ?>
      <?php echo link_to(image_tag("previous.png", array("title" => "Précédent", "alt" => "Précédent")), 'stat/historique?page='.$lend_pager->getPreviousPage()) ?>

      <?php foreach ($lend_pager->getLinks() as $page): ?>

         <?php echo link_to_unless($page == $lend_pager->getPage(), $page, 'stat/historique?page='.$page) ?>
         <?php echo ($page != $lend_pager->getCurrentMaxLink()) ? '-' : '' ?>

      <?php endforeach; ?>

      <?php echo link_to(image_tag("next.png", array("title" => "Suivant", "alt" => "Suivant")), 'stat/historique?page='.$lend_pager->getNextPage()) ?>
      <?php echo link_to(image_tag("last.png", array("title" => "Dernière page", "alt" => "Dernière page")), 'stat/historique?page='.$lend_pager->getLastPage()) ?>

   <?php endif; ?>
</div>
