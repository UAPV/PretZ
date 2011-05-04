<script type="text/javascript">
      $(document).ready(function()
      {
             $("#login").click(function()
             {
                $("#login").val("");
             })

             $("#liste tr").click(function()
             {
                var login = $("#liste tr").attr("id");
                alert(login);
                $( "#scanDialog" ).dialog({
                     modal: true,
                     buttons:
                     {
                         Fermer: function()
                        {
                             $("#scanDialog" ).dialog('close');
                        }
                     }
                });
                document.getElementById('mdpAdmin').focus();
             })

             $("#liste tr:even").css("background-color", "#D8F6F6");

             $("#mdpAdmin").bind('keypress', function(e) {
               var id = $("#carteId").val();

               if (e.keyCode == '13')
               {
                    loadUserAdmin(id,'<?php echo url_for('emprunt/carte') ?>');
               }
             })
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
    #bande
    {
        background-color: <?php echo $file->getMenuColor() ?>;
    }
</style>


<div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>; height:120px;">
     <h1>Authentification </h1>
     <a href=<?php echo 'http://'.$file->getUrl() ?>><?php echo image_tag('/images/uploads/'.$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')); ?></a>
     <div id="test"></div>

    <div id="choixAuth">
        <p>Veuillez vous authentifier SVP</p>
    </div>

    <div id="bande">
        <a href="<?php echo url_for('emprunt/accueil') ?>">
            <div id="login" style="margin-left: 320px;" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
                <center><div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">J'ai mes identifiants</div></center>
                <div class="message">
                    <br /><br />
                    Rentrer mon login et mon mot de passe
                </div>
            </div>
        </a>
    </div>
</div>

