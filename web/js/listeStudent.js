/**
 * Created by JetBrains PhpStorm.
 * User: fanny
 * Date: 29/04/11
 * Time: 15:01
 * To change this template use File | Settings | File Templates.
 */

/*
 * @param url of the path ajax add
 * Add a student on a list of an employee
 */
function addList(url)
{
    var nomEtudiant = $("#nameS").val();
    var nomReferant = $("#nameR").val();
    if(nomReferant != "" && nomEtudiant != "")
    {
       $.ajax({
           type: 'POST',
           url: url,
           dataType: 'json',
           data: {nomR: nomReferant,nomS: nomEtudiant},
           success: function(data)
           {
                if(data.error)
                     jAlert(nomReferant+" n'est pas autorisé à inscrire un élève");
                else if(data.already)
                     jAlert("L'élève est deja sur la liste de ce référant");
                else
                     jAlert("L'élève "+nomEtudiant+" a bien été ajouté à "+nomReferant);

                $("#nameS").val("");
                $("#autocomplete_nameS").val("");
           }
       })
    }
    else
       jAlert("Veuillez indiquer le nom de l'étudiant ainsi que celui du référant");
}

/*
 * @param url of the path ajax remove
 * Delete the list of an employee
 */
function deleteList(url)
{
     var nomReferant = $("#nameR").val();
     $.ajax({
         type: 'POST',
         url: url,
         dataType: 'json',
         data: {nomR: nomReferant},
         success: function(data)
         {
             if(!data.error)
                 jAlert("La liste de "+nomReferant+" a bien été supprimée");
         }
     })
}

/*
 * Clean all connections 
 */
function clearConnection(url)
{
     $.ajax({
          type: 'POST',
          url: url,
          dataType: 'json',
          data: {},
          success: function(data)
          {
             if(!data.error)
                jAlert("Toutes les connexions ont été supprimées. Veuillez re-essayer la connexion de la personne");
          }
     })
}

/*
 * Change template with an other one
 */
function changeTemplate(url)
{
     var template = $("#listeTemplate").val();
     $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: {template: template},
        success: function(data)
        {
           if(!data.error)
           {
              location.reload();
           }
        }
     });
}

/*
 * Change email option (active or disable)
 */
function changeMail(url)
{
     $.ajax({
         type: 'POST',
         url: url,
         dataType: 'json',
         data: {},
         success: function(data)
         {
           if(!data.error)
           {
              if(data.active)
                 jAlert("L'option a bien été désactivée.");
              else
                 jAlert("L'option a bien été activée.");

              location.reload();
           }
           else
              jAlert("erreur");
         }
     })
}

/*
 * Display list of ip addresses
 */
function listIp()
{
     $("#dVoirIp" ).dialog({
         width: 460,
         modal: true,
         buttons: {
            Fermer: function()
            {
                $(this).dialog( "close" );
            }
         }
     });
}

/*
 * Add an ip address
 */
function addIp(url)
{
     $("#dAjouterIp" ).dialog({
         width: 460,
         modal: true,
         buttons: {
              Ajouter: function()
              {
                  var ip = $("#ip").val();
                  $.ajax({
                      type: 'POST',
                      url: url,
                      dataType: 'json',
                      data: {ip: ip},
                      success: function(data)
                      {
                          if(!data.error)
                          {
                              jAlert("L'adresse a bien été ajoutée");
                              $("#ip").val("");
                          }
                          else
                              jAlert("Cette adresse est déjà dans celles autorisées");
                      }
                  });
              },
              Fermer: function()
              {
                  $(this).dialog("close");
                  location.reload();
              }
         }
     });
}

/*
 * Take the parameters in the URL
 */
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function urldecode (str)
{
    return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}