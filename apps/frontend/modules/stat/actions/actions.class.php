<?php

/**
 * stat actions.
 *
 * @package    pretz
 * @subpackage stat
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

class statActions extends gzActions
{

  public function executeIndex(sfWebRequest $request)
  {}

  public function executeProduit(sfWebRequest $request)
  {}

  public function executeCateg(sfWebRequest $request)
  {}

  /* display products for one date (parameter) */
  public function executeAjax(sfWebRequest $request)
  {
      $dateRecup = $request->getParameter('date');

      //we take the day, month and year of dates
      $day = substr($dateRecup,8,2);
      $month = substr($dateRecup,5,2);
      $year = substr($dateRecup,0,4);

      $respons['mois'] = $month;
      $respons['jour'] = $day;
      $respons['annee'] = $year;

      //we put this values in our table
      for($i=0;$i<5;$i++)
      {
        $newDate = mktime(0,0,0,$month,($day+$i),$year);
        $nbLend = LendQuery::create()
          ->filterByDay($newDate)
          ->find();
        $respons[$i] = count($nbLend);
      }

      //we put the number of product foreach category for this week
      $nbCateg = count(CategoryQuery::getAllCateg());
      $compteur = 1;
      $respons['pie']= "[";
      $respons['pie2']= "[";
      $haveAProduct2 = false;

      foreach(CategoryQuery::getAllCateg() as $categ)
      {
          $nb = LendQuery::countLendByCateg($categ->getId());
          $nbProd = ProductQuery::getProductLendedByCateg($categ->getId());

          if($nbProd != 0)
          {
              $haveAProduct2 = true;
              $respons['pie2'] .= "['".$categ->getName()."',".$nbProd."]";
          }

          if($nb != 0)
              $respons['pie'] .= "['".$categ->getName()."',".$nb."]";
          
          if($compteur != $nbCateg)
          {
              if($nb != 0)
                $respons['pie'] .= ", ";
              if($nbProd != 0)
                $respons['pie2'] .= ", ";
          }
          $compteur++;
      }
      $respons['pie'] .= "]";
      $respons['pie2'] .= "]";
      
      if(!$haveAProduct2)
          $respons['pie2'] = false;

      return $this->returnJSON($respons);
  }

  /* display one product for one month */
  public function executeAjaxMois(sfWebRequest $request)
  {
      $mois = $request->getParameter('mois');
      $id = $request->getParameter('id');

      $year = date('Y');

      $name = ProductQuery::create()
            -> filterById($id)
            ->findOne();
      
      //we put this values in the table for each day in the month
      for($i=0;$i<31;$i++)
      {
        $newDate = mktime(0,0,0,$mois,($i+1),$year);
        $nbLend = LendQuery::create()
          ->filterByDayAndProduct($newDate,$id)
          ->find();
        $respons[$i] = count($nbLend);
      }
      $respons['name'] = $name->getName();
      
      return $this->returnJSON($respons);
  }

  /* display products for one year */
  public function executeAjaxAnnee(sfWebRequest $request)
  {
      $year = $request->getParameter('annee');
      $id = $request->getParameter('id');

      $name = ProductQuery::create()
            -> filterById($id)
            ->findOne();

      //we put this values in the table
      for($i=0;$i<12;$i++)
      {
        //list of the products for each month
        $listProduct = LendQuery::create()
          ->filterByMonthAndProduct($i+1,$year,$id)
          ->find();
        $respons[$i] = count($listProduct);
      }
      $respons['name'] = $name->getName();

      return $this->returnJSON($respons);
  }

  /* display a line for one categ for one week */
  public function executeAjaxOneCateg(sfWebRequest $request)
  {
      $dateRecup = $request->getParameter('date');
      $id = $request->getParameter('id');

      //we take the day, month and year of dates
      $dayBegin = substr($dateRecup,0,2);

      $year = date('Y');
      $month = date('m');

      $name = ProductQuery::create()
            -> filterById($id)
            ->findOne();

      //we put this values in our table
      for($i=0;$i<5;$i++)
      {
        $nbLend = LendQuery::create()
          ->filterByDayAndProduct($dayBegin,$month,$year,$id)
          ->find();
        $respons[$i] = count($nbLend);
        $dayBegin++;
      }
      $respons['name'] = $name->getName();


      return $this->returnJSON($respons);
  }

  /* display a line for one product for one week */
  public function executeAjaxOneProduct(sfWebRequest $request)
  {
      $dateRecup = $request->getParameter('date');
      $id = $request->getParameter('id');

      $name = ProductQuery::create()
            -> filterById($id)
            ->findOne();

      //we take the day, month and year of dates
      $day = substr($dateRecup,0,2);
      $month = substr($dateRecup,3,2);
      $year = substr($dateRecup,6,8);


      //we put this values in our table
      for($i=0;$i<5;$i++)
      {
        $newDate = mktime(0,0,0,$month,($day+$i),$year);
        $nbLend = LendQuery::create()
          ->filterByDayAndProduct($newDate,$id)
          ->find();
        $respons[$i] = count($nbLend);
      }
      $respons['name'] = $name->getName();


      return $this->returnJSON($respons);
  }

  /* display a line for one categ for one week */
  public function executeAjaxWeekCateg(sfWebRequest $request)
  {
      $dateRecup = $request->getParameter('date');
      $id = $request->getParameter('id');

      //we take the day, month and year of dates
      $day = substr($dateRecup,0,2);
      $month = substr($dateRecup,3,2);
      $year = substr($dateRecup,6,8);

      $name = CategoryQuery::create()
            -> filterById($id)
            ->findOne();

      //we put this values in the table for each day in the month
      for($i=0;$i<5;$i++)
      {
         $newDate = mktime(0,0,0,$month,($day+$i),$year);
         $nbLend = LendQuery::create()
          ->filterByDayAndCateg($newDate,$id)
          ->find();
         $respons[$i] = count($nbLend);
         $respons['total'][$i] = ProductCategQuery::nbProductOneCateg($id,$newDate);
      }
      $respons['name'] = $name->getName();

      return $this->returnJSON($respons);
  }
  
  /* display a line for one categ for one month */
  public function executeAjaxMoisCateg(sfWebRequest $request)
  {
      $mois = $request->getParameter('mois');
      $id = $request->getParameter('id');

      $year = date('Y');

      $name = CategoryQuery::create()
            -> filterById($id)
            ->findOne();
      
      $tabJours31 = array('1','3','5','7','8','10','12');

      if(in_array($mois,$tabJours31))
          $max = 31;
      else
          $max = 30;
      
      //we put this values in the table for each day in the month
      for($i=0;$i<$max;$i++)
      {
        $newDate = mktime(0,0,0,$mois,($i+1),$year);
        $prods = LendQuery::create()
                ->filterByDayAndCateg($newDate,$id)
                ->find();
        $respons[$i] = count($prods);
        $respons['total'][$i] = ProductCategQuery::nbProductOneCateg($id,$newDate);
      }
      $respons['name'] = $name->getName();

      return $this->returnJSON($respons);
  }

  /* display a line for one product for one year */
  public function executeAjaxAnneeCateg(sfWebRequest $request)
  {
      $year = $request->getParameter('annee');
      $id = $request->getParameter('id');

      $name = CategoryQuery::create()
            -> filterById($id)
            ->findOne();

      //we put this values in the table
      for($i=0;$i<12;$i++)
      {
        //list of the products for each month
        $listProduct = LendQuery::create()
          ->filterByMonthAndCateg($i+1,$year,$id)
          ->find();
        $respons[$i] = count($listProduct);
        $newDate = mktime(0,0,0,$i+1,0,$year);
        $respons['total'][$i] = ProductCategQuery::nbProductOneCateg($id,$newDate);
      }
      $respons['name'] = $name->getName();

      return $this->returnJSON($respons);
  }

  /*
   * Verify if a user has too many jokers
   */
  public function executeAjaxrefreshJoker()
  {
      $i = 0;
      $users = UserQuery::create()
        ->find();

      $respons['nobody'] = true;
      if(count($users) > 0)
      {
          $respons['error'] = false;
          foreach ($users as $user)
          {
              if($user->getNbjokers() > 4)
              {
                  $respons['nobody'] = false;
                  $respons['name'][$i] = $user->getName();
                  $respons['nbjocker'][$i] = $user->getNbjokers();
                  $respons['uid'][$i] = $user->getUid();
                  $i++;
              }
          }
      }
      else
           $respons['error'] = true;

      return $this->returnJSON($respons);
  }

  /*
   * Verify if a category has products which aren't lent
   */
  public function executeAjaxrefreshProduct()
  {
      $i = 0;
      $categs = CategoryQuery::create()
        ->find();

      if(count($categs) > 0)
      {
          $respons['error'] = false;
          foreach($categs as $categ)
          {
              $nb = 0;
              $idCateg = $categ->getId();
              $pro = ProductCategQuery::create()
                ->filterByIdcateg ($idCateg)
                ->find();
              $total = count($pro);

              $products = ProductQuery::create()
                ->useProductCategQuery()
                      ->filterByIdcateg($idCateg)
                ->endUse()
                ->find();

              foreach($products as $product)
              {
                  if($product->getState() == '1')
                       $nb++;
              }

              if(($total-1) <= $nb)
              {
                $respons['name'][$i] = $categ->getName();
                $i++;
              }
          }
      }
      else
           $respons['error'] = true;

      return $this->returnJSON($respons);
  }

  /*
   * Pager object to paginate LendProduct for historic.
   */
  /*public function executeHistorique(sfWebRequest $request)
  {
      $pager = new sfPropelPager('LendProduct',10);
      $c = new Criteria();
      $c->addDescendingOrderByColumn(LendProductPeer::ID);
      $pager->setCriteria($c);
      $pager->setPeerMethod('doSelectJoinLend');
      $pager->setPeerMethod('doSelectJoinUser');
      $pager->setPeerMethod('doSelectJoinProduct');
      $pager->setPage($this->getRequestParameter('page', 1));
      $pager->init();
      $this->lend_pager = $pager;
  }

  /*
   * Pager object to paginate LendProduct  
   */
  /*public function executeSortie(sfWebRequest $request)
  {
      $pager = new sfPropelPager('LendProduct',10);
      $c = new Criteria();
      $c->addDescendingOrderByColumn(LendProductPeer::ID);
      $pager->setCriteria($c);
      $pager->setPeerMethod('doSelectJoinLend');
      $pager->setPeerMethod('doSelectJoinUser');
      $pager->setPeerMethod('doSelectJoinProduct');
      $pager->setPage($this->getRequestParameter('page', 1));
      $pager->init();
      $this->lend_pager = $pager;
  }*/

public function executeHistorique(sfWebRequest $request)
  {
      $pager = new sfPropelPager('Lend',10);
      $c = new Criteria();
      $c->addDescendingOrderByColumn(LendPeer::ID);
      $pager->setCriteria($c);
      $pager->setPeerMethod('doSelectJoinUser');
      $pager->setPage($this->getRequestParameter('page', 1));
      $pager->init();
      $this->lend_pager = $pager;
  }

  public function executeSortie(sfWebRequest $request)
  {
      $pager = new sfPropelPager('LendProduct',10);
      $c = new Criteria();
      $c->add(LendProductPeer::STATE,'1');
      $c->addDescendingOrderByColumn(LendProductPeer::ID);
      $pager->setCriteria($c);
      $pager->setPeerMethod('doSelectJoinLend');
      $pager->setPeerMethod('doSelectJoinUser');
      $pager->setPeerMethod('doSelectJoinProduct');
      $pager->setPage($this->getRequestParameter('page', 1));
      $pager->init();
      $this->lend_pager = $pager;
  }
    
}

      

      