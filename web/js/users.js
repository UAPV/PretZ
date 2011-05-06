/**
 * Created by JetBrains PhpStorm.
 * User: fanny
 * Date: 28/04/11
 * Time: 11:34
 * To change this template use File | Settings | File Templates.
 */


function redirectUser(id, url)
{
    if(id != "")
    {
       $.ajax({
         type: 'POST',
         url: url,
         dataType: 'json',
         data: {idCarte: id},
         success: function(data)
         {
            if(data != null)
            {
               if(data.error)
               {
                 jAlert("Le code-barres entré ne correspond à aucun personnel de l'université.");
                 $("#carteId").val("");
               }
               else
               {
                 $(location).attr('href','utilisateur/edit');
               }
            }
         }
       });
    }
    else
       jAlert("Impossible de lire le code-barres");
}