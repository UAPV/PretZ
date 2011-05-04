<style>
    p
    {
        color: #483d8b;
    }

</style>

<?php slot ('email_subject', 'Emprunt à votre nom') ?>
<?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne(); ?>

<div style="background: #FBF8EB; border: 1px solid #EFEBDD; padding: 20px;">
    <div>
        <a href=<?php echo "http://".$file->getUrl(); ?> >
            <img style="border:none; width:84px;" alt="logo" src=<?php echo "http://pretz.univ-avignon.fr/images/uploads/".$file->getLogo(); ?> />
        </a>
    </div>
    <div>
        <p>Bonjour,</p>
        <p>
            Un intervenant extérieur ou un élève ( <?php echo $user->getName() ?> ) a emprunté du materiel en votre nom.<br />
            Merci de contacter le service si cette personne vous est inconnue.<br /><br />
            Si vous ne souhaitez plus recevoir de mail, merci de donner la liste des étudiants pouvant emprunter à votre nom au service audiovisuel.<br /><br />
            Le service audiovisuel.<br />
        </p>
    </div>
</div>