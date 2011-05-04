<?php

/**
  * TODO: doc
  *
  * Add your own search actions for databases, whatever...
  */

class autocompleteActions extends sfActions
{
  /**
    * Action called by the uapvWidgetFormLdapAutocompleter for a ldap
    * search.
    */
  public function executeLdap (sfWebRequest $request)
  {
    $query = $request->getParameter('q') ;
    $attr  = $request->getParameter('attr') ;

    $returnedAttr = $request->getParameter('returned_attr', 'uid') ;
    $groupe= $request->getParameter('groupe');

    $data = array ();
    if(strlen($query) >= sfConfig::get('app_uapv_form_extra_autocomplete_query_length', 5))
      $data = uapvFormExtraUtils::getLdapEntriesBy($attr,$query, $returnedAttr, $groupe);

    return $this->returnJSON ($data);
  }

  /**
   * Autocomplétion d'un enseignement
   *
   */
  public function executeAppigEnseignement (sfWebRequest $request)
  {
    $codeUE = strtoupper($request->getParameter('q'));
    $limit = $request->getParameter('limit', 40);
    $data = array();

    if(strlen($codeUE) < sfConfig::get('app_uapv_form_extra_autocomplete_query_length', 5))
      return sfView::NONE;

    $enseignements = EnseignementQuery::create()
      ->filterByAn ($this->getUser()->getSelectedYear())
      ->filterByCodeApprox ($codeUE)
      ->limit ($limit)
      ->find ();

    foreach ($enseignements as $enseignement)
    {
      $data [$enseignement->getCode ()] = $enseignement->toArray();
      $data [$enseignement->getCode ()]['NomComplet'] = $enseignement->getCode ().' - '.$enseignement->getLibLong ();
      $data [$enseignement->getCode ()]['Code'] = $enseignement->getCode ();
    }

    return $this->returnJSON ($data);
  }
  
  /**
    * Action called by the uapvWidgetFormAppigAutocompleter for a appig
    * search.
    */
  public function executeAppigPers (sfWebRequest $request)
  {
    $query = strtoupper($request->getParameter('q'));
    $limit = $request->getParameter('limit', 30);

    if(strlen($query) < sfConfig::get('app_uapv_form_extra_autocomplete_query_length', 5))
      return sfView::NONE;

    $c = new Criteria ();
    $c->add(PersonnelPeer::NOM, $query.'%', Criteria::ILIKE);
    $c->setLimit($limit);

    $data = array ();
    foreach (PersonnelPeer::doSelectJoinComposante ($c) as $pers)
    {
      $data [$pers->getPers()] = $pers->toArray(); // TODO : limiter les attributs envoyés;
      $data [$pers->getPers()] ['NomComplet'] = $pers->getPrenom ().' '.$pers->getNom ();
      $data [$pers->getPers()] ['Composante'] = ($pers->getComposanteId () !== null ?
              $pers->getComposante ()->getLibelle () : '');
    }

    return $this->returnJSON ($data);
  }

  /**
   * Return in JSON when requested via AJAX or as plain text when requested directly in debug mode
   *
   */
  public function returnJSON($data)
  {
    $json = json_encode($data);
    $conf = $this->getContext()->getConfiguration();

    if ($conf->isDebug () && !$this->getRequest()->isXmlHttpRequest())
    {
      if (method_exists ($conf, 'loadHelpers'))
        $conf->loadHelpers (array ('Partial')); // only for sf >= 1.2
      else
        sfLoader::loadHelpers (array ('Partial')); // deprecated since sf 1.3
      
      $json = get_partial('jsonLayout', array('data' => $data));
    }
    else
    {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    return $this->renderText($json);
  }
}
