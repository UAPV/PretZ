<?php

/**
 * @synopsis : usefull functions
 * @author : Romain Deveaud
 */

class uapvFormExtraUtils
{
    /** @var $PERSONNELS groupPerson */
    public static $PERSONNELS = "personnels";
    /** @var $ADMINISTRATIFS groupPerson */
    public static $ADMINISTRATIFS = "administratifs";
    /** @var $ENSEIGNANTS groupPerson */
    public static $ENSEIGNANTS = "enseignants";
    /** @var $ETUDIANTS groupPerson */
    public static $ETUDIANTS = "etudiants";
    /** @var $ANCIENS_ETUDIANTS groupPerson */
    public static $ANCIENS_ETUDIANTS = "anciens-etudiants";
    /** @var $TOUS groupPerson */
    public static $TOUS = "tous";


    /**
     * TODO: when PHP >= 5.3.0 will be used, rewrite this function
     *       with __callStatic.
     *
     * Get all the LDAP entries the parameter attribute.
     *
     * @param $attr  string
     * @param $query string
     * @param $groupPersonn string
     *
     * @return array
     */
    static public function getLdapEntriesBy($attr, $query, $returnedAttr = "uid", $groupPerson= "personnels" )
    {
        $attr = strtolower($attr) ;
        $varTranslation = sfConfig::get ('app_profile_var_translation', array ());
        if (! array_key_exists ($attr, $varTranslation))
            throw new sfException('No profile_var_translation attribute : '.$attr) ;

        // We need to use uapvAuthPlugin here...
        $ldap = new uapvLdap() ;

        // filter de groupe :
        if ($groupPerson == uapvFormExtraUtils::$TOUS )
            $filter = "(".$varTranslation [$attr]."=$query*)";
        else
        {
            switch ($groupPerson)
            {
                case "enseignants" : $filter = "edupersonaffiliation=faculty";
                    break;
                case "administratifs" : $filter = "edupersonaffiliation=employee";
                    break;
                case "etudiants" : $filter = "edupersonaffiliation=student";
                    break;
                case "anciens-etudiants" : $filter = "edupersonaffiliation=student";
                    break;
                case "tous" : $filter ="";
                    break;
                default : $filter = "|(edupersonaffiliation=faculty)(edupersonaffiliation=employee)";  // personnels
            }
            $filter = "(&(".$varTranslation [$attr]."=$query*)($filter))";
        }
        
        return self::formatUsers($returnedAttr, $ldap->search($filter, 50) );
    }

    /**
     * TODO: write a generic function !
     *
     * Formats the LDAP search results into something usefull.
     * @param $returnedAttr  string
     * @param $users array
     *
     * @return array
     */
    static public function formatUsers($returnedAttr, $users)
    {
        // initialisation du tableau retourné
        $result = array() ;

        // acquisition des variables définies dans la configuration (app.yml)
        // ce sont celle qui seront prise en compte par la completion
        $varTranslation = sfConfig::get ('app_profile_var_translation', array ());
        foreach($users as $user)
        {
            $uid = $user [$varTranslation [$returnedAttr]];
            $result [$uid] = array ();
            foreach ($varTranslation as $to => $from)
            {
                if (!empty($user [$from]))
                    $result [$uid] [$to] = $user [$from];
                else
                    $result [$uid] [$to] = "";
            }
        }
        asort($result) ;
        return $result ;
    }
}
