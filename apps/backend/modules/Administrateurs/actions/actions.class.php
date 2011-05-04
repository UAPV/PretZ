<?php

/**
 * Fonctions actions.
 *
 * @package    accueil-ent
 * @subpackage Administrateurs
 * @author     Ellioh Thot'o
 */
class AdministrateursActions extends sfActions
{
    public function executeIndex($request)
    {
        // prevention de la simulation d'une superadmin
        if ($this->getUser()->isSimulating()) $this->forward404(); 

        $isSuperAdmin = false;
        if ($this->getUser()->hasCredential('superadmin'))
            $isSuperAdmin = true;

        // chemin vers le fichier de configuration des administrateurs
        $file_path = sfConfig::get('sf_upload_dir')."/god.yml";

        $gods_initialized = false;

        // On verifie le contenu du fichier de conf
        if (file_exists($file_path))
            if (count ($this->adminsList= sfYaml::load($file_path)) >0 )
                $gods_initialized = true;

        // Si les administrateurs ne sont pas initialisés
        if (!$gods_initialized)
        {
            //construction du fichier par défaut des administrateurs (équipe dev)
            $adminsList_arr['admins_list'] = array(
                    "admins" => array("marcelf"),
                    "superadmins" => array("marcelf")
            );
            file_put_contents($file_path, sfYaml::dump($adminsList_arr));
            $this->adminsList= sfYaml::load($file_path);

        }
        if(!$isSuperAdmin)
            $this->adminsList['admins_list']['superadmins'] = array();

    }

    public function executeAdd($request)
    {
        // prevention de la simulation d'une superadmin
        if ($this->getUser()->isSimulating()) $this->forward404(); 
        $file_path = sfConfig::get('sf_upload_dir')."/god.yml";

        // Acqusiition parametres saisis
        $typeAdmin = $request->getParameter('typeAdmin');
        $uid =  $request->getParameter('uid'.$typeAdmin);

        if (!empty($uid))
        {
            $adminsList= sfYaml::load($file_path);
            // test existance de l'utilisateur
            $profile =uapvProfileFactory::find($uid);

            if (empty($profile))
                return $this->renderText(json_encode( array("typeAdmin"  => $typeAdmin,"error" =>  "Désolé, l'utilisateur n'existe pas")));
            else
            {
                if (array_search($uid, $adminsList['admins_list'][$typeAdmin])== false)
                {
                    array_push($adminsList['admins_list'][$typeAdmin], $uid);
                    file_put_contents($file_path, sfYaml::dump($adminsList));

                    //put in database
                    $name = uapvProfileFactory::find ($uid)->get('displayname');
                    $admin = new Admin();
                    $admin->setName($name);
                    if($typeAdmin == 'superadmins')
                        $admin->setIdlevel('11');
                    else
                        $admin->setIdlevel('1');
                    $admin->setActive('1');
                    $admin->save();

                    return $this->renderText(json_encode( array(
                            "uid"  => $uid,
                            "confirm"  => "Ajout effectué!",
                            "typeAdmin"  => $typeAdmin,
                            "addText" => "<tr id=".$uid."_".$typeAdmin."_tr><td>". uapvProfileFactory::find($uid)->get('cn')."</td>
                    <td class='delete'>
                        <form id='".$uid."_".$typeAdmin."_form' class='removeForm' action='Administrateurs/remove' method='post'>".
                                    "<input type='hidden' name='uid' value=$uid />".
                                    "<input type='hidden' name='typeAdmin' value=$typeAdmin />".
                                    "<input type='image' src='../images/notifications/delete.png'  />".
                                    "</form>".
                                    "</td>".
                                    "<tr>")));
                }
                else
                    return $this->renderText(json_encode( array("typeAdmin"  => $typeAdmin,"error" => "Cette personne est déjà administrateur!")));
            }
        }
        else
            return $this->renderText(json_encode( array("typeAdmin"  => $typeAdmin,"error" =>  "Merci de saisir un nom valide!")));
    }

    public function executeRemove($request)
    {
        // prevention de la simulation d'une superadmin
        if ($this->getUser()->isSimulating()) $this->forward404(); 
        // Acquisition des parametres
        $uid =  $request->getParameter('uid');
        $typeAdmin=  $request->getParameter('typeAdmin');
        if (empty ($uid))
            throw new sfException('var $uid not found') ;
        if (empty ($typeAdmin))
            throw new sfException('var $typeAdmin not found') ;

        // test existance de l'utilisateur
        $profile =uapvProfileFactory::find($uid);
        if (empty($profile))
            return $this->renderText(json_encode( array("typeAdmin"  => $typeAdmin, "error" =>"Désolé, l'utilisateur n'existe pas")));
        else
        {
            $name = uapvProfileFactory::find ($uid)->get('displayname');
            
            //delete in database
            $admin = AdminQuery::create()
                ->filterByName($name)
                ->findOne();
            if(count($admin) > 0)
            {
                $admin->setActive('0');
                $admin->save();
            }
                    
            // Suppression de l'administrateur
            $file_path = sfConfig::get('sf_upload_dir')."/god.yml";
            $adminsList= sfYaml::load($file_path);
            $key = array_search($uid, $adminsList['admins_list'][$typeAdmin]);
            unset($adminsList['admins_list'][$typeAdmin][$key]);
            file_put_contents($file_path, sfYaml::dump($adminsList));
            return $this->renderText(json_encode( array("typeAdmin"  => $typeAdmin, "uid" => $uid)));
        }
    }
}
