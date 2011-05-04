<div class="listeMaterielContent">
    <div id="liens">
        <ul>
        <?php foreach($allCategs as $categ) {
                $cpt = 1; ?>
            <a href="#<?php echo $categ->getName(); ?>">
                <?php
                    if($cpt%2 == 1)
                        echo "<li class='left'>";
                    else
                        echo "<li class='right'>";
                ?>
                    <span><img src="/uploads/<?php echo $categ->getLogo() ?>" width="10%" style="border: none;"/></span>
                    <span style="text-transform:uppercase;text-decoration:underline;margin-bottom:5px;"><?php echo $categ->getName(); ?></span>
                </li>
            </a>
        <?php } ?>
        </ul>
    </div>

    <p style="font-size: 1em; color: #575E6B;font-style: italic;text-align: center;margin-bottom: -17px;margin-top:50px;">Cliquez sur le nom d'un produit pour réserver</p>
    <div id="listeMateriel">
        <?php foreach($allCategs as $categ) { ?>
            <a name="<?php echo $categ->getName(); ?>">
                <table border="1" class="tableCateg">
                    <caption>
                        <img src="/uploads/<?php echo $categ->getLogo() ?>" />
                        <?php echo $categ->getName(); ?>
                    </caption>
                    <tr>
                        <th>Produit</th>
                        <th>Description</th>
                        <th>Disponibilité</th>
                        <th>Retour</th>
                    </tr>
                    <?php $products = ProductQuery::getProducts($categ->getId()); ?>
                    <?php foreach($products as $prod){ ?>
                        <tr>
                            <td class="name">
                                <a href="http://glpi.univ-avignon.fr/index.php?redirect=plugin_uapvHelpdesk_1" target="_blank">
                                    <?php echo $prod->getName(); ?>
                                </a>
                            </td>
                            <td class="desc"><?php echo $prod->getDescription(); ?></td>
                            <td class="icon">
                                <?php
                                    if($prod->getState() == "0")
                                        echo image_tag('/images/green.png', array('alt' => 'Disponible'));
                                    else
                                        echo image_tag('/images/red.png', array('alt' => 'Non disponible'));
                                ?>
                            </td>
                            <td class="dateRetour"><?php echo $prod->getRetourPrevu(); ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </a>
        <?php } ?>
    </div>
</div>