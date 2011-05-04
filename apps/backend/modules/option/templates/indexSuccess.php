<script type="text/javascript">
    $(document).ready(function(){

        $("#error").hide();
        $("#tableVoirIp tr:even").css("background-color", "#dfeffc");

        if(getUrlVars()['error'] != null)
        {
            if(getUrlVars()['error'] != 'Le+template+a+bien+ete+ajoute')
            {
                $("#error").show();
                error = $("#error").val(urldecode(getUrlVars()['error']));
                if(getUrlVars()['fond'] != '')
                    $("#fond").val(urldecode(getUrlVars()['fond']));
                else
                    $("#fond").css('border','1px solid red');
                if(getUrlVars()['menu'] != '')
                    $("#menu").val(urldecode(getUrlVars()['menu']));
                else
                    $("#menu").css('border','1px solid red');
                if(getUrlVars()['nomT'] != '')
                    $("#nomT").val(urldecode(getUrlVars()['nomT']));
                else
                    $("#nomT").css('border','1px solid red');
                if(getUrlVars()['nomS'] != '')
                    $("#nomS").val(urldecode(getUrlVars()['nomS']));
                if(getUrlVars()['url'] != '')
                    $("#url").val(urldecode(getUrlVars()['url']));
                if(getUrlVars()['mess'] != '')
                    $("#couleurMess").val(urldecode(getUrlVars()['mess']));
                else
                    $("#couleurMess").css('border','1px solid red');

                $("#ajouter" ).dialog({
                        width: 460,
                        modal: false,
                        buttons: {
                             Fermer: function(){
                                 $(this).dialog( "close" );
                             }
                        }
                    })
            }
            else
                jAlert("Le template a correctement été ajouté !");
        }

          $("#listeTemplate").change(function()
          {
              changeTemplate('<?php echo url_for("option/ajaxChangeTemplate") ?>');
          });

          // a décommenter si on veut pouvoir changer l'automail
          /*
          $("#activer").click(function(){
              changeMail('<?php echo url_for("option/ajaxMail") ?>');
          });*/

          $("#voirIp").click(function(){
              listIp();
          });


          // a décommenter si on veut pouvoir ajouter une nouvelle adresse Ip
          /*
          $("#ajoutIp").click(function(){
                addIp('<?php echo url_for("option/ajaxAddIp") ?>');
          });*/

          $("#ajout").click(function(){
              $("#ajouter" ).dialog({
                    width: 510,
                    modal: true,
                    buttons: {
                         Fermer: function(){
                             $(this).dialog( "close" );
                         }
                    }
                })
            });
    });
</script>

<style type="text/css">
    #ajoutIp, #activer
    {
        opacity: 0.4;
        cursor: auto;
    }
</style>

<h1>Liste des options</h1>

<table id="listeOption" border="1">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Activer</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Template</td>
            <td>Vous pouvez ici choisir un template en spécifiant la couleur de l'arrière-plan, la couleur du menu ainsi qu'un logo</td>
            <td>
               <?php
                $pr = new sfWidgetFormPropelChoice(array(
                      'model' => 'Template',
                      'order_by' => array('Active', 'desc')));
                echo $pr->render('listeTemplate');
              ?>
            </td>
            <td><?php echo @image_tag("/images/add.png",array('title' => 'Ajouter', 'alt' => 'Ajouter', 'size' => '100%', 'id' => 'ajout', 'class' => 'survol')); ?></td>
        <tr>
        <tr>
            <td>Mail automatique</td>
            <td>Cette option permet d'envoyer un email automatique lorsqu'une personne vient de la part d'un service.
            Le mail sera donc envoyé à ce service pour l'informer que quelqu'un a prit un produit sous sa responsabilité.</td>
            <td>
                <?php
                  if(OptionQuery::mailIsActive())
			echo "<center>".@image_tag("ok.png",array('title' => 'Activee','alt' => 'Activée'))."</center>";
                  else 
                 	echo "<center>".@image_tag("cross.png",array('title' => 'Desactivée','alt' => 'Desactivée'))."</center>";
                ?>
            </td>
            <td><?php echo @image_tag("/images/change.png",array('title' => 'Activée/Désactivée', 'alt' => 'Activée/Désactivée', 'size' => '100%', 'id' => 'activer', 'class' => 'survol')); ?></td>
        </tr>
        <tr>
            <td>Adresses ip</td>
            <td>Cette option permet de spécifier les adresses ip autorisées pour la console de celui qui emprunte.</td>
            <a href="#" style="cursor: pointer;">
                <td><center><?php echo @image_tag("list.png",array('alt' => 'Voir','id' => 'voirIp','title' => 'Voir les adresses ip', 'class' => 'survol')); ?></center></td>
            </a>
            <td><?php echo @image_tag("/images/add.png",array('title' => 'Ajouter une adresse', 'alt' => 'Ajouter', 'id' => 'ajoutIp', 'class' => 'survol')); ?></td>
        </tr>
    </tbody>
</table>

<form action="option/addTemplate" method="POST" enctype="multipart/form-data" id="ajouter" title="Ajouter un template ">
    <?php
        echo "<b><input type='text' id='error' style='border:none; color:red;width:100%; disabled='disabled' /><br /><br /></b>";
        echo "<table border='1' id='ajouterTableau'><tr>";

        echo "<td class='label'>Nom du template </td>";
        $nom = new sfWidgetFormInput();
        echo "<td>".$nom->render('nomT')."</td></tr>";

        echo "<tr><td class='label'>Nom de la structure</td>";
        $fond = new sfWidgetFormInput();
        echo "<td>".$fond->render('nomS')."</td></tr>";

        echo "<tr><tr><td class='label'>Adresse internet</td> ";
        $menu = new sfWidgetFormInput();
        echo "<td>".$menu->render('url')."</td></tr><tr><td class='help' colspan='2'>".@image_tag('/images/help.png', array('title' => 'Aide', 'alt' => 'Help :'))." Adresse de la forme : www.univ-avignon.fr</td></tr></tr>";

        echo "<tr><td class='label'>Couleur de fond</td>";
        $menu = new sfWidgetFormInput();
        echo "<td>".$menu->render('fond')."</td><tr><td class='help' colspan='2'>".@image_tag("/images/help.png", array('title' => 'Aide', 'alt' => 'Help :'))." Vous pouvez mettre un code couleur</td></tr>";

        echo "<tr><td class='label'>Couleur du menu</td>";
        $menu = new sfWidgetFormInput();
        echo "<td>".$menu->render('menu')."</td><tr><td class='help' colspan='2'>".@image_tag("/images/help.png", array('title' => 'Aide', 'alt' => 'Help :'))." Vous pouvez mettre un code couleur</td></tr>";

        echo "<tr><td class='label'>Couleur des blocs des messages</td>";
        $couleurMess = new sfWidgetFormInput();
        echo "<td>".$couleurMess->render('couleurMess')."</td></tr>";
?>
        <tr><td class='label'>Logo</td>
            <td><input type="file" name="logoUpload" id="logoUpload" /></td>
        </tr>
        <tr>
            <td colspan='2'><input type="submit" value="Enregistrer" class="ui-dialog-buttonset" /></td>
        </tr>
    </table>
</form>

<div id="dAjouterIp" style="display: none;" title="Ajouter une adresse ip">
    <?php
        echo "Adresse ip : ";
        $ip = new sfWidgetFormInput();
        echo "<center>".$ip->render('ip')."</center><br /><br />";
    ?>
</div>

<div id="dVoirIp"  style="display: none;" title="Adresses ip autorisées">
    <table id="tableVoirIp" style="margin-top:10px; margin-left: 10px;width: 90%;">
    <?php
        $ips = OptionIpQuery::create()
            ->find();

        if(count($ips) > 0)
        {
            foreach($ips as $ip)
                echo "<tr><td><center>".$ip->getAddress()."</center></td></tr>";
        }
        else
            echo "<tr><td><center><i>Il n'y a pas d'adresses Ip pour le moment</i></center></td></tr>";
    ?>
    </table>
</div>