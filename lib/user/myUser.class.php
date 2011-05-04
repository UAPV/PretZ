<?php

class myUser extends uapvBasicSecurityUser
{
    public function __toString()
    {
        return $this->getName();
    }

     public function configure()
     {
        $this->setCredentials();
     }

     /* Identification des droits d'administration de l'utilisateur courant
     * @return void
     */
    private function setCredentials()
    {
        $gods_initialized = false;

        // chemin vers le fichier de configuration des administrateurs
        $file_path = sfConfig::get('sf_upload_dir')."/god.yml";

        // On verifie le contenu du fichier de conf
        if (file_exists($file_path))
            if (count ($this->adminsList= sfYaml::load($file_path)) >0 )
                $gods_initialized = true;

        // Si les administrateurs ne sont pas initialisés
        if (!$gods_initialized)
        {
            //construction du fichier par défaut des administrateurs
            $adminsList_arr['admins_list'] = array(
                    "admins" => array("marcelf"),
                    "superadmins" => array("marcelf")
            );
            file_put_contents($file_path, sfYaml::dump($adminsList_arr));
        }
        $adminsList= sfYaml::load($file_path);

        if (array_search($this->getProfileVar("uid"), $adminsList['admins_list']['superadmins'])!== false)
        {
            $this->addCredential("superadmin");
        }
        if (array_search($this->getProfileVar("uid"), $adminsList['admins_list']['admins'])!== false)
        {
            $this->addCredential("admin");
        }
    }
}
?>