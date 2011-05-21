<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->get('/')->info('Page d’accueil, redirection vers le CAS')->
click('#login')->with('request')->begin()->
  isParameter('module', 'utilisateur')->
  isParameter('action', 'edit')->
  isParameter('id', ‘1’)->
end()
;

$browser->get('/')->info('Page d’accueil, ouverture de la boite de dialogue')->
$browser->info('L’accueil avec le scanne')-> with('response')->
  checkElement('.ui-dialog', true)->
;
