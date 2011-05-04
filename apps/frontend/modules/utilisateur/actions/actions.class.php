<?php

/**
 * utilisateur actions.
 *
 * @package    pretz
 * @subpackage utilisateur
 * @author     Your name here
 */
class utilisateurActions extends gzActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->Users = UserPeer::doSelect(new Criteria());
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->User = UserPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404Unless($this->User);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new UserForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new UserForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /*
   * Register the user in the database if he's not already registered.
   * Create un new connection if he's user doesn't wait
   * Save informations about this user
   */
  public function executeEdit(sfWebRequest $request)
  {
      //we send this user in the database
      $nameUser = $this->getUser()->getProfileVar('uid');
      $user = UserQuery::create()
        ->filterByUid($nameUser)
        ->findOne();

      if(count($user) == 0)
      {
        $user = new User();
        $user->setName($this->getUser()->getProfileVar('cn'));
        $user->setUid($this->getUser()->getProfileVar('uid'));
        $user->setStatus($this->getUser()->getProfileVar('edupersonaffiliation'));
        $user->setEmail($this->getUser()->getProfileVar('mail'));
        $user->save();
      }

      $alreadyConnected = ConnectionQuery::create()->filterByIdUser($user->getId())->findOne();
      if(count($alreadyConnected) == 0)
      {
          $wait = new Connection();
          $wait->setIduser($user->getId());
          $wait->setWaiting('1');
          $wait->setOrder('1');
          $wait->save();
      }
  }

  /*
   * Set a flash message and wait a new connection
   */
  public function executeAjaxRefresh()
  {
      $this->getUser()->setFlash('notice', sprintf('Mise en attente de '.$this->getUser()->getProfileVar('displayname')));
      $respons['error'] = false;

      return $this->returnJSON($respons);
  }


  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($User = UserPeer::retrieveByPk($request->getParameter('id')), sprintf('Object User does not exist (%s).', $request->getParameter('id')));
    $this->form = new UserForm($User);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($User = UserPeer::retrieveByPk($request->getParameter('id')), sprintf('Object User does not exist (%s).', $request->getParameter('id')));
    $User->delete();

    $this->redirect('utilisateur/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $User = $form->save();

      $this->redirect('utilisateur/edit?id='.$User->getId());
    }
  }

  /*
   * Init PretZ with its template which have got this name
   */
  public function executeAjaxInit(sfWebRequest $request)
  {
      $nom = $request->getParameter('nom');
      
      $name = TemplateQuery::create()
        ->filterByName($nom)
        ->findOne();

      // if this template exists
      if(count($name) > 0)
      {
          $respons['error'] = false;
          $respons['logo'] = $name->getLogo();
          $respons['back'] = $name->getBackColor();
          $respons['menu'] = $name->getMenuColor();
      }
      else
          $respons['error'] = true;

      return $this->returnJSON($respons); 
  }

  /*
   * Verify a library card with its barcode
   * /!\ This function must be changed when it verify if the card exists.
   */
  public function executeCarte(sfWebRequest $request)
  {
      $id = $request->getParameter('idCarte');
      $ldap = new uapvLdap();
      $this->getContext()->set('ldap', $ldap);
      
      if($id[0]=='9')
      {
          if($id[1] == '0')
          {
              $numberCarte = substr($id,2);
              $tab = $ldap->search('uidnumber='.$numberCarte);
          }
          elseif($id[1] == '1')
          {
              $numberCarte = substr($id,5);
              $tab = $ldap->search('supannempid='.$numberCarte);
          }

          if($tab != null)
          {
              $respons['error'] = false;
              $this->getUser()->signIn($tab[0]['uid']) ;
              $this->getUser()->addCredentials('member') ;
              $respons['ldap'] = $tab;
          }
          else
              $respons['error'] = true;
      }
      else
      {
          $respons['error'] = true;
      }

      return $this->returnJSON($respons);
  }

}
