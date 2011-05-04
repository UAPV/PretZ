<script type="text/javascript">
    $(document).ready(function(){
        var initSeconde = 5;
        var x;
        refresh();
        decompte();

      function init()
      {
        $.ajax({
           type: 'POST',
           url: '<?php echo url_for("utilisateur/ajaxInit")?>',
           dataType: 'json',
           data: {},
           success: function(data)
           {
             if(data != null)
               setTimeout(refresh,1000*60);
           }
         })
      }

      function refresh()
      {
         $.ajax({
           type: 'POST',
           url: '<?php echo url_for("utilisateur/ajaxRefresh")?>',
           dataType: 'json',
           data: {},
           success: function(data)
           {}
         })
      }

      function logout()
      {
         $(location).attr('href','<?php echo url_for("authentication/logout")?>');
      }

      function decompte()
      {
        if(initSeconde >= 1)
        {
            $("#chrono").html(initSeconde);
            initSeconde-- ;
            setTimeout(decompte, 1000) ;
        }
        else
           logout();
      }
            });
</script>

<?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne(); ?>

<style type="text/css">
    .ui-widget-header
    {
        background-color: <?php echo $file->getColorMessage() ?>;
    }
</style>

 <div id="attente" class="ui-dialog ui-widget ui-widget-content ui-corner-all relative ">
        <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix titreDiv">Mise en attente ...</div>
        <div class="messAttente">
            Bonjour <b><?php echo  $sf_user->getProfileVar('displayname'); ?></b>, <br />
           <p id="messAttente">Votre demande de prêt va être prise en compte, merci de patienter svp ...</p>
           <center><p id="mess"> PS : vous allez être mis en attente dans <span style="font-weight:bold;font-size:1.2em;" id="chrono"></span></p></center>
           <?php echo image_tag("/images/horloge.png", array('id' => 'horloge')); ?>
        </div>
</div>
