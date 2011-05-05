/**
 * Created by JetBrains PhpStorm.
 * User: fanny
 * Date: 28/04/11
 * Time: 14:45
 * To change this template use File | Settings | File Templates.
 */

/*
    Verify it's user can access to this page and if he has rights.
 */
function loadUserAdmin(id, url)
{
    if(id != "")
    {
       $.ajax({
          type: 'POST',
          url: url,
          dataType: 'json',
          data: { idCarte: id },
          success: function(data)
          {
             if(data != null)
             {
                if(data.error)
                {
                   jAlert("Le code-barres entré ne correspond à aucun personnel de l'université.");
                   $("#carteId").val("");
                   $("#scanDialog").close();
                }
                else if(!data.credit)
                {
                   jAlert("Vous n'avez pas les droits pour accéder à cette page");
                   $(location).attr('href','authentication/logout?redirect=http://pretz.univ-avignon.fr/emprunt');
                }
                else
                {
                   $(location).attr('href','emprunt/accueil');
                }
             }
          }
       });
    }
    else
       jAlert("Impossible de lire le code-barres");
}

/*
    Allow to search a new connection with a new user when anybody want to lend a product.
    When a user is connected, it appears in the list of user on the top left.
    This function do a setTimeOut every 1.5sec. It can be changed.
 */
function refresh(url, urlBasket, urlLoadUser, urlRemoveUser)
{
     $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {},
        success: function(data)
        {
            if(data != null)
            {
              if(!data.error)
              {
                for(i = 0; i < data.name.length; i++)
                {
                    $("#listeAttente").append("<tr id='"+data.uid[i]+"'><td class='userName' onClick=\"changeCss('"+data.uid[i]+"', '"+urlBasket+"', '"+urlRemoveUser+"');\" style='display:block;' >"+data.name[i]+"</td><td id='alert"+data.uid[i]+"'><a href='#' onClick=\"removeUser('"+data.uid[i]+"','"+urlRemoveUser+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer'F /></a><a href='#' onClick=\"loadUser('"+data.uid[i]+"','"+urlLoadUser+"');\"><img src='/images/info.png'  style='border:none;width:30%;' alt='informations' /></a></td></tr>");
                }
                var id = $("#listeAttente tbody tr:first-child").attr('id');
                $("#uidUser").val(id);
                document.getElementById('produit').focus();
              }
            }
        }
     });
     setTimeout("refresh('"+url+"','"+urlBasket+"','"+urlLoadUser+"','"+urlRemoveUser+"')",1500);
}

/*
    Allows to reload the page to keep the connection to the ldap.
 */
function loadIframe()
{
     document.getElementById("iframe").contentDocument.location.reload(true);
     setTimeout(loadIframe,1000*60*10);
}

/*
    Allow to change the css of the list of users.
    The box of the user selected is yellow.
 */
function changeCss(uidUs,url,urlRemoveProduct)
{
    var uid = $("#uidUser").val();
    $("#listeEmprunt tr").remove();

    $("#"+uid).css("background-color","white");
    $("#uidUser").val(uidUs);

    $("#"+uidUs).css("background-color","#F7EAB2");
    loadBasketUser(uidUs,url,urlRemoveProduct);
}

/*
    Load a user who doesn't have any login and password.
    He's saved in the database with his name with status "invite".
 */
function loadIntervenant(url)
{
    var name = $("#name").val();
    var service = $("#service").val();
    if(name == "" || service == "")
    {
        jAlert("Veuillez indiquer un nom et un service svp.");
    }
    else
    {
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            data: {name: name, service: service},
            success: function(data)
            {}
        })
    }
}

/*
    Return a product. This function verify if this one was lent and so save in the database lend associated is finish.
 */
function returnProduct(product,url)
{
    $.ajax({
          type: 'POST',
          url: url,
          dataType: 'json',
          data: {product: product},
          success: function(data)
          {
             if(data.error)
                jAlert("Ce code-barres n'existe pas dans la base de données.");
             else if(data.already)
                jAlert("Ce produit est a déjà été rendu.");
             else if(data.name == null)
                jAlert("Ce produit est inconnu");
             else
                $("#listeProductReturn").append("<tr><td>"+data.name+"</td></tr>");
          }
    });
}

/*
    Load the cart (or basket) of the user to retrieve his products if his lend was not finished.
    This function allows to display the basket in the list of products.
 */
function loadBasketUser(uidUser,url,urlRemoveProduct)
{
    $.ajax({
       type: 'POST',
       url: url,
       dataType: 'json',
       data: {uidUser: uidUser},
       success: function(data)
       {
           if(!data.empty)
           {
              for(x=0; x<data.product.length; x++)
              {
                 if(data.product[x].comments != null)
                     $("#listeEmprunt").append("<tr id='Pro"+data.product[x].id+"'><td><b>"+data.product[x].name+"</b><br /><p style='margin-right:5px;font-size:0.8em; color:#d3d3d3;'><i>"+data.product[x].comments+"</i></p></td><td><a href='#' onClick=\"removeProduct('"+data.product[x].id+"','"+urlRemoveProduct+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer' style='width:30%;' /></a></td><td class='option'><input type='text' class='dateRetour' value='Date retour' id='Input"+data.idProduct+"' /></td></tr>");
                 else
                    $("#listeEmprunt").append("<tr id='Pro"+data.product[x].id+"'><td><b>"+data.product[x].name+"</b><br /></td><td><a href='#' onClick=\"removeProduct('"+data.product[x].id+"','"+urlRemoveProduct+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer' style='width:30%;' /></a></td><td class='option'><input type='text' class='dateRetour' value='Date retour' id='Input"+data.idProduct+"' /></td></tr>");
              }
              $("#validate").show();
           }
           document.getElementById('produit').focus();
       }
    })
}

/*
    Allow to delete a user in the list.
    This function allows to delete his cart if the lend wasn't over.
 */
function removeUser(uid,url)
{
   jConfirm("Voulez-vous vraiment supprimer cette personne ?","Confirmation de la demande d'annulation", function(r)
   {
       if(r)
       {
           $.ajax({
               type: 'POST',
               url: url,
               dataType: 'json',
               data: {uid: uid},
               success: function(data)
               {
                  if(data != null)
                  {
                      if(!data.error)
                      {
                         $("#"+uid).remove();
                         $("#listeEmprunt tr").remove();
                      }
                  }
               }
           })
       }
   });
}

/*
    Allow to remove a product and to continue the lend with the other which were lent before.
 */
function removeProduct(id,url)
{
   $.ajax({
      type: 'POST',
      url: url,
      dataType: 'json',
      data: {id: id},
      success: function(data)
      {
         if(data != null)
         {
            if(!data.error)
                $("#Pro"+id).remove();
         }
      }
   })
}

/*
    Allow to load a user when he's connecting.
    This function loads all information about him.
 */
function loadUser(uid, url)
{
   $.ajax({
       type: 'POST',
       url: url,
       dataType: 'json',
       data: {nom: uid},
       success: function(data)
       {
           $("#dialog:ui-dialog").dialog("destroy");
           $("#info").attr("title","Informations");

           if(!data.invite)
              $("#infoPerso").html("<p><b><u>Numéro : </u></b> "+data.uid+" </p><p><b><u>Nom :</u></b> "+data.displayname+"</p><p><b><u>Statut :</u></b> "+data.edupersonaffiliation+"</p><p><b><u>Mail :</u></b> "+data.mail+"</p><p><b><u>Nombre de jokers utilisés :</u></b> "+data.nbJoker+"</p>");
           else
              $("#infoPerso").html("<p><b><u>Nom : </u></b> "+data.displayname+" </p><br /><br /><center><p><i>Il n'y a aucune information complémentaire sur cette personne.</i></p></center>");

           $("#info").dialog
           ({
              height: 500,
              width: 450,
              modal: false,
              zIndex: 3000,
              buttons:
              {
                  Fermer: function()
                  {
                     $("#info" ).dialog('close')
                  }
              }
           });
           $("#uidUser").val(uid);
       }
   })
}

/*
    Display a dialog textbox to know the name of the referant of the student.
 */
function dialogReferant(url,urlRemoveProduct)
{
   var nomReferant;
   var continuer = false;
   var nomEtudiant = $("#uidUser").val();
   $("#dialog:ui-dialog" ).dialog( "destroy" );
   $("#referantEnter" ).dialog({
      modal: false,
      width: 350,
      zIndex: 3000,
      buttons: {
            Ok: function() {
                nomReferant = $("#nomReferant").val();
                var dialog = $(this);
                if(nomReferant != "")
                {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        dataType: 'json',
                        data: {nomEtudiant: nomEtudiant,nomReferant: nomReferant,idProduct: idProduct},
                        success: function(data)
                        {
                            if(data != null && nomReferant != null && data.authorized)
                            {
                                $("#listeEmprunt").append("<tr id='Pro"+data.id+"'><td><b>"+data.name+"</b><br /><p style='margin-right:5px;font-size:0.8em; color:#d3d3d3;'><i>"+data.comments+"</i></p></td><td class='optionSup'><a href='#' onClick=\"removeProduct('"+data.id+"','"+urlRemoveProduct+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer' style='width:30%;' /></a></td><td class='option'><input type='text' class='dateRetour' id='Input"+data.idProduct+"' value='Date retour' /></td></tr>");
                                $("#"+data.barcode).effect("highlight",{},500);
                                $("#produit").val("");
                                $("#validate").show();
                            }
                            else if(!data.authorized)
                            {
                                $("#nomReferant").val("");
                                jConfirm("Cette personne n'est inscrite sur aucune liste d'emprunt. <br/>Voulez-vous quand même lui prêter du matériel sous la responsabilité de quelqu'un ?","Nécessité d'un référant",
                                  function(r) {
                                    if(r){
                                        continuer = true;
                                        dialog.dialog('close');
                                        dialogReferantBox('<?php echo url_for("emprunt/ajaxReferantDialog")?>',urlRemoveProduct);
                                        return true;
                                    }
                                    else
                                        return false;
                                  });
                            }
                            else
                                jAlert("Cette personne n'est pas autorisée à emprunter du matériel");
                        }
                    });
                    $(this).dialog( "close" );
                }
                else
                    jAlert("Veuillez indiquer un référant SVP");
            },
             Fermer: function(){
                $(this).dialog( "close" );
             }
      }
   });
   //$("#referantEnter").dialog('open');
}

/*
    if the user isn't on a list of a referant, you can lend him a product : you enter the name of a referant (token in the LDAP) and a email is sent to him.
 */
function dialogReferantBox(url, urlRemoveProduct)
{
    var nomEtudiant = $("#uidUser").val();
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $("#referantEnterBox" ).dialog({
        modal: false,
        autoOpen: false,
        width: 350,
        zIndex: 3000,
        buttons: {
            Valider: function() {
                  nomReferant = $("#nomReferantBox").val();
                  if(nomReferant != "")
                  {
                     $.ajax({
                         type: 'POST',
                         url: url,
                         dataType: 'json',
                         data: {nomEtudiant: nomEtudiant,nomReferant: nomReferant,idProduct: idProduct},
                         success: function(data)
                         {
                             if(data.exist)
                             {
                                 $("#listeEmprunt").append("<tr id='Pro"+data.id+"'><td><b>"+data.name+"</b><br /><p style='margin-right:5px;font-size:0.8em; color:#d3d3d3;'><i>"+data.comments+"</i></p></td><td class='optionSup'><a href='#' onClick=\"removeProduct('"+data.id+"','"+urlRemoveProduct+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer' style='width:30%;' /></a></td><td class='option'><input type='text' class='dateRetour' id='Input"+data.idProduct+"' value='Date retour' /></td></tr>");
                                 $("#"+data.barcode).effect("highlight",{},500);
                                 $("#produit").val("");
                                 $("#validate").show();
                                 return true;
                             }
                         }
                     });
                     $(this).dialog('close');
                     return false;
                  }
            },
            Annuler: function(){
                  $(this).dialog( "close" );
            }
        }
    });
   // $("#referantEnterBox").dialog('open');
}

/*
    Search a product in the databace thanks a barcode and verify the availability of it.
    Display the product if it's ok in the list.
 */
function searchProduct(barcode, nomUser, url, urlReferant, urlRemoveProduct)
{
    $.ajax({
       type: 'POST',
       url: url,
       dataType: 'json',
       data: {barcode: barcode,nomUser: nomUser},
       success: function(data){
           if(data != null)
           {
               if(nomUser == "")
               {
                   jAlert("Veuillez attendre qu'un utilisateur se connecte svp");
                   document.getElementById('produit').focus();
               }
               else
               {
                   if(!data.error && data.state == '0')
                   {
                      if(data.referant == '1')
                      {
                          dialogReferant(urlReferant,urlRemoveProduct);
                          idLend = data.idLend;
                          idProduct = data.idProduct
                      }
                      else if(data.comments != null && data.comments != "")
                      {
                          $("#listeEmprunt").append("<tr id='Pro"+data.idProduct+"'><td><b>"+data.name+"</b><br /><p style='margin-right:5px;font-size:0.8em; color:#d3d3d3;'><i>"+data.comments+"</i></p></td><td class='optionSup'><a href='#' onClick=\"removeProduct('"+data.idProduct+"','"+urlRemoveProduct+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer' style='width:30%;' /></a></td><td class='option'><input type='text' class='dateRetour' id='Input"+data.idProduct+"' value='Date retour' /></td></tr>");
                          $("#Pro"+data.idProduct).effect("highlight",{},500);
                          $("#produit").val("");
                          $("#validate").show();
                      }
                      else
                      {
                          $("#listeEmprunt").append("<tr id='Pro"+data.idProduct+"'><td><b>"+data.name+"</b><br /></td><td class='optionSup'><a href='#' onClick=\"removeProduct('"+data.idProduct+"','"+urlRemoveProduct+"');\"><img src='/images/cross.png' style='margin-left:5px;margin-right:5px;border:none;' alt='supprimer' style='width:30%;' /></a></td><td class='option'><input type='text' class='dateRetour' id='Input"+data.idProduct+"' value='Date retour' /></td></tr>");
                          $("#Pro"+data.idProduct).effect("highlight",{},500);
                          $("#produit").val("");
                          $("#validate").show();
                      }
                   }
                   else if(data.state == '1')
                   {
                      $("#produit").val("");
                      jAlert("Ce produit est déjà emprunté. Il n'y a plus d'exemplaires disponibles actuellement.");
                      document.getElementById('produit').focus();
                   }
                   else
                   {
                      jAlert("Le produit "+barcode+" n'existe pas dans la base de données.<br /><br /> Veuillez vérifier le code-barres saisi.");
                      document.getElementById('produit').focus();
                   }
               }
           }
           else
               jAlert('data est nulle');
       }
    });
}

/*
    Validate a lend and save it in the database.
    This function remove the user and these products of lists.
 */
function validate(dateRetour,url)
{
    var i=1;
    var uid = $("#uidUser").val();
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {uid: uid,dateRetour: dateRetour},
        success: function(data)
        {
            if(!data.error)
            {
               document.getElementById('produit').focus();
            }

            $("#listeEmprunt tr").remove();
            $("#"+uid).remove();
            $("#validate").hide();

            if($("#listeAttente").length > 1)
            {
               var id = $("#listeAttente tbody tr:first-child").attr('id');
               $("#uidUser").val(id);
            }
            else
               $("#uidUser").val("");
               setTimeout(location.reload,3*1000);
            }
        });
    document.getElementById('produit').focus();
}
