<?php

/**
 *
 * TODO Documenter
 *
 */
class uapvBasicSecurityUser extends sfBasicSecurityUser
{
  const SESSION_NAMESPACE = 'uapvBasicSecurityUser';

  /**
   * @var uapvBasicProfile
   */
  protected $profile = null;

  /**
   * @return boolean
   */
  public function isAnonymous ()
  {
    return ! $this->isAuthenticated();
  }

  /**
   * Log user in
   * @param string $login Username
   */
  public function signIn ($login)
  {
    sfContext::getInstance()->getLogger()->log('User "'.$login.'" sign in.');

    // enregistrement dans la session de l'utilisateur
    $this->setAttribute ('login', $login, self::SESSION_NAMESPACE);
    $this->setAuthenticated (true);
    $this->clearCredentials ();

    // récupération du profil
    $profile = uapvProfileFactory::find ($login);
    if ($profile !== null)
      $this->setProfile ($profile);

    // on passe la main à l'application pour qu'elle configure les autorisation
    // de l'utilisateur courant
    if (method_exists ($this, 'configure'))
      $this->configure();
  }

  /**
   * Log user out
   */
  public function signOut()
  {
    $this->getAttributeHolder()->removeNamespace (self::SESSION_NAMESPACE);
    $this->profile = null;
    $this->clearCredentials();
    $this->setAuthenticated(false);
  }

  /**
   * @return uapvBasicProfile
   */
  public function getProfile ()
  {
    if ($this->profile !== null)
      return $this->profile;

    if ($this->hasAttribute ('profile', self::SESSION_NAMESPACE))
      return $this->profile = unserialize ($this->getAttribute ('profile', null, self::SESSION_NAMESPACE));

    return null;
  }

  /**
   * @param $profile uapvBasicProfile
   */
  public function setProfile ($profile)
  {
    $this->profile = $profile;
  }

  /**
   * @return boolean
   */
  public function hasProfile ()
  {
    return ($this->profile !== null || $this->hasAttribute ('profile', self::SESSION_NAMESPACE));
  }

  /**
   * Retourne la valeur d'une donnée de profil
   */
  public function getProfileVar ($name, $default = null)
  {
    if (! $this->hasProfile ())
      return $default;

    return $this->getProfile()->get ($name, $default);
  }

  /**
   * Cette fonction est appelée lorsque la requête a été traitée
   * On serialise a ce moment là le profil afin de le stocker dans la session
   *
   * @see sfBasicSecurityUser.shutdown()
   */
  public function shutdown ()
  {
    if ($this->profile !== null)
      $this->setAttribute ('profile', serialize ($this->profile), self::SESSION_NAMESPACE);

    parent::shutdown ();
  }

  /**
   * Simule un profil en remplacant le profile courant. Ce dernier est sauvegardé en session
   *
   * @param <type> $simulatedUid
   */
  public function simulateProfile ($simulatedUid)
  {
    // on sauvegarde l'uid de l'utilisateur courant
    if (! $this->isSimulating ())
      $this->setAttribute ('simUid', $this->getProfileVar ('uid'));

    // on authentifie l'utilisateur a simulé
    $this->signOut ();
    $this->signIn ($simulatedUid);
  }

  /**
   * Retourne true si l'utilisateur courant est autorisé à simuler le profil d'un
   * autre utilisateur, false sinon.
   *
   * @return boolean
   */
  public function canSimulateProfile ()
  {
    return in_array ($this->getProfileVar ('uid'),
                         (array) sfConfig::get ('app_security_gods', array ()));
  }

  /**
   * Restaure un profil à la suite d'une simulation de profil.
   * L'uid initial est stocké en session dans l'attribut 'simUid'.
   *
   */
  public function restoreProfile ()
  {
    if ($this->isSimulating ())
    {
      // on authentifie l'utilisateur qui a simulé
      $this->signOut ();

      // desactive le mode simulation
      $simUid = $this->getAttribute ('simUid'); 
      $this->getAttributeHolder()->remove ('simUid');

      $this->signIn ($simUid);
    }
  }

  /**
   * Retourne true si mode simulation activé et false sinon
   *
   * @return boolean
   */
  public function isSimulating ()
  {
    return $this->hasAttribute ('simUid');
  }

}
