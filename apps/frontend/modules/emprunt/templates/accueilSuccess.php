<script type="text/javascript">
   $(document).ready(function()
   {
        var dateRetour = new Array();
        var cptDate = 0;
        loadIframe();

        var id = $("#listeAttente tbody tr:first-child").attr('id');
        $("#uidUser").val(id);
        refresh('<?php echo url_for("emprunt/ajaxRefresh")?>','<?php echo url_for("emprunt/ajaxLoadBasketUser")?>','<?php echo url_for("emprunt/ajaxLoadUser")?>','<?php echo url_for("emprunt/ajaxRemoveUser")?>');

        $("#inter_valid").click(function()
        {
            loadIntervenant('<?php echo url_for("emprunt/ajaxIntervenant")?>');
            document.getElementById('produit').focus();
            $("#name").val("");
        })

        $("#retour").click(function()
        {
           $( "#dialog:ui-dialog" ).dialog( "destroy" );
           $("#listeRetour" ).dialog({
                modal: false,
                zIndex: 3000,
                title: "Retour de produits",
                buttons:
                {
                    Fermer: function()
                    {
                        $("#listeProductReturn tr").html("");
                        $("#listeRetour" ).dialog('close');
                        document.getElementById('produit').focus();
                    }
                }
           })

            $('#produitRetour').bind('keypress', function(e)
            {
                   var product = $("#produitRetour").val();
                   if (e.keyCode == '13')
                   {
                       if(product != "")
                       {
                           returnProduct(product,'<?php echo url_for("emprunt/ajaxReturn")?>');
                           $("#produitRetour").val("");
                       }
                   }
            })
        })

        $('#produit').bind('keypress', function(e)
        {
              if (e.keyCode == '13')
              {
                 var barcode = $("#produit").val();
                 var nomUser = $("#uidUser").val();
                 if(barcode != "" && nomUser != "")
                    searchProduct(barcode,nomUser,'<?php echo url_for("emprunt/ajax")?>','<?php echo url_for("emprunt/ajaxReferant")?>','<?php echo url_for("emprunt/ajaxRemoveProduct")?>');
                 else
                 {
                    jAlert("Veuillez attendre qu'une personne se connecte svp");
                    document.getElementById('produit').focus();
                 }
              }
         })

         $(function(){
              $('input.dateRetour').live('click', function() {
                   $(this).datepicker({
                       showOn: "focus",
                       monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
                       dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                       firstDay: '1',
                       dateFormat: 'dd-mm-yy',
                       onSelect: function(dateText, inst){
                          var date =  $(this).val();
                          var idProduct = $(this).parent().parent().attr("id");
                          dateRetour[cptDate] = new Array();
                          dateRetour[cptDate][0] = idProduct;
                          dateRetour[cptDate][1] = date;
                          cptDate++;
                        }
                   }).focus();
               });
         });

         $('#autocomplete_nom').bind('keypress', function(e) {
          if (e.keyCode == '13')
          {
             var nom = $("input[name=nom]").val();
             if(nom != "")
               loadBasketUser(nom,'<?php echo url_for("emprunt/ajaxLoadBasketUser")?>','<?php echo url_for("emprunt/ajaxRemoveProduct")?>');
             else
               jAlert("Veuillez indiquer un nom svp.");
             $("#autocomplete_nom").val("");
             $("input[name=nom]").val("");
             $("#nomEtudiant").val(nom);
             document.getElementById('produit').focus();
          }
         })


         $('#mdp').click(function(){
             var nom = $("input[name=nom]").val();
             if(nom != "")
               loadBasketUser(nom,'<?php echo url_for("emprunt/ajaxLoadBasketUser")?>','<?php echo url_for("emprunt/ajaxRemoveProduct")?>');
             else
               jAlert("Veuillez indiquer un nom svp.");
             $("#autocomplete_nom").val("");
             $("input[name=nom]").val("");
             $("nom").val("");
             $("#nomEtudiant").val(nom);
             document.getElementById('produit').focus();
         })


         $("#validate").click(function(){
            validate(dateRetour,'<?php echo url_for("emprunt/ajaxValidate")?>');
         })
   });
</script>
<?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne();
?>
<style type="text/css">
    .ui-widget-header
    {
       background-color: <?php echo $file->getColorMessage() ?>;
    }
</style>

<iframe id="iframe" src="<?php echo $_SERVER['REQUEST_URI']?>" frameborder=1 style="visibility: hidden;" width="0" height="0"></iframe>

<?php
if(!OptionIpQuery::exists($_SERVER["REMOTE_ADDR"]))
        echo "<p>Vous n'avez pas le droit d'accéder à cette page</p>";
    else
    {
    ?>
    <div id="header" style="background-color: <?php echo $file->getMenuColor(); ?>">
        <h1>Emprunt</h1>

        <?php echo link_to(image_tag('uploads/'.$file->getLogo(), array('alt' => 'Logo', 'id' => 'logo')), 'http://'.$file->getUrl()) ?>
        <div id="test"></div>

        <input type="submit" value="Retour produit" name="retour" id="retour">

        <div id="menuEmprunt">
            <p><a href="<?php echo url_for('stat/index') ?>">Voir statistiques</a></p>
            <p><a href="<?php echo url_for('backend.php/main') ?>">Espace admin</a></p>
            <p><a href="http://pretz.univ-avignon.fr/authentication/logout?redirect=http://pretz.univ-avignon.fr/emprunt">Déconnexion</a></p>
        </div>
    </div>
      <div id="presentation">
          <u>Gestionnaire</u> : <b><span id="admin"><?php echo $sf_user->getProfileVar('displayname');?></span></b>
      </div>

      <div id="columnLeft">
          <div id="liste" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Liste emprunteurs</div>
            <table id="listeAttente"></table>
          </div>

        <div id="oublie" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">L'utilisateur a oublié son mdp ?</div>
            <center>
                <?php
                      use_helper("uapvForm");
                      echo uapvLdapAutocompleteInput("nom", "name", "uid","cn", "affectation", "uapv_form_extra_input", 20, "Nom de la personne",uapvFormExtraUtils::$PERSONNELS);
                ?><br />
                <input type="submit" value="Enregistrer" id="mdp" name="mdp">
            </center>
        </div>

        <div id="intervenant" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Emprunt intervenant/invité</div>
                <div id="validateInvite">
                 <?php
                        echo "Nom et prénom de l'intervenant : <br />";
                        $name = new sfWidgetFormInput();
                        echo $name->render('name')."<br /><br />";

                        echo "Nom du service : <br />";
                        $service = new sfWidgetFormPropelChoice(array(
                          'model' => 'Service',
                          'order_by' => array('Name','asc'),
                    ));
                    echo $service->render('service')."<br /><br />";
                ?>
                    <input type="submit" value="Enregistrer" id="inter_valid" name="inter_valid">
                </div>
            </div>
        </div>

      <div id="columnRight">
          <div id="emprunt" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
                <p id="titleEmprunt" class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <button id="validate">Terminer</button>
                    Produits en cours d'emprunt</p>
                <table id="listeEmprunt"></table>
                <div id="barcode">
                    <?php
                      echo "Entrez le code-barres du produit : <br /><br />";
                      $product = new sfWidgetFormInput();
                      echo $product->render('produit');
                    ?>
                </div>
          </div>
      </div>

    <div id="referantEnter" title="Attention! Un référant est nécessaire pour ce produit !">
       <p>
        <?php
              use_helper("uapvForm");
              echo uapvLdapAutocompleteInput("nomReferant", "name", "uid","cn", "affectation", "uapv_form_extra_input", 20, "Nom du referant",uapvFormExtraUtils::$PERSONNELS);

              $student = new sfWidgetFormInputHidden();
              echo $student->render('nomEtudiant');

              $uidUser = new sfWidgetFormInputHidden();
              echo $uidUser->render('uidUser');

              $idProduct = new sfWidgetFormInputHidden();
              echo $idProduct->render('idProduct');
              ?>
         </p>
    </div>

   <div id="referantEnterBox" style="display:none;" title="Responsabilité d'un personnel">
       <p>
        <?php
              echo "Nom du référant : <br />";
              use_helper("uapvForm");
              echo uapvLdapAutocompleteInput("nomReferantBox", "name", "uid","cn", "affectation", "uapv_form_extra_input", 20, "Nom du referant",uapvFormExtraUtils::$PERSONNELS);
              ?>
         </p>
    </div>

    <div id="info" title="Informations">
        <div id="infoPerso" style="padding-top:20px;">
        </div>
    </div>

    <div  style="display:none;" id="listeRetour">
        <table style="width: 200px;" id="listeProductReturn">
            <caption>
                <b>Produit(s) rendu(s)</b><br />
            </caption>
        </table>
        <?php
          echo "<br /><br />Code-barres du produit : <br />";
          $product = new sfWidgetFormInput();
          echo $product->render('produitRetour');
        ?>
    </div>
<?php } ?>
