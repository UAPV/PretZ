<?php

/**
 * Description of uapvWebDebugPanelUser
 *
 * @author didrya
 */
class uapvWebDebugPanelUser extends sfWebDebugPanel
{

  /**
   * Retourne le titre du Panel tel qu'il sera affiché dans la barre de debug
   *
   * @return string
   */
  public function getTitle ()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
    $user = sfContext::getInstance()->getUser();
    return '<img src="'.public_path('uapvAuthPlugin/images/icons/user.png')
          .'" alt="Informations de l\'utilisateur" height="16" width="16" /> '
          .($user->isAuthenticated () ?
            $user->getProfileVar ('fullname', 'Unknown?') : 'Not logged in!');
  }

  /**
   * Retourne le titre du Panel
   *
   * @return string
   */
  public function getPanelTitle ()
  {
    return 'Informations utilisateur';
  }

  /**
   * Retourne le contenu du Panel
   *
   * @return string
   */
  public function getPanelContent ()
  {
    $user = sfContext::getInstance()->getUser ();

    if (! $user->isAuthenticated () || is_null($user->getProfile()))
      return;

    // Affichage des credentials
    /* @var $user sfBasicSecurityUser */
    $content = "<h2>Credentials :</h2>\n<ul>";
    foreach ($user->getCredentials() as $value) {
      $content .= '<li>- '.$value.'</li>';
    }
    $content .= "</ul>\n";

    // Affichage des informations du profil
    $content .= "<h2>Profil :</h2>\n<table>";
    foreach ($user->getProfile()->getAll () as $key => $value) {
      $content .= '<tr><th scope="row">'.$key.'</th><td>'.$value.'</td></tr>';
    }
    $content .= "</table>\n";

    // Formulaire de simulation de profil
    if ($user->canSimulateProfile ())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag'));
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Form'));
      $content .= "<h2>Se connecter en tant que :</h2>\n"
        .'<form method="post" action="'.url_for ('@uapvAuthProfileSimulate').'">'
        .label_for ('simulated_uid', 'Identifiant de l\'utilisateur à simuler : ')
        .input_tag ('simulated_uid')
        .submit_tag('Simuler ce profil')
        .'</form>';
    }

    // Liens pour annuler la simulation
    if ($user->isSimulating ())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag'));
      $content .= "<h2>Mode simulation :</h2>\n"
        .link_to ('Restaurer l\'ancien profil ('.$user->getAttribute('simUid').')',
         '@uapvAuthProfileRestore', array ('method' => 'post'));
    }

    return $content.'<br /><br />';
  }

  /**
   * Cette fonction sert à attacher ce panel à la barre de debug. Elle sera
   * automatiquement appelée lors du chargement du plugin.
   * 
   * @param sfEvent $event
   */
  public static function loadPanel(sfEvent $event)
  {
    $event->getSubject()->setPanel ('user', new self ($event->getSubject ()));
  }
}
