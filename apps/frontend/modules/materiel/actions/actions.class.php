<?php

/**
 * materiel actions.
 *
 * @package    pretz
 * @subpackage materiel
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class materielActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $defaultCateg = count(ProductQuery::getProducts('1'));
    if($defaultCateg == 0)
        $this->allCategs = CategoryQuery::create()->where('id != "1"')->orderByName('asc')->find();
    else
        $this->allCategs = CategoryQuery::create()->orderByName('asc')->find();
    $this->nbCateg = count($this->allCategs);
    $this->allProducts = ProductQuery::create()->find();
    //$this->forward('default', 'module');
  }
}
