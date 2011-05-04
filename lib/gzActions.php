<?php

/**
*

*/
class gzActions extends sfActions
{

  /**
* Return in JSON when requested via AJAX or as plain text when requested directly in debug mode

*
*/
  public function returnJSON($data)
  {
    $json = json_encode($data);

    if (sfContext::getInstance()->getConfiguration()->isDebug () && !$this->getRequest()->isXmlHttpRequest())
    {
      $this->getContext()->getConfiguration()->loadHelpers('Partial');
      $json = get_partial('global/json', array('data' => $data));
    } 
    else
    {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    return $this->renderText($json);
  }


  public function sendEmailUser(User $user, $partial, $vars = array())
  {
    $vars = array_merge (array ('recipient' => $user), $vars);

    $body = $this->getPartial ($partial, $vars).$this->getPartial ('global/email_signature');

    $message = $this->getMailer ()->compose (
      'pretz@univ-avignon.fr', // TODO
      $user->getEmail(),
      '[Pretz] '.html_entity_decode (get_slot ('email_subject'), ENT_QUOTES),
      $body
    )
    ->setContentType ("text/html")
    ->addPart (html_entity_decode (@strip_tags ($body), ENT_QUOTES), 'text/plain');

    $this->getMailer()->send ($message);
  }
  

  public function sendEmailService($user, $partial, $vars = array())
  {
    $vars = array_merge (array ('recipient' => $user), $vars);

    $body = $this->getPartial ($partial, $vars).$this->getPartial ('global/email_signature');

    $message = $this->getMailer ()->compose (
      'pretz@univ-avignon.fr', // TODO
      $user->getEmail(),
      '[Pretz] '.html_entity_decode (get_slot ('email_subject'), ENT_QUOTES),
      $body
    )
    ->setContentType ("text/html")
    ->addPart (html_entity_decode (@strip_tags ($body), ENT_QUOTES), 'text/plain');

    $this->getMailer()->send ($message);
  }

}

