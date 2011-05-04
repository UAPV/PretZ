<script type="text/javascript">
      $(document).ready(function()
      {
        $("#login").click(function(){
          $("#login").val("");
        })
          
        $("#carte").click(function(){
            $( "#scanDialog" ).dialog({
                 modal: true,
                 buttons:
                 {
                     Fermer: function()
                    {
                         $("#scanDialog" ).dialog('close')
                    }
                 }
            });
            document.getElementById('carteId').focus();
        })

        $("#scanDialog").bind('keypress', function(e) {
           var id = $("#carteId").val();

           if (e.keyCode == '13')
           {
               redirectUser(id,'<?php echo url_for('utilisateur/carte') ?>');
           }
        })

      })
</script>


<?php

/* We verify if this IP address is allowed to access to PretZ */
if(!OptionIpQuery::exists($_SERVER["REMOTE_ADDR"]))
{
    echo "<p>Vous n'avez pas le droit d'accéder à cette page</p>";
}
else
{
    $file = TemplateQuery::create()
       ->filterByActive('1')
       ->findOne();
?>
        
<style type="text/css">
    .ui-widget-header
    {
        background-color: <?php echo $file->getColorMessage() ?>;
    }


    #bande
    {
        background-color: <?php echo $file->getMenuColor() ?>;
    }
</style>
        
<div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>; height:120px;">
     <h1>Authentification </h1>
     <a href=<?php echo 'http://'.$file->getUrl() ?>><?php echo image_tag('/images/uploads/'.$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')); ?></a>
     <div id="banniere"></div>
     <p id="choixAuth">Veuillez choisir un moyen de vous authentifier SVP</p>

    <div id="bande">
        <a href="<?php echo url_for('utilisateur/edit') ?>">
            <span id="login" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix titreDiv">J'ai mes identifiants</div>
                <div class="message" style="padding-top:50px;">Rentrer mon login et mon mot de passe</div>
            </span>
        </a>

        <span id="carte" class="survol ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix titreDiv">J'ai ma carte de bibliothèque</div>
            <div class="message" style="padding-top:50px;">Scanner ma carte</div>
        </span>
    </div>
</div>

<div id="scanDialog">
    <p>Scannez votre carte avec le lecteur SVP</p>
    <input type="text" id="carteId" />
</div>

<?php }?>