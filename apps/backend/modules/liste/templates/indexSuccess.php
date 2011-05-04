<script type="text/javascript">
    $(document).ready(function(){

        $("#valider").click(function(){
            addList('<?php echo url_for("liste/ajaxAdd") ?>');
        });

        $("#supprimer").click(function(){
            deleteList('<?php echo url_for("liste/ajaxDelete") ?>');
        });
    });

</script>

<style type="text/css ">
    #boutons
    {
        margin-left: 45px;
    }

    #supprimer
    {
        margin-left:15px;
        padding-left:10px;
        padding-right: 10px;
        margin-right:25px;
    }

    #valider
    {
        padding-left:10px;
        padding-right:10px;
    }
</style>

<h1>Liste d'étudiants</h1>

<div id="liste" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
     <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">Selectionnez le personnel concerné</div>
     <p style="color:#a9a9a9;margin-top:5px;margin-left:50px;"><i>Tapez les premières lettres du nom de famille</i></p>
     <p>
         <?php
                  use_helper("uapvForm");
                  echo "Nom du référant : ";
                  echo uapvLdapAutocompleteInput("nameR", "name", "uid","cn", "affectation", "uapv_form_extra_input", 25, "Nom de la personne")."<br /><br /><br />";
                  echo "Nom de l'étudiant : ";
                  echo uapvLdapAutocompleteInput("nameS", "name", "uid","cn", "affectation", "uapv_form_extra_input", 25, "Nom de la personne","etudiants")."<br /><br />";
         ?>
         <span id="boutons">
             <input type="submit" id="supprimer" name="supprimer" value="Supprimer sa liste" />
             <input type="submit" id="valider" name="valider" value="Enregistrer" />
         </span>
     </p>
</div>