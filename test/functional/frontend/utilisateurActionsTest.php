<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  get('/utilisateur/index')->

  with('request')->begin()->
    isParameter('module', 'utilisateur')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body', '!/This is a temporary page/')->
  end()
;

$browser->get('/')->info('Page d’accueil, ouverture de la boite de dialogue')->
$browser->info('L’accueil avec le scanne')-> with('response')->
  checkElement('.ui-dialog', true)->
;
