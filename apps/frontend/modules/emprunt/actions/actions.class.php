<?php

/**
 * emprunt actions.
 *
 * @package    pretz
 * @subpackage emprunt
 * @author     Your name here
 */
class empruntActions extends gzActions
{
  public function executeSecure(sfWebRequest $request)
  {
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->Lends = LendPeer::doSelect(new Criteria());
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->Lend = LendPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404Unless($this->Lend);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new LendForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new LendForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($Lend = LendPeer::retrieveByPk($request->getParameter('id')), sprintf('Object Lend does not exist (%s).', $request->getParameter('id')));
    $this->form = new LendForm($Lend);
  }

  public function executeAccueil(sfWebRequest $request)
  {
    $this->setTemplate('accueil');
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($Lend = LendPeer::retrieveByPk($request->getParameter('id')), sprintf('Object Lend does not exist (%s).', $request->getParameter('id')));
    $this->form = new LendForm($Lend);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($Lend = LendPeer::retrieveByPk($request->getParameter('id')), sprintf('Object Lend does not exist (%s).', $request->getParameter('id')));
    $Lend->delete();

    $this->redirect('emprunt/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $Lend = $form->save();

      $this->redirect('emprunt/edit?id='.$Lend->getId());
    }
  }

  /*
   * Allow to find the product thanks the barcode and save it in the database.
   */
  public function executeAjax(sfWebRequest $request)
  {
       $nomUser = $request->getParameter('nomUser');
       $barcodeRecup = $request->getParameter('barcode');

       $barcode = strtolower($barcodeRecup);
       $respons['barcode'] = $barcode;

      // we search the id of the admin
      $nameAdmin = AdminQuery::create()
        ->filterByName($this->getUser()->getProfileVar('displayname'))
        ->findOne();
      $idAdmin = $nameAdmin->getId();

      //we take the barcode and search what product is this one
      $product = ProductQuery::create()
      ->filterByBarcode($barcode)
      ->findOne();

      //we search the user who wants to borrow
      $user = UserQuery::create()
            ->filterByUid($nomUser)
            ->findOne();
      $ldap = new uapvLdap();
      $this->getContext()->set('ldap', $ldap);

      //this user doesn't exist in the database
      if(count($user) == 0) $respons['test'] = false;

      // the product doesn't exist
      else if(count($product) == 0) $respons['error'] = true;

      //the product is already borrow
      else if($product->getState() == '1') $respons['state'] = '1';

      //if everything is correct
      else
      {
          $respons['referant'] = $product->getReferant();
          
          //if the user is a employee he doesn't need a referant
          if($user->getStatus() == 'employee'  || $user->getStatus() == 'faculty' || $user->getStatus() == 'invite')
          {
             if($product->getReferant() == 1)
                $respons['referant'] = 0;
          }

          //we save theses values in our json object
          $respons['name'] = $product->getName();
          $respons['comments'] = $product->getComments();
          $respons['state'] = $product->getState();
          $respons['error'] = false;
          $respons['idProduct'] = $product->getId();
          $respons['user'] = $user->getId();
          $respons['status'] = $user->getStatus();

          //we search the category of the product
          $categ = CategoryQuery::getCategForOneProduct($product->getId());

          //we verify if this user doens't need a referant.
          if($respons['referant'] == 0 )
          {
               //we save the product for this user
               $product->setState('1');
               $product->save();

               //we check if this user has already a lend now to not recreate a new lend
               $lend = LendQuery::create()
                      ->filterByIduser($user->getId())
                      ->filterByState('en cours')
                      ->findOne();

               //if he have no lend, we create a new one
               if(count($lend) == 0)
               {
                  $lend = new Lend();
                  $lend->setIduser($user->getId());
                  $lend->setIdadmin($idAdmin);
                  $lend->setState('en cours');
                  $lend->save();
               }

               $lend_prod = new LendProduct();
               $lend_prod->setIdproduct($product->getId());
               $lend_prod->setIdlend($lend->getId());
               $lend_prod->setState('1');
               $lend_prod->save();

               //we save the connection id
               $connect = ConnectionQuery::create()
                       ->filterByIduser($user->getId())
                       ->findOne();

               $respons["connection"] = $connect->getId();
           }

        }

     return $this->returnJSON($respons);
  }


  /*
   * This function is used for student.
   * If product needs a referant, we aks the name of this one and we search on the list if student is on.
   * */
  public function executeAjaxReferant(sfWebRequest $request)
  {
      //In the first case, we see if he's on a list and then we show a dialog box
      $nameAdmin = AdminQuery::getByName($this->getUser()->getProfileVar('displayname'));
      $idAdmin = $nameAdmin->getId();
      $respons['authorized'] = false;

      // we take name of each personne and the product 
      $nomReferant = $request->getParameter("nomReferant");
      $nomStudent = $request->getParameter("nomEtudiant");
      $idProduct = $request->getParameter("idProduct");

      // we find the id of the user who is the referant and the user
      $idReferant = UserQuery::create()
                ->filterByUid($nomReferant)
                ->findOne();
      $user = UserQuery::create()
                ->filterByUid($nomStudent)
                ->findOne();

      $ldap = new uapvLdap();
      $this->getContext()->set('ldap', $ldap);

       // we find our product
       $product = ProductQuery::create()
       ->filterById($idProduct)
       ->findOne();

       // we verify that the student and the referant exist in the database
       if($user != null && $idReferant != null)
       {
           // we find the student with the name of our parameter for this referant
           $students = StudentUserQuery::create()
                        ->filterByIduser($idReferant->getId())
                        ->useStudentQuery()
                            ->filterByUid($nomStudent)
                        ->endUse()
                        ->findOne();

           $respons['Iduse'] = $idReferant->getId();
           $respons['Idstudent'] = $nomStudent;

           //student is on the list and so can borrow
           if(count($students) > 0)
           {
                $respons['exist'] = true;
                $respons['name'] = $product->getName();
                $respons['comments'] = $product->getcomments();
                $respons['barcode'] = $product->getBarcode();
                $respons['id'] = $idProduct;
                $respons['authorized'] = true;

               //we find our product
               $product = ProductQuery::create()
               ->filterById($idProduct)
               ->findOne();

               //we save the product
               $product->setState('1');
               $product->save();

               //we check if this user has already a lend now to not recreate a new lend
               $lend = LendQuery::create()
                   ->filterByIduser($user->getId())
                   ->filterByState('en cours')
                   ->findOne();

               //if he have no lend, we create a new one
               if(count($lend) == 0)
               {
                  $lend = new Lend();
                  $lend->setIduser($user->getId());
                  $lend->setIdadmin($idAdmin);
                  $lend->setState('en cours');
                  $lend->save();
               }

               $lend_prod = new LendProduct();
               $lend_prod->setIdproduct($product->getId());
               $lend_prod->setIdlend($lend->getId());
               $lend_prod->setState('1');
               $lend_prod->save();
           }

       }
      return $this->returnJSON($respons);
    }

    /*
     * If the student cannot borrow a product, you can lend it yet.
     * Must give a name of a referant and an email will be send to it.
     */
    public function executeAjaxReferantDialog(sfWebRequest $request)
    {
      $nameAdmin = AdminQuery::getByName($this->getUser()->getProfileVar('displayname'));
      $idAdmin = $nameAdmin->getId();

      $nomReferant = $request->getParameter("nomReferant");
      $nomStudent = $request->getParameter("nomEtudiant");
      $idProduct = $request->getParameter("idProduct");

      // we find the id of the user who is the referant
      $idReferant = UserQuery::create()
                ->filterByUid($nomReferant)
                ->findOne();
      $user = UserQuery::create()
                ->filterByUid($nomStudent)
                ->findOne();

      $ldap = new uapvLdap();
      $this->getContext()->set('ldap', $ldap);

      //we find our product
      $product = ProductQuery::create()
       ->filterById($idProduct)
       ->findOne();

      //we save it
      $product->setState('1');
      $product->save();

      $respons['exist'] = true;   // this student can borrow for this teacher
      $respons['name'] = $product->getName();
      $respons['comments'] = $product->getcomments();
      $respons['barcode'] = $product->getBarcode();
      $respons['id'] = $idProduct;


       // if the referant doens't exist in the database, we create him
       if(count($idReferant) == 0)
       {
           //we create the user
           $idReferant = new User();
           $idReferant->setName(uapvProfileFactory::find($nomReferant)->get('cn'));
           $idReferant->setUid(uapvProfileFactory::find($nomReferant)->get('uid'));
           $idReferant->setEmail(uapvProfileFactory::find($nomReferant)->get('mail'));
           $idReferant->setStatus(uapvProfileFactory::find($nomReferant)->get('edupersonaffiliation'));
           $idReferant->save();
       }

       // if the user doesn't exist in the database, we create him
       if(count($user) == 0)
       {
           //we create the user
           $user = new User();
           $user->setName($user->getName());
           $user->setUid($nomStudent);
           $user->setReferant($idReferant->getId());
           $user->setIdlevel('1');
           $user->save();
       }

       $lend = LendQuery::create()
                ->filterByIduser($user->getId())
                ->findOne();

       if($lend == null)
       {
            $lend = new Lend();
            $lend->setIduser($user->getId());
            $lend->setIdadmin($idAdmin);
            $lend->setState('en cours');
            $lend->save();
       }

       $lend_prod = new LendProduct();
       $lend_prod->setIdproduct($product->getId());
       $lend_prod->setIdlend($lend->getId());
       $lend_prod->save();

       $con = ConnectionQuery::create()
               ->filterByIduser($user->getId())
               ->findOne();


       $respons["connection"] = $con->getId();

      // we send an email to the referant
      $this->sendEmailUser($idReferant, 'email',array('user' => $user));

    return $this->returnJSON($respons);
    }

  /*
   * Allow to validate the basket of the user.
   * Update state of the product to finish.
   * Keep date of return of it's specified.
   *
   * If the user has a status 'invite', we send an email to the service.
   */
  public function executeAjaxValidate(sfWebRequest $request)
  {
      $uid = $request->getParameter('uid');
      $dateRetour = $request->getParameter('dateRetour');
      $tab = $request->getParameter('tab');

      // we find the user who is borrowing
      $user = UserQuery::create()
      	->filterByUid($uid)
      	->findOne();

      //we find the name of the service
      $serv = ServiceQuery::create()
                ->filterById($user->getIdService())
                ->findOne();

      //we find his lend 
      $lend = LendQuery::create()
        ->filterByUserBorrowing($user->getId())
        ->findOne();

      // if a lend exist for this user
      if(count($lend) > 0)
      {
          // we update status of the lend
          $lend->setState('fini');
          $lend->setRetour('1');
          $lend->save();

          // if a date of return was specified for a product anad save it in the database
          if(isset($dateRetour))
          {
              $i=0;
              $j=0;
              foreach($dateRetour as $param)
              {
                $idProd = substr($param[0],3,3);
                $respons[$i]['idProd'] = $idProd;
                $respons[$i]['date'] = $dateRetour;
                $i++;

                $product = ProductQuery::create()
                            ->filterById($idProd)
                            ->findOne();
                $product->setRetourPrevu($param['1']);
                $product->save();
              }

              foreach($tab as $param)
              {
                $idProd = substr($param[0],3,3);
                $respons[$j]['idProd'] = $idProd;
                $respons[$j]['date'] = $dateRetour;
                $j++;

                $product = ProductQuery::create()
                            ->filterById($idProd)
                            ->findOne();
                $product->setRetourPrevu($param['1']);
                $product->save();
              }
          }

          // we destroy the connection because the user doesn't wait anymore
          $con = ConnectionQuery::create()
            ->filterByIduser($user->getId())
            ->findOne();
          $con->delete();

          // if the user was invited, we must send an email to the service
          if($user->getStatus() == "invite")
          {
              // we show if the last option fo automail is activated
              $option = OptionQuery::create()
                        ->orderById('desc')
                        ->findOne();

              // we send an email to the service
              if($option->getAutomail() == 1)
              {
                 $this->sendEmailService($serv, 'email',array('user' => $user));
              }
          }

          $respons['error'] = false;
      }
      else
          $respons['error'] = true;

      return $this->returnJSON($respons);
  }

  /*
   * Auto method to load the page every X seconds.
   * This function verify if a new connection was created since the last refresh
   */
  public function executeAjaxRefresh()
  {
      // we take all connection which are waiting
      $cons = ConnectionQuery::create()
                ->filterByWaiting('1')
                ->find();

      // if this is a new connection
      if(count($cons) > 0)
      {
          $i = 0;
          foreach($cons as $con)
          {
               $name = UserQuery::create()
                    ->filterById($con->getIduser())
                    ->findOne();

               $respons['name'][$i] = $name->getName();
               $respons['uid'][$i] = $name->getUid();
               $respons['nbJokers'][$i] = $name->getNbjokers();
               $con->setWaiting('0');
               $con->save();

               $i++;
               $respons['error'] = false;
          }
          $respons['taille'] = $i;
      }
      else
      {
          $respons['error'] = true;
      }

      return $this->returnJSON($respons);
  }

  /*
   * Load basket of the user if he has got products not validated.
   */
  public function executeAjaxLoadBasketUser(sfWebRequest $request)
  {
      $uidUser = $request->getParameter('uidUser');

      $user = UserQuery::create()
        ->filterByUid($uidUser)
        ->findOne();

      $ldap = new uapvLdap();
      $this->getContext()->set('ldap', $ldap);

      if($user == null)
      {
         $user = new User();
         $user->setName(uapvProfileFactory::find ($uidUser)->get('cn'));
         $user->setEmail(uapvProfileFactory::find ($uidUser)->get('mail'));
         $user->setUid($uidUser);
         $user->setStatus(uapvProfileFactory::find ($uidUser)->get('edupersonaffiliation'));
         $user->save();
      }

      $respons['nom'] = $user->getName();

      $con = ConnectionQuery::create()
        ->filterByIduser($user->getId())
        ->findOne();

      // if this user had been added with a joker (login and password forgotten)
      if(count($con) == 0)
      {
         $con = new Connection();
         $con->setIduser($user->getId());
         $con->setWaiting('1');
         $con->setOrder("1");
         $con->save();

         $nbJock = $user->getNbjokers()+1;
         $user->setNbjokers($nbJock);
         $user->save();

         $respons['new'] = true;
      }
      else
          $respons['new'] = false;

       // we verify if this user has already products in a lend which wasn't finished
       $lend = LendQuery::create()
                ->filterByIduser($user->getId())
                ->filterByState('en cours')
                ->findOne();

      if(count($lend) == 0)
      {
          $respons['empty'] = true;
      }
      else
      {
          // we take all products to display them in his basket
          $respons['empty'] = false;
          $respons['connection'] = $con->getId();

          $productLend = LendProductQuery::create()
                      ->filterByIdlend($lend->getId())
                      ->find();

          $i = 0;
          foreach($productLend as $product)
          {
             $prod = ProductQuery::create()
                ->filterById($product->getIdproduct())
                ->findOne();

             $respons['product'][$i]['name'] = $prod->getName();
             $respons['product'][$i]['id'] = $prod->getId();
             $respons['product'][$i]['description'] = $prod->getDescription();
             $respons['product'][$i]['brand'] = $prod->getBrand();
             $respons['product'][$i]['comments'] = $prod->getComments();
             $i++;
          }
      }
      return $this->returnJSON($respons);
  }

  /*
   * Remove a user.
   * Remove connection of the user because he doesn't wait anymore
   * Remove all products which were in his basket
   */
  public function executeAjaxRemoveUser(sfWebRequest $request)
  {
      $uid = $request->getParameter('uid');

      $user = UserQuery::create()
            ->filterByUid($uid)
            ->findOne();

      //destroy his connexion
      $con = ConnectionQuery::create()
            ->filterByIduser($user->getId())
            ->findOne();
       $con->delete();

      //destroy his basket and put state of the product to '0'
      $basket = LendQuery::create()
        ->filterByIduser($user->getId())
        ->filterByState("en cours")
        ->findOne();

      if(count($basket) > 0)
      {
          $prodLend = LendProductQuery::create()
                ->filterByIdLend($basket->getId())
                ->find();

          foreach($prodLend as $prod)
          {
              $product = ProductQuery::create()
                        ->filterById($prod->getIdproduct())
                        ->findOne();

              if(count($product) > 0)
              {
                $product->setState('0');
                $product->save();
              }

           $prod->delete();
          }

          $basket->delete();
      }

      $respons["error"] = false;

      return $this->returnJSON($respons);
  }

    /*
     * Remove a product of a lend
     */
    public function executeAjaxRemoveProduct(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        // we search the product of the database and put its state into '0'
        $product = ProductQuery::create()
            ->filterById($id)
            ->findOne();
        $product->setState('0');
        $product->save();

        $productLends = LendProductQuery::create()
                    ->filterByIdProduct($id)
                    ->find();

        // if the lend contained one product we destroy the lend
        foreach($productLends as $productLend)
        {
            $lendPr = LendQuery::create()
                   ->filterById($productLend->getIdLend())
                   ->find();

            if(count($lendPr) == 1)
            {
                $lendPr->delete();
            }
            $productLend->delete();
        }

        $respons['error'] = false;

	return $this->returnJSON($respons);
   }

  /*
   * Load information about the user who is lending
   */
  public function executeAjaxLoadUser(sfWebRequest $request)
  {
     $uid = $request->getParameter('nom');

     $user = UserQuery::create()
        ->filterByUid($uid)
        ->findOne();

      $ldap = new uapvLdap();
      $this->getContext()->set('ldap', $ldap);

     // if it's an invite we don't have information.
     if(strpos($uid,"invite") === false)
     {
         $respons['uid'] = uapvProfileFactory::find ($user->getUid())->get('uid');
         $respons['displayname'] = uapvProfileFactory::find ($user->getUid())->get('displayname');
         $respons['edupersonaffiliation'] = uapvProfileFactory::find ($user->getUid())->get('edupersonaffiliation');
         $respons['mail'] = uapvProfileFactory::find ($user->getUid())->get('mail');
         $respons['nbJoker'] = $user->getNbjokers();
         $respons['invite'] = false;
     }
     else
     {
         $respons['displayname'] = $user->getName();
         $respons['invite'] = true;
     }

     return $this->returnJSON($respons);
  }

  /*
   *  Create a user who doesn't have a login and password 
   */
  public function executeAjaxIntervenant(sfWebRequest $request)
  {
  	$name = $request->getParameter('name');
  	$serviceId = $request->getParameter('service');

  	$user = UserQuery::create()
  		->filterByName($name)
  		->filterByStatus('invite')
  		->findOne();

    $service = ServiceQuery::create()
            ->filterById($serviceId)
            ->findOne();

	$nbInvite = UserQuery::create()
		->filterByStatus('invite')
		->find();

    if(count($service) == 0)
        $respons['error'] = true;

    // if the user doesn't exist, we create him
  	if(count($user) == 0)
  	{
  		$nb = count($nbInvite)+1;
  		$user = new User();
  		$user->setUid('invite'.$nb);
  		$user->setName($name);
        $user->setIdservice($service->getId());
  		$user->setStatus('invite');
  		$user->save();
  	}
      
  	if(ConnectionPeer::getLastOrder() != null)
            $maxOrder = ConnectionPeer::getLastOrder()->getOrder();
         else
            $maxOrder = 0;

  	$con = new Connection();
  	$con->setIduser($user->getId());
  	$con->setOrder($maxOrder+1);
  	$con->save();

  	$respons['error'] = false;

  	return $this->returnJSON($respons);
  }

  /*
   * To return a product in the database
   */
  public function executeAjaxReturn(sfWebRequest $request)
  {
      $barcode = $request->getParameter('product');

      $respons['name'] = "inconnu";
      $product = ProductQuery::create()
        ->filterByBarcode($barcode)
        ->findOne();

      if(count($product) == 0)
      {
          $respons['error'] = true;
      }
      else
      {
          // we search the lend Product which contains this product
          $ourlendproduct = LendProductQuery::create()
              ->filterByIdProduct($product->getId())
              ->filterByState('1')
              ->findOne();
          if(count($ourlendproduct) == 0)
          {
             // if the lend doesn't exist and product is to '1'
             if($product->getState() == "1")
             {
                 $product->setState('0');
                 $product->setRetourPrevu(NULL);
                 $product->save();

                 $respons['error'] = false;
                 $respons['name'] = $product->getName();
                 $respons['already'] = false;
             }
             else
                 $respons['already'] = true;
          }
          else
          {
              // we search the lend
              $lend = LendQuery::create()
                    ->filterById($ourlendproduct->getIdLend())
                    ->findOne();

              if(count($lend) > 0)
              {
                  // the product is already returned
                  if($product->getState() == '0')
                  {
                      $respons['already'] = true;
                      $respons['error'] = false;
                      $respons['lend'] = 1;
                  }
                  else
                  {
                      // we check all product for this lend to verify if all products were returned to finish the lend
                      $lendPros = LendProductQuery::create()
                              ->filterByIdLend($ourlendproduct->getIdLend())
                              ->find();

                      $product->setState('0');
                      $product->setRetourPrevu(NULL);
                      $product->save();

                      $ourlendproduct->setDateRetour(new DateTime());
                      $ourlendproduct->setState('0');
                      $ourlendproduct->save();

                      // if one product of this lend has a status '1', the lend is not finished
                      $stop = false;
                      foreach($lendPros as $lendPro)
                      {
                          if($lendPro->getState() == '1')
                          {
                            $stop = true;
                            break;
                          }
                      }

                      // if all products are ok, we finish the lend
                      if(!$stop)
                      {
                          $lend->setRetour('0');
                          $lend->setRetourDate(new DateTime());
                          $lend->save();
                      }
                      $respons['error'] = false;
                      $respons['name'] = $product->getName();
                      $respons['already'] = false;
                  }
              }
              else
              {
                  if($product->getState() == '1')
                  {
                    $product->setState('0');
                    $product->setRetourPrevu(NULL);
                    $product->save();

                    $respons['error'] = false;
                    $respons['name'] = $product->getName();
                    $respons['already'] = false;
                  }
                  else
                  {
                      $respons['already'] = true;
                      $respons['error'] = false;
                  }
              }
          }
      }

      return $this->returnJSON($respons);
  }

  /*
   * Check the library card and check user's rights to access to this page
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
              if($tab == null)
              {
                  $numberCarte = substr($id,6);
                  $tab = $ldap->search('supannempid='.$numberCarte);
              }
          }

          if($tab != null)
          {
              $i=0;
              $respons['credit'] = false;
              $this->getUser()->signIn($tab[0]['uid']);

              foreach($this->getUser()->getCredentials() as $credit)
              {
                  $respons['cred'][$i] = $credit;
                  $i++;
                  if($credit == 'admin' || $credit == 'superadmin')
                  {
                      $respons['ldap'] = $tab;
                      $respons['error'] = false;
                      $respons['credit'] = true;
                  }
              }
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
