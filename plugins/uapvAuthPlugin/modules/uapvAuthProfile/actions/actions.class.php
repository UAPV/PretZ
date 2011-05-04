<?php

/**
 * Controleur permettant de simuler des profils utilisateurs
 */
class uapvAuthProfileActions extends sfActions
{
  public function executeSimulate (sfWebRequest $request)
  {
    $this->forward404Unless ($request->isMethod ('post'));

    $user = $this->getUser ();
    if ($user->canSimulateProfile () && $request->hasParameter ('simulated_uid'))
    {
      $user->simulateProfile ($request->getParameter ('simulated_uid'));
    }
    $this->redirect ($request->getReferer ()); // rechargement de la page
  }

  public function executeRestore (sfWebRequest $request)
  {
    $this->forward404Unless ($request->isMethod ('post'));
    $this->getUser()->restoreProfile ();
    $this->redirect ($request->getReferer ()); // rechargement de la page
  }

}
