<script type="text/javascript">
    $(document).ready(function(){

        $("#cc").click(function(){
            clearConnection('<?php echo url_for("main/ajaxCache") ?>');
        });

    });
</script>

<h1>Espace administration</h1>


<div id="presentation">
    <p>Vous voici sur votre espace d'administration. <br /><br />
        Vous pourrez <b>modifier, ajouter ou supprimer </b>des produits et des catégories.</p>
    <p> Vous avez aussi un accès direct aux <b>statistiques</b> ainsi que la liste détaillée de tous les produits du pôle.</p>
</div>
<div id="allBarcode">
    <a href="<?php echo url_for('product/allPDF') ?>">
        Imprimer tous les codes-barres
    </a>
</div>
<div id="lien">Une personne n'apparait pas dans la liste d'attente quand elle se connecte ? <p id="cc">Cliquez ici</p></div>
