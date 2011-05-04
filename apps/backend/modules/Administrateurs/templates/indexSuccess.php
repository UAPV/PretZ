<?php use_helper("uapvForm"); ?>
<?php //use_helper("Form"); ?>
<?php use_stylesheet("redmond/jquery-ui-1.8.2.custom.css"); ?>

<script type="text/javascript" language="Javascript">
    $(document).ready(function() {

        // Ouverture de la fenetre d'ajout d'utilisateurs

        $(".ajouterAdmin").click(function(){
            var myform = $("#"+$(this).attr("title")+"_form");            
            $("#"+$(this).attr("title")).dialog({
                modal: true,
                width:400,
                height: 240,
                show: 'slide',
                hide: 'drop',
                resizable:false,
                buttons: {
                    Fermer: function() {                  
                        $(this).dialog('close');
                    }
                }
            });
        });

        // Fonctions de retour AJAX

        function showRemoveResponse(responseText, statusText, xhr, $form){
            $("#"+responseText.uid+"_"+responseText.typeAdmin+"_tr").fadeOut(); $("#"+responseText.uid+"_"+responseText.typeAdmin+"_tr").slideUp();
            setTimeout(function (){
                $("#"+responseText.uid+"_"+responseText.typeAdmin+"_tr").remove();
            } ,2000);
        };

        function showAddResponse(responseText, statusText, xhr, $form){
            // Identifiant de laboite de dialog a afficher
            if( responseText.typeAdmin== 'superadmins') var dialogId = 'choisir_sa'; else var dialogId= 'choisir_a';
            if (responseText.error)
            {
                // En cas d'erreur affichage du message d'erreur
                var errorString="<div id='msg' class='notif error' style='margin-top:10px'>"+responseText.error+"</div>";
                $('#'+dialogId).append(errorString);
                $('#msg').fadeIn();
                // On cache le message a la fin du delai et on reinitialise des champs de saisie
                $('#autocomplete_uid'+responseText.typeAdmin).val("");                
                setTimeout(function (){
                    $('#msg').fadeOut();$('#msg').remove();
                    $('#uid'+responseText.typeAdmin).val("");
                } ,5000);
            }
            else
            {
                // Ajout d'un nouvelle element dans la liste des administrateurs
                $("#"+responseText.typeAdmin+"_tr").after(responseText.addText);
                if (responseText.confirm)
                {
                    //Affichage du message de confirmation
                    var msgString="<div id='msg' class='notif note' style='margin-top:10px'>"+responseText.confirm+"</div>";
                    $('#'+dialogId).append(msgString);
                    $('#msg').fadeIn();
                    // On cache le message a la fin du delai et on reinitialise des champs de saisie
                    $('#autocomplete_uid'+responseText.typeAdmin).val("");
                    setTimeout(function (){
                        $('#msg').fadeOut();$('#msg').remove();
                        $('#uid'+responseText.typeAdmin).val("");                        
                    } ,5000);
                }

                // Au initialise la validation ajax                
                $('#'+responseText.uid+"_"+responseText.typeAdmin+'_form').ajaxForm({
                    success:    showRemoveResponse,   // post-submit callback
                    dataType:  "json"
                });
            }
        };

        // Initialisation des formulaire AJAX

        $('.addForm').ajaxForm({
            success:    showAddResponse,   // post-submit callback
            dataType:  "json"
        });

        $('.removeForm').ajaxForm({
            success:    showRemoveResponse,   // post-submit callback
            dataType:  "json"
        });

    });
</script>

<h1 class="alinea">Administrateurs de la page d'accueil</h1>

<div style="margin-left: 20px;width:300px">
<div  class="notif note">IMPORTANT! Les utilisateurs doivent se reconnecter pour que les changements soient appliqués </div>
</div>

<!-- dialogs -->

<div id="choisir_sa" class="hideMe" title="Ajouter un administrateur">
    <form id="choisir_sa_form" class="addForm" action="Administrateurs/add" method="post">
        Nom : <?php echo uapvLdapAutocompleteInput("uidsuperadmins"); ?> <input type="submit" value="Ajouter"/>
        <input type="hidden" name='typeAdmin' value='superadmins' />
    </form>    
</div>
<div id="choisir_a" class="hideMe" title="Ajouter un gestionnaire">
    <form id="choisir_a_form" class="addForm" action="Administrateurs/add" method="post">
        Nom : <?php echo uapvLdapAutocompleteInput("uidadmins"); ?> <input type="submit" value="Ajouter"/>
        <input type="hidden" name='typeAdmin' value='admins' />
    </form>    
</div>

<!-- liste des admins -->
<div id ='listeAdmins-div'>
    <table id="admins_table">
        <?php if (count($adminsList['admins_list']['superadmins'])>0) : ?>
        <tr id="superadmins_tr"><th colspan="2" ><br/>Administrateurs<h5> <i>(Accès à toutes les pages, modification et suppression autorisées)</i></h5></th></tr>

            <?php foreach($adminsList['admins_list']['superadmins'] as $admin) : ?>
        <tr id="<?php echo $admin ?>_superadmins_tr">

            <td><?php echo uapvProfileFactory::find($admin)->get('cn'); ?></td>
            <td class="delete">
                <form class="removeForm" action="Administrateurs/remove" method="post">
                            <input type="hidden" name="uid" value="<?php echo $admin; ?>"/>
                            <input type="hidden" name="typeAdmin" value="superadmins" />
                            <input type="image" src="<?php echo image_path("notifications/delete.png"); ?>" />
                </form>
            </td>
        </tr>
            <?php endforeach; ?>

        <tr><td class="add" colspan="2">   
                <input type="button" value="Ajouter un administrateur" id="ajouterSuperadmin" title="choisir_sa" class="ajouterAdmin"/>
            </td></tr>
        <?php endif; ?>
        <tr id="admins_tr"><th colspan="2"><br/>Gestionnaires <h5><i>(Accès à la page d'emprunts et aux statistiques)</i></h5></th></tr>
        <?php foreach($adminsList['admins_list']['admins'] as $admin) : ?>
        <tr id="<?php echo $admin ?>_admins_tr">
            <td><?php echo uapvProfileFactory::find($admin)->get('cn'); ?></td>
            <td class="delete">
                <form class="removeForm" action="Administrateurs/remove" method="post">
                        <input type="hidden" name='uid' value="<?php echo  $admin; ?>"/>
                        <input type="hidden" name='typeAdmin'  value="admins" />
                        <input type="image" src="<?php echo image_path("notifications/delete.png"); ?>" />
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr><td class="add" colspan="2">   
                <input type="button" value="Ajouter un gestionnaire" id="ajouterAdmin" class="ajouterAdmin" title="choisir_a"/>
            </td></tr>
    </table>

</div>
